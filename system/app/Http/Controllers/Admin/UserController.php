<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'guru');

        // Base query excluding students and canteen users
        $query = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('slug', ['student', 'siswa', 'calon-siswa', 'kantin', 'canteen']);
        })->with(['roles', 'homeroomClasses']);

        if ($activeTab === 'guru') {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah']);
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $teachersCount = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah']);
        })->count();

        $allUsersCount = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('slug', ['student', 'siswa', 'calon-siswa', 'kantin', 'canteen']);
        })->count();

        $roles = Role::whereNotIn('slug', ['student', 'siswa', 'calon-siswa', 'kantin', 'canteen'])->get();

        return view('admin.users.index', compact('users', 'roles', 'activeTab', 'teachersCount', 'allUsersCount'));
    }

    public function create()
    {
        $roles = Role::whereNotIn('slug', ['student', 'siswa', 'calon-siswa', 'kantin', 'canteen'])->get();
        $classes = \App\Models\ClassModel::with('major')->where('is_active', true)->orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'nip' => 'nullable|string|max:50|unique:users,nip',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:30',
            'role_id' => 'required|exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'homeroom_classes' => 'nullable|array',
            'homeroom_classes.*' => 'exists:classes,id',
        ], [
            'name.required' => 'Nama lengkap pegawai/staff wajib diisi.',
            'name.max' => 'Nama tidak boleh melebihi 255 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar untuk pengguna lain.',
            'nip.max' => 'NIP/NIPPPK tidak boleh melebihi 50 karakter.',
            'nip.unique' => 'NIP ini sudah terdaftar pada pengguna/pegawai lain.',
            'password.required' => 'Password akun wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok dengan password yang dimasukkan.',
            'phone.max' => 'Nomor Telepon/WA tidak boleh melebihi 30 karakter.',
            'role_id.required' => 'Peran/Jabatan akun wajib dipilih.',
            'role_id.exists' => 'Peran/Jabatan yang dipilih tidak valid.',
            'avatar.image' => 'Foto profil harus berupa berkas gambar (JPG, JPEG, PNG, WEBP).',
            'avatar.mimes' => 'Format foto profil harus JPG, JPEG, PNG, atau WEBP.',
            'avatar.max' => 'Ukuran foto profil tidak boleh melebihi 2MB.',
            'homeroom_classes.*.exists' => 'Kelas yang dipilih sebagai Wali Kelas tidak valid.',
        ]);

        // Handle avatar upload
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $savedAvatar = save_uploaded_public_file($request->file('avatar'), 'img/avatars');
            $avatarPath = basename($savedAvatar);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nip' => $validated['nip'] ?? null,
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'avatar' => $avatarPath,
        ]);

        // Assign role
        $role = Role::findOrFail($validated['role_id']);
        $user->assignRole($role);

        // Assign homeroom classes (only for role guru)
        if (in_array($role->slug, ['guru', 'teacher']) && !empty($request->homeroom_classes)) {
            \App\Models\ClassModel::whereIn('id', $request->homeroom_classes)->update(['homeroom_teacher_id' => $user->id]);
        }

        return redirect()->route('admin.users.index', ['tab' => 'guru'])
            ->with('success', 'Data Guru & Staf berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit($encodedId)
    {
        $id = decode_id($encodedId);
        $user = User::with('homeroomClasses')->findOrFail($id);
        $roles = Role::whereNotIn('slug', ['student', 'siswa', 'calon-siswa', 'kantin', 'canteen'])->get();
        $classes = \App\Models\ClassModel::with('major')->where('is_active', true)->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles', 'classes'));
    }

    public function update(Request $request, $encodedId)
    {
        $id = decode_id($encodedId);
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nip' => 'nullable|string|max:50|unique:users,nip,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:30',
            'role_id' => 'required|exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'homeroom_classes' => 'nullable|array',
            'homeroom_classes.*' => 'exists:classes,id',
        ], [
            'name.required' => 'Nama lengkap pegawai/staff wajib diisi.',
            'name.max' => 'Nama tidak boleh melebihi 255 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar untuk pengguna lain.',
            'nip.max' => 'NIP/NIPPPK tidak boleh melebihi 50 karakter.',
            'nip.unique' => 'NIP ini sudah terdaftar pada pengguna/pegawai lain.',
            'password.min' => 'Password baru minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok dengan password baru.',
            'phone.max' => 'Nomor Telepon/WA tidak boleh melebihi 30 karakter.',
            'role_id.required' => 'Peran/Jabatan akun wajib dipilih.',
            'role_id.exists' => 'Peran/Jabatan yang dipilih tidak valid.',
            'avatar.image' => 'Foto profil harus berupa berkas gambar (JPG, JPEG, PNG, WEBP).',
            'avatar.mimes' => 'Format foto profil harus JPG, JPEG, PNG, atau WEBP.',
            'avatar.max' => 'Ukuran foto profil tidak boleh melebihi 2MB.',
            'homeroom_classes.*.exists' => 'Kelas yang dipilih sebagai Wali Kelas tidak valid.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->nip = $validated['nip'] ?? null;

        // Handle password update
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                delete_public_file($user->avatar, 'img/avatars');
            }
            $savedAvatar = save_uploaded_public_file($request->file('avatar'), 'img/avatars');
            $user->avatar = 'avatars/' . basename($savedAvatar);
        }

        $user->save();

        // Sync role
        $role = Role::findOrFail($validated['role_id']);
        $user->syncRoles([$role]);

        // Sync multi-class homeroom assignment (only if assigned role is guru/teacher)
        \App\Models\ClassModel::where('homeroom_teacher_id', $user->id)->update(['homeroom_teacher_id' => null]);
        if (in_array($role->slug, ['guru', 'teacher']) && !empty($request->homeroom_classes)) {
            \App\Models\ClassModel::whereIn('id', $request->homeroom_classes)->update(['homeroom_teacher_id' => $user->id]);
        }
        
        // Reload user to get updated roles
        $user->load('roles');
        
        // Check if current user is updating their own role and lost admin access
        if (auth()->id() == $user->id && !($user->hasRole('super-admin') || $user->hasRole('admin'))) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'Role Anda telah diubah dan tidak lagi memiliki akses admin.');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.' . (!empty($validated['password']) ? ' Password telah diubah.' : ''));
    }

    public function resetPassword(Request $request, $encodedId)
    {
        $id = decode_id($encodedId);
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'password' => 'required|min:8',
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Password berhasil direset.');
    }

    public function destroy($encodedId)
    {
        $id = is_numeric($encodedId) ? $encodedId : decode_id($encodedId);
        $user = User::findOrFail($id);
        
        // Delete avatar
        if ($user->avatar) {
            delete_public_file($user->avatar, 'img/avatars');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    /**
     * Toggle Account Status (Active/Inactive) & Unlock Bruteforce Lockout
     */
    public function toggleStatus(Request $request, $id)
    {
        $userId = is_numeric($id) ? $id : decode_id($id);
        $user = User::findOrFail($userId);

        if ($request->has('status')) {
            $user->status = $request->status ? 'active' : 'inactive';
        } else {
            $user->status = ($user->status === 'active' && !$user->isLockedOut()) ? 'inactive' : 'active';
        }

        // Reset bruteforce lockout on activation/unlock
        if ($user->status === 'active') {
            $user->failed_login_attempts = 0;
            $user->locked_until = null;
        }

        $user->save();

        $isCurrentlyActive = ($user->status === 'active' && !$user->isLockedOut());
        $statusLabel = $isCurrentlyActive ? 'Aktif' : 'Nonaktif / Terkunci';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Status akun {$user->name} berhasil diubah menjadi {$statusLabel}.",
                'status' => $user->status,
                'is_locked' => $user->isLockedOut(),
            ]);
        }

        return back()->with('success', "Status akun {$user->name} berhasil diubah menjadi {$statusLabel}.");
    }
}
