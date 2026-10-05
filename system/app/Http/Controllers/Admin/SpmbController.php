<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Major;
use App\Models\Role;
use App\Models\Setting;
use App\Models\SpmbRegistration;
use App\Models\Student;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SpmbController extends Controller
{
    private function resolveId($encodedId)
    {
        return decode_id($encodedId) ?: (is_numeric($encodedId) ? (int)$encodedId : null);
    }

    public function index(Request $request)
    {
        $query = SpmbRegistration::with(['user', 'wave', 'verifier', 'class', 'major']);
        
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('wave_id') && $request->wave_id !== 'all') {
            $query->where('wave_id', $request->wave_id);
        }
        
        $registrations = $query->latest()->paginate(20)->withQueryString();

        // Fetch waves for filter and quota monitoring
        $waves = Wave::with('academicYear')->orderBy('start_date', 'asc')->get();

        $selectedWaveId = $request->input('wave_id');
        $waveQuery = Wave::with('academicYear')
            ->withCount([
                'spmbRegistrations',
                'spmbRegistrations as paid_registrations_count' => fn($q) => $q->where('payment_status', 'paid'),
                'spmbRegistrations as pending_registrations_count' => fn($q) => $q->where('payment_status', 'pending'),
            ]);

        if ($selectedWaveId && $selectedWaveId !== 'all') {
            $activeWave = (clone $waveQuery)->find($selectedWaveId);
        } else {
            $activeWave = (clone $waveQuery)->where('status', 'active')->first() 
                ?? (clone $waveQuery)->latest()->first();
        }

        $activeWaveUnpaid = $activeWave ? $activeWave->spmbRegistrations()->where('payment_status', 'unpaid')->count() : 0;

        return view('admin.spmb.index', compact('registrations', 'activeWave', 'activeWaveUnpaid', 'waves'));
    }

    public function show($encodedId)
    {
        $id = $this->resolveId($encodedId);
        $spmb = SpmbRegistration::with(['user', 'wave.academicYear', 'class', 'major', 'documents'])->findOrFail($id);
        $classes = ClassModel::where('is_active', true)->get();
        $majors = Major::where('is_active', true)->get();
        return view('admin.spmb.show', compact('spmb', 'classes', 'majors'));
    }

    public function printForm($encodedId)
    {
        $id = $this->resolveId($encodedId);
        $spmb = SpmbRegistration::with(['user', 'wave.academicYear', 'class', 'major', 'documents'])->findOrFail($id);
        $schoolName = Setting::get('school_name', 'Sekolah');
        $schoolShortName = Setting::get('school_short_name', 'SCH');
        $schoolAddress = Setting::get('school_address', '');
        $schoolCity = Setting::get('school_city', '');
        $schoolProvince = Setting::get('school_province', '');
        $schoolPostalCode = Setting::get('school_postal_code', '');
        $schoolPhone = Setting::get('school_phone', '');
        $schoolEmail = Setting::get('school_email', '');
        $schoolWebsite = Setting::get('school_website', '');
        $schoolLogo = Setting::get('school_logo', '');

        $letterheadHeaderTop = Setting::get('letterhead_header_top');
        $letterheadSub = Setting::get('letterhead_sub');
        
        $logoLeftPath = Setting::get('letterhead_logo_path') ?? Setting::get('logo_path') ?? Setting::get('school_logo');
        $logoRightPath = Setting::get('letterhead_logo_right_path');

        $letterheadLogoLeftUrl = $logoLeftPath ? (\Illuminate\Support\Str::startsWith($logoLeftPath, ['http://', 'https://', 'img/']) ? asset($logoLeftPath) : asset('img/' . $logoLeftPath)) : null;
        $letterheadLogoRightUrl = $logoRightPath ? (\Illuminate\Support\Str::startsWith($logoRightPath, ['http://', 'https://', 'img/']) ? asset($logoRightPath) : asset('img/' . $logoRightPath)) : null;
        
        return view('admin.spmb.print-form', compact(
            'spmb', 'schoolName', 'schoolShortName', 'schoolAddress', 'schoolCity',
            'schoolProvince', 'schoolPostalCode', 'schoolPhone', 'schoolEmail',
            'schoolWebsite', 'schoolLogo', 'letterheadHeaderTop', 'letterheadSub',
            'letterheadLogoLeftUrl', 'letterheadLogoRightUrl'
        ));
    }

    public function confirmPayment($encodedId, Request $request)
    {
        $id = $this->resolveId($encodedId);
        $spmb = SpmbRegistration::with('wave')->findOrFail($id);
        $wave = $spmb->wave;

        // If wave has a quota, check if confirmation would exceed quota
        // (if applicant is not already held in 'pending' status)
        $isOverQuota = false;
        if ($wave && $wave->quota > 0 && $wave->remaining_quota <= 0 && $spmb->payment_status !== 'pending') {
            $isOverQuota = true;
            if (!$request->boolean('override_quota')) {
                return back()->with('quota_warning', [
                    'message' => 'Kuota pendaftaran untuk ' . $wave->name . ' telah penuh (' . $wave->quota . ' siswa). Apakah Anda yakin ingin mengonfirmasi pembayaran ini dan mengizinkan kuota berlebih (override)?',
                    'action' => route('admin.spmb.confirm-payment', encode_id($spmb->id)),
                ]);
            }
        }
        
        $transaction = \App\Models\PaymentTransaction::where('reference_type', 'spmb')
            ->where('reference_id', $spmb->id)
            ->where('status', 'pending')
            ->whereNotNull('payment_proof')
            ->latest()
            ->first();

        if (!$transaction) {
            $transaction = \App\Models\PaymentTransaction::where('reference_type', 'spmb')
                ->where('reference_id', $spmb->id)
                ->where('status', 'pending')
                ->latest()
                ->first();
        }

        if (!$transaction) {
            $amount = $wave?->registration_fee ?? (float)Setting::get('spmb_registration_fee', 0);
            $invoiceNumber = 'INV-SPMB-MANUAL-' . $spmb->id . '-' . time();
            $transaction = \App\Models\PaymentTransaction::create([
                'reference_type' => 'spmb',
                'reference_id' => $spmb->id,
                'payment_gateway' => 'manual',
                'invoice_number' => $invoiceNumber,
                'amount' => $amount,
                'status' => 'pending',
                'payment_proof' => $spmb->active_payment_proof ?? $spmb->payment_proof,
            ]);
        }

        \App\Http\Controllers\PaymentController::completePayment($transaction, ['confirmed_by' => auth()->id()]);

        // Bersihkan transaksi pending lain yang mungkin tersisa agar tidak menggantung
        \App\Models\PaymentTransaction::where('reference_type', 'spmb')
            ->where('reference_id', $spmb->id)
            ->where('id', '!=', $transaction->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        $msg = 'Pembayaran uang pendaftaran berhasil dikonfirmasi.';
        if ($isOverQuota) {
            $msg .= ' (Persetujuan Melebihi Kuota / Override Berhasil).';
        }

        return back()->with('success', $msg);
    }

    public function verify($encodedId)
    {
        $id = $this->resolveId($encodedId);
        $spmb = SpmbRegistration::findOrFail($id);
        $spmb->update([
            'status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Pendaftaran berhasil diverifikasi.');
    }

    public function accept($encodedId, Request $request)
    {
        $id = $this->resolveId($encodedId);
        $spmb = SpmbRegistration::findOrFail($id);
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'class_id' => 'nullable|exists:classes,id',
                'nisn' => 'nullable|string|max:20',
                'spp_discount' => 'nullable|numeric|min:0',
                'discount_description' => 'nullable|string|max:255',
            ]);

            // Update SPMB status
            $spmb->update([
                'status' => 'accepted',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'verification_notes' => $request->notes,
            ]);

            // Update user role to student
            $user = $spmb->user;
            $calonSiswaRole = Role::where('slug', 'calon-siswa')->first();
            $studentRole = Role::where('slug', 'student')->first();

            if ($calonSiswaRole) {
                $user->removeRole($calonSiswaRole);
            }
            if ($studentRole) {
                $user->assignRole($studentRole);
            }

            // Create student record
            Student::create([
                'user_id' => $user->id,
                'nisn' => $validated['nisn'] ?? $spmb->nisn,
                'nik' => $spmb->nik,
                'gender' => $spmb->gender,
                'birth_place' => $spmb->birth_place,
                'birth_date' => $spmb->birth_date,
                'phone' => $spmb->phone,
                'email' => $spmb->email,
                'address' => $spmb->address,
                'parent_name' => $spmb->parent_name,
                'parent_phone' => $spmb->parent_phone,
                'class_id' => $validated['class_id'],
                'major_id' => $spmb->major_id,
                'spp_discount' => $validated['spp_discount'] ?? 0,
                'discount_description' => $validated['discount_description'] ?? null,
            ]);

            DB::commit();

            // Send WA Notification on Accept
            $waTarget = $spmb->parent_phone ?? $spmb->phone;
            if ($waTarget && \App\Models\Setting::get('wa_notify_spmb', '1') == '1') {
                $schoolName = \App\Models\Setting::get('school_name', 'Sekolah');
                $waMsg = "Pengumuman SPMB - {$schoolName}\n\nSelamat! Pendaftaran SPMB a.n. *{$spmb->full_name}* (No: {$spmb->registration_number}) telah *DITERIMA* sebagai siswa di {$schoolName}.\n\nCatatan: " . ($request->notes ?? 'Selamat bergabung!') . "\n\nTerima kasih.";
                \App\Services\WhatsAppService::sendMessage($waTarget, $waMsg);
            }

            return back()->with('success', 'Pendaftaran diterima. User sekarang menjadi siswa.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pendaftaran: ' . $e->getMessage());
        }
    }

    public function reject($encodedId, Request $request)
    {
        $id = $this->resolveId($encodedId);
        $spmb = SpmbRegistration::findOrFail($id);
        $spmb->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'verification_notes' => $request->notes,
        ]);

        // Send WA Notification on Reject
        $waTarget = $spmb->parent_phone ?? $spmb->phone;
        if ($waTarget && \App\Models\Setting::get('wa_notify_spmb', '1') == '1') {
            $schoolName = \App\Models\Setting::get('school_name', 'Sekolah');
            $waMsg = "Pengumuman SPMB - {$schoolName}\n\nPemberitahuan pendaftaran SPMB a.n. *{$spmb->full_name}* (No: {$spmb->registration_number}).\nStatus: *BELUM DAPAT DITERIMA*\n\nCatatan Admin: " . ($request->notes ?? 'Mohon maaf, syarat pendaftaran belum terpenuhi.') . "\n\nTerima kasih.";
            \App\Services\WhatsAppService::sendMessage($waTarget, $waMsg);
        }

        return back()->with('success', 'Pendaftaran ditolak.');
    }

    public function updatePhoto($encodedId, Request $request)
    {
        $id = $this->resolveId($encodedId);
        $spmb = SpmbRegistration::with('documents')->findOrFail($id);

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'photo.required' => 'Pilih file pas foto terlebih dahulu.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format file foto harus jpeg, png, jpg, atau webp.',
            'photo.max' => 'Ukuran file foto maksimal 3MB.',
        ]);

        $file = $request->file('photo');
        $savedFile = save_uploaded_public_file($file, 'img/spmb/photos');
        $fileName = basename($savedFile);

        $oldPhoto = $spmb->documents->firstWhere('type', 'photo');
        if ($oldPhoto) {
            if ($oldPhoto->file_path) {
                delete_public_file($oldPhoto->file_path, 'img/spmb/photos');
            }
            $oldPhoto->update([
                'file_path' => 'spmb/photos/' . $fileName,
                'file_name' => $fileName,
                'file_mime' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'is_verified' => true,
            ]);
        } else {
            $spmb->documents()->create([
                'type' => 'photo',
                'file_path' => 'spmb/photos/' . $fileName,
                'file_name' => $fileName,
                'file_mime' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'is_verified' => true,
            ]);
        }

        return back()->with('success', 'Pas foto calon siswa berhasil diperbarui oleh Admin.');
    }

    public function requestRevision($encodedId, Request $request)
    {
        $id = $this->resolveId($encodedId);
        $spmb = SpmbRegistration::findOrFail($id);

        $request->validate([
            'notes' => 'required|string|max:1000',
        ], [
            'notes.required' => 'Catatan perbaikan / revisi untuk wali murid wajib diisi.',
        ]);

        try {
            $spmb->update([
                'status' => 'need_revision',
                'verification_notes' => $request->notes,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $spmb->update([
                'status' => 'draft',
                'verification_notes' => $request->notes,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);
        }

        // Send WA Notification if requested or enabled
        $waTarget = $spmb->parent_phone ?? $spmb->phone;
        if ($waTarget && ($request->boolean('send_wa', true) || \App\Models\Setting::get('wa_notify_spmb', '1') == '1')) {
            $schoolName = \App\Models\Setting::get('school_name', 'Sekolah');
            $waMsg = "Pemberitahuan Revisi Berkas SPMB - {$schoolName}\n\nHalo Bapak/Ibu wali dari *{$spmb->full_name}* (No: {$spmb->registration_number}).\n\nBerkas pendaftaran ananda memerlukan *perbaikan/revisi*.\n\n*Catatan Panitia SPMB:*\n\"{$request->notes}\"\n\nSilakan login ke portal SPMB untuk mengunggah berkas/foto perbaikan:\n" . route('spmb.dashboard.index') . "\n\nTerima kasih.";
            \App\Services\WhatsAppService::sendMessage($waTarget, $waMsg);
        }

        if ($request->boolean('open_wa') && $spmb->whatsapp_phone) {
            return back()->with([
                'success' => 'Status pendaftaran diubah menjadi perlu revisi dan permintaan dikirimkan ke wali murid.',
                'open_wa_url' => $spmb->whatsapp_url,
            ]);
        }

        return back()->with('success', 'Permintaan revisi berkas berhasil disimpan. Wali murid diminta untuk memperbaiki berkas.');
    }

    public function resetPassword($encodedId, Request $request)
    {
        $id = $this->resolveId($encodedId);
        $spmb = SpmbRegistration::with('user')->findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|min:8',
        ]);

        $spmb->user->password = Hash::make($validated['password']);
        $spmb->user->save();

        return back()->with('success', 'Password user berhasil direset.');
    }

    public function destroy($encodedId)
    {
        $id = $this->resolveId($encodedId);
        $spmb = SpmbRegistration::with('documents')->findOrFail($id);
        
        foreach ($spmb->documents as $doc) {
            if ($doc->file_path) {
                delete_public_file($doc->file_path, 'doc/spmb/documents');
            }
        }

        $spmb->delete();

        return back()->with('success', 'Data pendaftaran SPMB berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required',
        ]);

        $ids = collect($request->ids)->map(fn($id) => $this->resolveId($id))->filter();
        
        $registrations = SpmbRegistration::with('documents')->whereIn('id', $ids)->get();
        $count = 0;
        foreach ($registrations as $spmb) {
            foreach ($spmb->documents as $doc) {
                if ($doc->file_path) {
                    delete_public_file($doc->file_path, 'doc/spmb/documents');
                }
            }
            $spmb->delete();
            $count++;
        }

        return back()->with('success', "Berhasil menghapus {$count} data pendaftaran SPMB terpilih.");
    }
}

