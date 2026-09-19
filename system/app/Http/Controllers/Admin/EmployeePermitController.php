<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeePermit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EmployeePermitController extends Controller
{
    /**
     * Helper to verify if user has Principal or Admin privileges.
     */
    protected function isPrincipalOrAdmin(User $user): bool
    {
        return $user->hasRole('kepala-sekolah') || $user->hasRole('admin') || $user->hasRole('super-admin');
    }

    /**
     * Display a listing of employee leave & permit submissions.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $isPrincipal = $this->isPrincipalOrAdmin($currentUser);

        $query = EmployeePermit::with(['user', 'approver']);

        // Non-privileged users can only view their own submissions
        if (!$isPrincipal) {
            $query->where('user_id', $currentUser->id);
        } else {
            // Filters for Principal / Admin
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
        }

        // Common Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('permit_type')) {
            $query->where('permit_type', $request->permit_type);
        }
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        $permits = $query->latest()->paginate(15)->withQueryString();

        // Statistics
        $statsBase = $isPrincipal ? EmployeePermit::query() : EmployeePermit::where('user_id', $currentUser->id);
        $stats = [
            'total' => (clone $statsBase)->count(),
            'pending' => (clone $statsBase)->where('status', 'pending')->count(),
            'approved' => (clone $statsBase)->where('status', 'approved')->count(),
            'rejected' => (clone $statsBase)->where('status', 'rejected')->count(),
        ];

        // Teachers / Employees list for dropdown filters and submission
        $employees = [];
        if ($isPrincipal) {
            $employees = User::whereHas('roles', function ($q) {
                $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah', 'bendahara', 'guru-bk']);
            })->orderBy('name')->get(['id', 'name', 'nip', 'email']);
        }

        return view('admin.employee-permits.index', compact('permits', 'stats', 'isPrincipal', 'employees'));
    }

    /**
     * Show form for creating a new permit.
     */
    public function create()
    {
        $currentUser = auth()->user();
        $isPrincipal = $this->isPrincipalOrAdmin($currentUser);

        $employees = [];
        if ($isPrincipal) {
            $employees = User::whereHas('roles', function ($q) {
                $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah', 'bendahara', 'guru-bk']);
            })->orderBy('name')->get(['id', 'name', 'nip']);
        }

        return view('admin.employee-permits.create', compact('isPrincipal', 'employees'));
    }

    /**
     * Store a newly created permit in storage.
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $isPrincipal = $this->isPrincipalOrAdmin($currentUser);

        $validated = $request->validate([
            'user_id' => $isPrincipal ? 'nullable|exists:users,id' : 'nullable',
            'permit_type' => 'required|in:sakit,izin,cuti,tugas_luar,lainnya',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'emergency_contact' => 'nullable|string|max:50',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:10240',
            'notes' => 'nullable|string|max:500',
            'auto_approve' => 'nullable|boolean',
        ]);

        $targetUserId = ($isPrincipal && !empty($validated['user_id'])) ? (int) $validated['user_id'] : $currentUser->id;
        $validated['user_id'] = $targetUserId;

        // Handle attachment file upload
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $isDoc = in_array($ext, ['pdf', 'doc', 'docx']);

            $folderName = 'employee_permits';
            $targetDir = public_path($isDoc ? "doc/{$folderName}" : "img/{$folderName}");

            if (!File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }

            $filename = time() . '_' . Str::random(10) . '.' . $ext;
            $file->move($targetDir, $filename);

            $validated['proof_file'] = ($isDoc ? "doc/{$folderName}/" : "img/{$folderName}/") . $filename;
        }

        // Approval status determination
        $autoApprove = $isPrincipal && $request->boolean('auto_approve');
        if ($autoApprove) {
            $validated['status'] = 'approved';
            $validated['approved_by'] = $currentUser->id;
            $validated['approved_at'] = now();
        } else {
            $validated['status'] = 'pending';
        }

        $permit = EmployeePermit::create($validated);

        if ($permit->status === 'approved') {
            $permit->syncAttendance();
            $msg = 'Permohonan izin pegawai berhasil disimpan dan otomatis disetujui serta disinkronkan ke data presensi!';
        } else {
            $msg = 'Permohonan izin pegawai berhasil diajukan dan sedang menunggu verifikasi Kepala Sekolah.';
        }

        return redirect()->route('admin.employee-permits.index')->with('success', $msg);
    }

    /**
     * Approve an employee permit (Principal or Admin only).
     */
    public function approve(Request $request, EmployeePermit $permit)
    {
        $currentUser = auth()->user();
        if (!$this->isPrincipalOrAdmin($currentUser)) {
            abort(403, 'Hanya Kepala Sekolah atau Administrator yang berwenang menyetujui izin pegawai.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $permit->update([
            'status' => 'approved',
            'approved_by' => $currentUser->id,
            'approved_at' => now(),
            'notes' => $request->filled('notes') ? $request->input('notes') : $permit->notes,
        ]);

        // Synchronize to teacher_attendances table
        $permit->syncAttendance();

        return redirect()->back()->with('success', 'Permohonan izin pegawai resmi disetujui dan data presensi telah diperbarui secara otomatis!');
    }

    /**
     * Reject an employee permit (Principal or Admin only).
     */
    public function reject(Request $request, EmployeePermit $permit)
    {
        $currentUser = auth()->user();
        if (!$this->isPrincipalOrAdmin($currentUser)) {
            abort(403, 'Hanya Kepala Sekolah atau Administrator yang berwenang menolak izin pegawai.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        // If it was previously approved, revert any attendance generated
        if ($permit->status === 'approved') {
            $permit->revertAttendance();
        }

        $permit->update([
            'status' => 'rejected',
            'approved_by' => $currentUser->id,
            'approved_at' => now(),
            'notes' => $request->input('notes'),
        ]);

        return redirect()->back()->with('success', 'Permohonan izin pegawai telah ditolak.');
    }

    /**
     * Remove or cancel an employee permit.
     */
    public function destroy(EmployeePermit $permit)
    {
        $currentUser = auth()->user();
        $isPrincipal = $this->isPrincipalOrAdmin($currentUser);

        // Regular employees can only cancel their own pending permits
        if (!$isPrincipal) {
            if ($permit->user_id !== $currentUser->id) {
                abort(403, 'Anda tidak memiliki hak akses menghapus permohonan ini.');
            }
            if ($permit->status !== 'pending') {
                return redirect()->back()->with('error', 'Permohonan izin yang telah diproses tidak dapat dibatalkan secara mandiri.');
            }
        }

        // Revert attendance if approved
        if ($permit->status === 'approved') {
            $permit->revertAttendance();
        }

        // Delete attachment if present
        if ($permit->proof_file && File::exists(public_path($permit->proof_file))) {
            File::delete(public_path($permit->proof_file));
        }

        $permit->delete();

        return redirect()->back()->with('success', 'Permohonan izin pegawai berhasil dihapus.');
    }
}
