<?php

namespace App\Http\Controllers\Spmb;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Major;
use App\Models\SpmbRegistration;
use App\Models\Wave;
use App\Models\Setting;
use App\Models\PaymentTransaction;
use App\Models\BankAccount;
use App\Services\PaymentGatewayService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Display SPMB dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $registration = SpmbRegistration::where('user_id', $user->id)->first();
        
        $activeGateways = [];
        if (Setting::get('payment_manual_enabled', '1') == '1') $activeGateways[] = 'manual';
        if (Setting::get('payment_midtrans_enabled', '0') == '1') $activeGateways[] = 'midtrans';
        if (Setting::get('payment_tripay_enabled', '0') == '1') $activeGateways[] = 'tripay';
        if (Setting::get('payment_duitku_enabled', '0') == '1') $activeGateways[] = 'duitku';

        $tripayChannels = [];
        if (in_array('tripay', $activeGateways)) {
            $tripayChannels = PaymentGatewayService::getTripayChannels();
        }

        $bankAccounts = BankAccount::where('is_active', true)->where('type', 'bank')->get();
        
        // Find existing transaction if any
        $latestTransaction = null;
        if ($registration) {
            $latestTransaction = PaymentTransaction::where('reference_type', 'spmb')
                ->where('reference_id', $registration->id)
                ->latest()
                ->first();
        }

        $registrationFee = $registration?->wave?->registration_fee 
            ?? (float)Setting::get('spmb_registration_fee', 0);

        // Redirect ke bagian pembayaran jika belum lunas
        $isPaid = $registration && $registration->payment_status === 'paid';
        $needsPayment = $registration && !$isPaid;

        return view('spmb.dashboard', compact(
            'registration', 
            'activeGateways', 
            'tripayChannels', 
            'bankAccounts', 
            'latestTransaction',
            'registrationFee',
            'isPaid',
            'needsPayment'
        ));
    }

    /**
     * Initiate online payment checkout or manual choice.
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        $registration = SpmbRegistration::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'payment_gateway' => 'required|in:midtrans,tripay,duitku,manual',
            'payment_channel' => 'required_if:payment_gateway,tripay',
        ]);

        if ($registration->payment_status === 'paid') {
            return redirect()->route('spmb.dashboard.index')->with('info', 'Uang pendaftaran Anda sudah lunas.');
        }

        $amount = $registration->wave?->registration_fee ?? (float)Setting::get('spmb_registration_fee', 0);
        if ($amount <= 0) {
            // Auto complete if free
            $registration->update(['payment_status' => 'paid', 'status' => 'draft']);
            return redirect()->route('spmb.dashboard.index')->with('success', 'Biaya pendaftaran gratis. Akun diaktifkan.');
        }

        // Generate invoice number
        $invoiceNumber = 'INV-SPMB-' . $registration->id . '-' . time();

        $customerDetails = [
            'name' => $registration->full_name,
            'email' => $registration->email ?? $user->email,
            'phone' => $registration->phone ?? $user->phone ?? '',
        ];

        // Create transaction entry
        $transaction = PaymentTransaction::create([
            'reference_type' => 'spmb',
            'reference_id' => $registration->id,
            'payment_gateway' => $request->payment_gateway,
            'invoice_number' => $invoiceNumber,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        if ($request->payment_gateway === 'midtrans') {
            $response = PaymentGatewayService::createMidtransTransaction($invoiceNumber, $amount, $customerDetails, 'Uang Pendaftaran SPMB ' . $registration->registration_number);
            if ($response['success']) {
                $transaction->update([
                    'snap_token' => $response['snap_token'],
                    'payment_url' => $response['redirect_url'],
                ]);
                return redirect()->away($response['redirect_url']);
            }
            $transaction->delete();
            return back()->withErrors(['error' => $response['message']]);
        }

        if ($request->payment_gateway === 'tripay') {
            $response = PaymentGatewayService::createTripayTransaction($invoiceNumber, $amount, $request->payment_channel, $customerDetails, 'Uang Pendaftaran SPMB ' . $registration->registration_number);
            if ($response['success']) {
                $transaction->update([
                    'payment_url' => $response['payment_url'],
                    'payment_method_code' => $request->payment_channel,
                ]);
                return redirect()->away($response['payment_url']);
            }
            $transaction->delete();
            return back()->withErrors(['error' => $response['message']]);
        }

        if ($request->payment_gateway === 'duitku') {
            $response = PaymentGatewayService::createDuitkuTransaction($invoiceNumber, $amount, $customerDetails, 'Uang Pendaftaran SPMB ' . $registration->registration_number);
            if ($response['success']) {
                $transaction->update([
                    'payment_url' => $response['payment_url'],
                ]);
                return redirect()->away($response['payment_url']);
            }
            $transaction->delete();
            return back()->withErrors(['error' => $response['message']]);
        }

        // Manual Transfer
        return redirect()->route('spmb.dashboard.index')->with([
            'success' => 'Silakan lakukan transfer bank manual dan unggah bukti transfer di bawah.',
            'active_tab' => 'manual'
        ]);
    }

    /**
     * Upload manual transfer proof receipt.
     */
    public function uploadManualProof(Request $request)
    {
        $user = Auth::user();
        $registration = SpmbRegistration::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'notes' => 'nullable|string|max:500',
        ]);

        // Find or create pending manual transaction
        $transaction = PaymentTransaction::where('reference_type', 'spmb')
            ->where('reference_id', $registration->id)
            ->where('payment_gateway', 'manual')
            ->where('status', 'pending')
            ->first();

        if (!$transaction) {
            $amount = $registration->wave?->registration_fee ?? (float)Setting::get('spmb_registration_fee', 0);
            $invoiceNumber = 'INV-SPMB-' . $registration->id . '-' . time();
            
            $transaction = PaymentTransaction::create([
                'reference_type' => 'spmb',
                'reference_id' => $registration->id,
                'payment_gateway' => 'manual',
                'invoice_number' => $invoiceNumber,
                'amount' => $amount,
                'status' => 'pending',
            ]);
        }

        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $ext = strtolower($file->getClientOriginalExtension());
            $isDoc = in_array($ext, ['pdf', 'docx', 'doc']);
            $targetSubfolder = $isDoc ? 'doc/spmb/proofs' : 'img/spmb/proofs';
            $savedProof = save_uploaded_public_file($file, $targetSubfolder);

            $bank = BankAccount::find($request->bank_account_id);
            $bankInfo = $bank ? "Transfer ke: {$bank->bank_name} - {$bank->account_number} a.n. {$bank->account_name}" : '';
            $notes = $request->notes ? $bankInfo . "\nCatatan: " . $request->notes : $bankInfo;

            $proofPath = ($isDoc ? 'doc/spmb/proofs/' : 'spmb/proofs/') . basename($savedProof);

            $transaction->update([
                'payment_proof' => $proofPath,
                'notes' => $notes,
                'payment_method_code' => $bank ? $bank->bank_name : null,
            ]);

            // Update registration payment status to pending confirmation
            $registration->update([
                'payment_status' => 'pending',
                'payment_proof' => $proofPath, // Sync with registration column
            ]);

            // Notify Admin via WhatsApp if configured
            $adminPhone = Setting::get('spmb_contact_phone') ?? Setting::get('school_whatsapp');
            if ($adminPhone && Setting::get('wa_notify_spmb', '1') == '1') {
                $schoolName = Setting::get('school_name', 'Sekolah');
                $msg = "Notifikasi Pembayaran SPMB Manual - {$schoolName}\n\nHalo Admin,\n\nCalon siswa baru telah mengunggah bukti transfer manual pendaftaran:\n\nNama: *{$registration->full_name}*\nNo. Daftar: *{$registration->registration_number}*\nNominal: *Rp " . number_format($transaction->amount, 0, ',', '.') . "*\n\nSilakan periksa di dashboard admin dan lakukan verifikasi pembayaran.\nTerima kasih.";
                WhatsAppService::sendMessage($adminPhone, $msg);
            }

            return redirect()->route('spmb.dashboard.index')->with('success', 'Bukti transfer pendaftaran berhasil diunggah. Menunggu konfirmasi admin.');
        }

        return back()->withErrors(['error' => 'Gagal mengunggah berkas bukti transfer.']);
    }

    /**
     * Submit pendaftaran (Final Submit) after stages 2-5 are completed.
     */
    public function finalSubmit()
    {
        $user = Auth::user();
        $registration = SpmbRegistration::where('user_id', $user->id)->firstOrFail();

        if ($registration->payment_status !== 'paid') {
            return redirect()->route('spmb.dashboard.index')->with('error', 'Silakan selesaikan pembayaran uang pendaftaran terlebih dahulu.');
        }

        // Validate basic completion
        if (empty($registration->nisn) || empty($registration->nik) || empty($registration->gender) || empty($registration->birth_date) || empty($registration->address)) {
            return back()->withErrors(['error' => 'Data pribadi pendaftar belum lengkap. Silakan edit dan lengkapi data Anda.']);
        }

        if (empty($registration->parent_name) || empty($registration->parent_phone)) {
            return back()->withErrors(['error' => 'Data orang tua / wali belum lengkap. Silakan lengkapi terlebih dahulu.']);
        }

        // Check if documents exist
        $photo = $registration->documents()->where('type', 'photo')->first();
        $kk = $registration->documents()->where('type', 'kk')->first();
        $birthCert = $registration->documents()->where('type', 'birth_certificate')->first();

        if (!$photo || !$kk || !$birthCert) {
            return back()->withErrors(['error' => 'Berkas persyaratan wajib (Pasfoto, Kartu Keluarga, dan Akta Lahir) belum diunggah.']);
        }

        // Complete the registration submission
        $registration->update([
            'status' => 'submitted',
        ]);

        // Send WA Notification
        if ($registration->phone && Setting::get('wa_notify_spmb', '1') == '1') {
            $schoolName = Setting::get('school_name', 'Sekolah');
            $msg = "Formulir Pendaftaran SPMB Dikirim - {$schoolName}\n\nHalo *{$registration->full_name}*,\n\nFormulir pendaftaran dan berkas persyaratan Anda telah berhasil dikirim untuk proses verifikasi berkas oleh admin.\n\nNomor Pendaftaran: *{$registration->registration_number}*\nStatus: *Menunggu Verifikasi Berkas*\n\nAnda akan menerima notifikasi berkala mengenai perkembangan status pendaftaran Anda.\nTerima kasih.";
            WhatsAppService::sendMessage($registration->phone, $msg);
        }

        return redirect()->route('spmb.dashboard.index')->with('success', 'Formulir pendaftaran Anda berhasil dikirim secara resmi! Silakan pantau status seleksi secara berkala.');
    }

    /**
     * Show edit form for registration data
     */
    public function edit()
    {
        $user = Auth::user();
        $registration = SpmbRegistration::where('user_id', $user->id)->firstOrFail();
        
        if ($registration->payment_status !== 'paid') {
            return redirect()->route('spmb.dashboard.index')
                ->with('error', 'Silakan selesaikan pembayaran uang pendaftaran terlebih dahulu untuk melengkapi data.');
        }

        $waves = Wave::where('status', 'active')->get();
        $majors = Major::where('is_active', true)->get();

        return view('spmb.edit', compact('registration', 'waves', 'majors'));
    }

    /**
     * Update registration data
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $registration = SpmbRegistration::where('user_id', $user->id)->firstOrFail();

        if ($registration->payment_status !== 'paid') {
            return redirect()->route('spmb.dashboard.index')
                ->with('error', 'Silakan selesaikan pembayaran uang pendaftaran terlebih dahulu untuk melengkapi data.');
        }

        $validated = $request->validate([
            // Data Pribadi
            'nisn' => 'required|string|max:20',
            'nik' => 'required|string|max:20',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',

            // Data Sekolah
            'origin_school' => 'required|string|max:255',
            'major_id' => 'nullable|exists:majors,id',

            // Data Orang Tua
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',
            'parent_address' => 'required|string',

            // Upload Dokumen
            'photo' => 'nullable|image|max:2048',
            'kk' => 'nullable|file|max:2048',
            'birth_certificate' => 'nullable|file|max:2048',
        ]);

        try {
            // Update registration data
            $registration->update([
                'full_name' => $validated['full_name'],
                'nisn' => $validated['nisn'] ?? null,
                'nik' => $validated['nik'] ?? null,
                'gender' => $validated['gender'],
                'birth_place' => $validated['birth_place'] ?? null,
                'birth_date' => $validated['birth_date'] ? $validated['birth_date'] : null,
                'address' => $validated['address'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'origin_school' => $validated['origin_school'] ?? null,
                'major_id' => $validated['major_id'] ?? null,
                'parent_name' => $validated['parent_name'] ?? null,
                'parent_phone' => $validated['parent_phone'] ?? null,
                'parent_address' => $validated['parent_address'] ?? null,
            ]);

            // Update user name if changed
            $user->update([
                'name' => $validated['full_name'],
            ]);

            // Handle file uploads (only if file is uploaded)
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $this->handleFileUpload($request, $registration, 'photo', 'spmb/photos');
            }
            if ($request->hasFile('kk') && $request->file('kk')->isValid()) {
                $this->handleFileUpload($request, $registration, 'kk', 'spmb/documents');
            }
            if ($request->hasFile('birth_certificate') && $request->file('birth_certificate')->isValid()) {
                $this->handleFileUpload($request, $registration, 'birth_certificate', 'spmb/documents');
            }

            return redirect()->route('spmb.dashboard.edit')
                ->with('success', 'Data pendaftaran berhasil diperbarui. Klik kembali ke dashboard untuk mengirimkan pendaftaran Anda.');
        } catch (\Exception $e) {
            Log::error('SPMB Update Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menyimpan perubahan. Pastikan semua data sudah benar'])->withInput();
        }
    }

    /**
     * Handle file upload for SPMB documents
     */
    private function handleFileUpload($request, $registration, $fieldName, $uploadPath)
    {
        if (!$request->hasFile($fieldName)) {
            return;
        }

        $file = $request->file($fieldName);
        
        // Check if file is valid
        if (!$file || !$file->isValid()) {
            Log::error('File upload error: File is not valid - ' . $fieldName);
            return;
        }

        try {
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();
            $ext = strtolower($file->getClientOriginalExtension());
            $isDoc = in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx']);
            $targetSubfolder = ($isDoc ? 'doc/' : 'img/') . trim($uploadPath, '/');
            
            $savedFile = save_uploaded_public_file($file, $targetSubfolder);
            $fileName = basename($savedFile);

            $oldDocument = $registration->documents()->where('type', $fieldName)->first();
            if ($oldDocument) {
                if ($oldDocument->file_path) {
                    delete_public_file($oldDocument->file_path, 'doc/spmb/documents');
                }
                $oldDocument->delete();
            }

            $registration->documents()->create([
                'type' => $fieldName,
                'file_path' => ($isDoc ? 'doc/' : '') . trim($uploadPath, '/') . '/' . $fileName,
                'file_name' => $fileName,
                'file_mime' => $mimeType,
                'file_size' => $fileSize,
            ]);
        } catch (\Exception $e) {
            Log::error('File upload exception for ' . $fieldName . ': ' . $e->getMessage());
        }
    }

    /**
     * Show account edit form (user credentials)
     */
    public function account()
    {
        $user = Auth::user();
        $registration = SpmbRegistration::where('user_id', $user->id)->first();
        $photoDoc = $registration ? $registration->documents()->where('type', 'photo')->first() : null;
        
        return view('spmb.account', compact('user', 'photoDoc'));
    }

    /**
     * Update account credentials
     */
    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $password = trim($request->input('password') ?? '');
            $passwordConfirmation = trim($request->input('password_confirmation') ?? '');
            $currentPassword = trim($request->input('current_password') ?? '');

            if (!empty($password)) {
                if (empty($passwordConfirmation)) {
                    return back()->withErrors(['password_confirmation' => 'Konfirmasi password harus diisi'])->withInput();
                }
                if ($password !== $passwordConfirmation) {
                    return back()->withErrors(['password' => 'Konfirmasi password tidak sesuai'])->withInput();
                }
                if (strlen($password) < 8) {
                    return back()->withErrors(['password' => 'Password minimal 8 karakter'])->withInput();
                }
                if (empty($currentPassword)) {
                    return back()->withErrors(['current_password' => 'Password saat ini harus diisi'])->withInput();
                }
                if (!Hash::check($currentPassword, $user->password)) {
                    return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai'])->withInput();
                }
            }

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => !empty($password) ? Hash::make($password) : $user->password,
            ]);

            return redirect()->route('spmb.dashboard.account')
                ->with('success', 'Akun berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('SPMB Account Update Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui akun: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show announcements for SPMB
     */
    public function announcements(Request $request)
    {
        $announcements = \App\Models\Announcement::published()
            ->where('type', 'spmb')
            ->latest()
            ->paginate(10);
        
        return view('spmb.announcements', compact('announcements'));
    }
}
