<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'guru');
        $perPage = in_array((int)$request->input('per_page'), [10, 20, 50, 100]) ? (int)$request->input('per_page') : 10;
        $sort = $request->input('sort', 'name_asc');

        // Base query excluding students and canteen users
        $query = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('slug', ['student', 'siswa', 'calon-siswa', 'kantin', 'canteen']);
        })->with(['roles', 'homeroomClasses', 'quranClasses']);

        if ($activeTab === 'guru') {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('slug', [
                    'guru', 'teacher', 'guru-quran', 'admin', 'operator', 
                    'tata-usaha', 'staff', 'kepala-sekolah', 'wakasek-kesiswaan', 
                    'wakasek-kurikulum', 'wakasek-kehumasan', 'bendahara', 'guru-bk'
                ]);
            });
        }

        // Filter per Role / Jabatan
        if ($request->filled('role')) {
            $roleParam = $request->role;
            $query->where(function ($q) use ($roleParam) {
                $q->whereHas('roles', function ($r) use ($roleParam) {
                    $r->where('slug', $roleParam);
                })->orWhere('jabatan', 'like', "%{$roleParam}%");
            });
        }

        if ($request->filled('jabatan')) {
            $query->where('jabatan', $request->jabatan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        // Sorting per urutan nama
        if ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        } elseif ($sort === 'latest') {
            $query->latest();
        } else {
            // Default: name_asc (A-Z)
            $query->orderBy('name', 'asc');
        }

        $users = $query->paginate($perPage)->withQueryString();

        $teachersCount = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', [
                'guru', 'teacher', 'guru-quran', 'admin', 'operator', 
                'tata-usaha', 'staff', 'kepala-sekolah', 'wakasek-kesiswaan', 
                'wakasek-kurikulum', 'wakasek-kehumasan', 'bendahara', 'guru-bk'
            ]);
        })->count();

        $allUsersCount = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('slug', ['student', 'siswa', 'calon-siswa', 'kantin', 'canteen']);
        })->count();

        $roles = Role::whereNotIn('slug', ['student', 'siswa', 'calon-siswa', 'kantin', 'canteen'])->get();

        // Daftar jabatan unik untuk filter
        $jabatanList = collect();
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'jabatan')) {
                $jabatanList = User::whereNotNull('jabatan')
                    ->where('jabatan', '!=', '')
                    ->distinct()
                    ->pluck('jabatan')
                    ->sort()
                    ->values();
            }
        } catch (\Throwable $e) {}

        return view('admin.users.index', compact(
            'users', 'roles', 'activeTab', 'teachersCount', 'allUsersCount', 
            'perPage', 'sort', 'jabatanList'
        ));
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
            'tmt' => 'nullable|date',
            'last_education' => 'nullable|string|max:100',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:30',
            'role_id' => 'required|exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'homeroom_classes' => 'nullable|array',
            'homeroom_classes.*' => 'exists:classes,id',
            'quran_classes' => 'nullable|array',
            'quran_classes.*' => 'exists:classes,id',
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
            'quran_classes.*.exists' => 'Kelas yang dipilih sebagai Kelas Binaan Qur\'an tidak valid.',
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
            'tmt' => $validated['tmt'] ?? null,
            'last_education' => $validated['last_education'] ?? null,
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

        // Assign quran classes (for guru-quran or guru)
        if (!empty($request->quran_classes)) {
            $user->quranClasses()->sync($request->quran_classes);
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
        $user = User::with(['homeroomClasses', 'quranClasses'])->findOrFail($id);
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
            'tmt' => 'nullable|date',
            'last_education' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:30',
            'role_id' => 'required|exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'homeroom_classes' => 'nullable|array',
            'homeroom_classes.*' => 'exists:classes,id',
            'quran_classes' => 'nullable|array',
            'quran_classes.*' => 'exists:classes,id',
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
            'quran_classes.*.exists' => 'Kelas yang dipilih sebagai Kelas Binaan Qur\'an tidak valid.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->nip = $validated['nip'] ?? null;
        $user->tmt = $validated['tmt'] ?? null;
        $user->last_education = $validated['last_education'] ?? null;

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

        // Sync quran classes
        $user->quranClasses()->sync($request->quran_classes ?? []);
        
        // Reload user to get updated roles
        $user->load(['roles', 'quranClasses']);
        
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

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        
        // Detach classes
        ClassModel::where('homeroom_teacher_id', $user->id)->update(['homeroom_teacher_id' => null]);
        ClassModel::where('quran_teacher_id', $user->id)->update(['quran_teacher_id' => null]);

        // Delete avatar
        if ($user->avatar) {
            delete_public_file($user->avatar, 'img/avatars');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    /**
     * Hapus massal data user / guru yang dicentang
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required',
        ]);

        $ids = collect($request->ids)->map(function ($id) {
            return is_numeric($id) ? (int)$id : decode_id($id);
        })->filter();

        // Jangan izinkan menghapus diri sendiri
        $ids = $ids->reject(function ($id) {
            return $id == auth()->id();
        });

        if ($ids->isEmpty()) {
            return back()->with('error', 'Tidak ada data user valid yang dapat dihapus.');
        }

        $users = User::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($users as $user) {
            // Lindungi super-admin utama
            if ($user->hasRole('super-admin') && User::whereHas('roles', fn($q) => $q->where('slug', 'super-admin'))->count() <= 1) {
                continue;
            }

            // Lepas penugasan kelas binaan
            ClassModel::where('homeroom_teacher_id', $user->id)->update(['homeroom_teacher_id' => null]);
            ClassModel::where('quran_teacher_id', $user->id)->update(['quran_teacher_id' => null]);

            // Hapus avatar jika ada
            if ($user->avatar) {
                delete_public_file($user->avatar, 'img/avatars');
            }

            $user->delete();
            $count++;
        }

        $message = "Berhasil menghapus {$count} data user / guru terpilih.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
            ]);
        }

        return back()->with('success', $message);
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

    /**
     * Download Excel template for mass importing teachers and staff
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Guru & Pegawai');

        // Column Headers
        $headers = [
            'A1' => 'Nama Lengkap*',
            'B1' => 'NIP / NRH',
            'C1' => 'Email (Username Login)*',
            'D1' => 'Password (Kata Sandi)*',
            'E1' => 'No. WhatsApp / HP',
            'F1' => 'Jenis Kelamin (L/P)',
            'G1' => 'Peran / Jabatan',
            'H1' => 'Wali Kelas (Opsional)',
            'I1' => 'TMT (YYYY-MM-DD)',
            'J1' => 'Pendidikan Terakhir',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Header Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']], // Royal Blue
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Sample Data 1
        $sheet->setCellValue('A2', 'Ustadz Ahmad Dahlan, S.Pd.I');
        $sheet->setCellValueExplicit('B2', '198507152010011002', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('C2', 'ahmad.dahlan@sekolah.sch.id');
        $sheet->setCellValue('D2', 'guru123');
        $sheet->setCellValueExplicit('E2', '081234567890', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('F2', 'Laki-laki');
        $sheet->setCellValue('G2', 'guru');
        $sheet->setCellValue('H2', 'VII-A');
        $sheet->setCellValue('I2', '2015-07-01');
        $sheet->setCellValue('J2', 'S1 Pendidikan Agama Islam');

        // Sample Data 2
        $sheet->setCellValue('A3', 'Ustadzah Siti Aminah, S.Pd.');
        $sheet->setCellValueExplicit('B3', '199003202015022001', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('C3', 'siti.aminah@sekolah.sch.id');
        $sheet->setCellValue('D3', 'guru123');
        $sheet->setCellValueExplicit('E3', '085712345678', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('F3', 'Perempuan');
        $sheet->setCellValue('G3', 'guru');
        $sheet->setCellValue('H3', 'VIII-B');
        $sheet->setCellValue('I3', '2018-01-15');
        $sheet->setCellValue('J2', 'S1 Pendidikan Bahasa dan Sastra Indonesia');

        // Sample Data 3 (Staff TU)
        $sheet->setCellValue('A4', 'Muhammad Rizky (Tata Usaha)');
        $sheet->setCellValueExplicit('B4', '199512102020031005', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('C4', 'rizky.tu@sekolah.sch.id');
        $sheet->setCellValue('D4', 'staff123');
        $sheet->setCellValueExplicit('E4', '081398765432', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('F4', 'Laki-laki');
        $sheet->setCellValue('G4', 'tata-usaha');
        $sheet->setCellValue('H4', '');
        $sheet->setCellValue('I4', '2020-03-01');
        $sheet->setCellValue('J4', 'D3 Administrasi Perkantoran');

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Template_Import_Guru_Pegawai.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Import Teacher & Staff Data from Excel (.xlsx, .xls, .csv)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, false, true);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal membaca berkas Excel: ' . $e->getMessage()], 422);
            }
            return back()->with('error', 'Gagal membaca berkas Excel: ' . $e->getMessage());
        }

        if (empty($data) || count($data) < 2) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Berkas Excel kosong atau tidak memiliki baris data.'], 422);
            }
            return back()->with('error', 'Berkas Excel kosong atau tidak memiliki baris data.');
        }

        $columnAliases = [
            'name'           => ['nama_lengkap', 'nama', 'nama guru', 'nama pegawai', 'name', 'nama lengkap'],
            'nip'            => ['nip', 'nrh', 'no_nip', 'nomor induk pegawai', 'nip / nrh', 'nip/nrh', 'nipnrh'],
            'email'          => ['email', 'e-mail', 'surel', 'username', 'email / username', 'email username login', 'email (username login)*', 'emailusernamelogin', 'usernamelogin'],
            'password'       => ['password', 'kata sandi', 'pass', 'sandi', 'pin', 'password (kata sandi)*', 'passwordkatasandi'],
            'phone'          => ['no_hp', 'no hp', 'nohp', 'telepon', 'phone', 'no wa', 'whatsapp', 'no_whatsapp', 'no. whatsapp / hp', 'nowhatsapphp'],
            'gender'         => ['jenis_kelamin', 'jenis kelamin', 'jk', 'gender', 'l/p', 'l_p', 'jenis kelamin (l/p)', 'jeniskelaminlp'],
            'role'           => ['peran', 'jabatan', 'role', 'posisi', 'peran / jabatan', 'peranjabatan'],
            'homeroom'       => ['wali_kelas', 'wali kelas', 'bina_kelas', 'kelas', 'rombel', 'wali kelas (opsional)', 'walikelasopsional'],
            'tmt'            => ['tmt', 'terhitung_mulai_tanggal', 'terhitung mulai tanggal', 'tgl_tmt', 'tanggal_tmt', 'tmt (yyyy-mm-dd)', 'tmtyyyymmdd', 'tmt yyyy-mm-dd'],
            'last_education' => ['pendidikan_terakhir', 'pendidikan terakhir', 'pendidikan', 'ijazah_terakhir', 'ijazah terakhir', 'jenjang_pendidikan', 'pendidikanterakhir'],
        ];

        $normalize = function ($str) {
            $str = strtolower(trim((string)$str));
            return preg_replace('/[^a-z0-9]/', '', $str);
        };

        // Detect header row
        $headerRowIndex = null;
        $matchedColMap = [];
        $rowsArray = array_values($data);

        for ($i = 0; $i < min(5, count($rowsArray)); $i++) {
            $rowCandidate = $rowsArray[$i];
            $currentMatches = [];

            foreach ($rowCandidate as $colLetter => $cellVal) {
                $cellNorm = $normalize($cellVal);
                if (empty($cellNorm)) continue;

                foreach ($columnAliases as $field => $aliases) {
                    if (isset($currentMatches[$field])) continue;

                    foreach ($aliases as $alias) {
                        if ($cellNorm === $normalize($alias)) {
                            $currentMatches[$field] = $colLetter;
                            break;
                        }
                    }

                    // Fallback fuzzy contains
                    if (!isset($currentMatches[$field])) {
                        if ($field === 'email' && (str_contains($cellNorm, 'email') || str_contains($cellNorm, 'username') || str_contains($cellNorm, 'surel'))) {
                            $currentMatches[$field] = $colLetter;
                        } elseif ($field === 'tmt' && (str_contains($cellNorm, 'tmt') || str_contains($cellNorm, 'terhitung'))) {
                            $currentMatches[$field] = $colLetter;
                        } elseif ($field === 'role' && (str_contains($cellNorm, 'peran') || str_contains($cellNorm, 'jabatan') || str_contains($cellNorm, 'posisi'))) {
                            $currentMatches[$field] = $colLetter;
                        } elseif ($field === 'last_education' && (str_contains($cellNorm, 'pendidikan') || str_contains($cellNorm, 'ijazah'))) {
                            $currentMatches[$field] = $colLetter;
                        } elseif ($field === 'password' && (str_contains($cellNorm, 'password') || str_contains($cellNorm, 'sandi'))) {
                            $currentMatches[$field] = $colLetter;
                        } elseif ($field === 'nip' && (str_contains($cellNorm, 'nip') || str_contains($cellNorm, 'nrh'))) {
                            $currentMatches[$field] = $colLetter;
                        } elseif ($field === 'name' && (str_contains($cellNorm, 'nama') || str_contains($cellNorm, 'name'))) {
                            $currentMatches[$field] = $colLetter;
                        }
                    }
                }
            }

            if (isset($currentMatches['name'])) {
                $headerRowIndex = $i;
                $matchedColMap = $currentMatches;
                break;
            }
        }

        if ($headerRowIndex === null || !isset($matchedColMap['name'])) {
            $errMsg = 'Header kolom Excel tidak dikenali. Pastikan kolom "Nama Lengkap", "Email", dan "Password" tersedia.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errMsg], 422);
            }
            return back()->with('error', $errMsg);
        }

        $allRoles = Role::all();
        $defaultRole = $allRoles->firstWhere('slug', 'guru') ?? $allRoles->firstWhere('slug', 'teacher');
        $allClasses = ClassModel::all();

        $imported = 0;
        $createdCount = 0;
        $updatedCount = 0;
        $skipped = 0;

        $dataRows = array_slice($rowsArray, $headerRowIndex + 1);

        foreach ($dataRows as $index => $row) {
            $getVal = function ($field) use ($row, $matchedColMap) {
                if (!isset($matchedColMap[$field])) return null;
                $col = $matchedColMap[$field];
                return isset($row[$col]) ? trim((string)$row[$col]) : null;
            };

            $name = $getVal('name');
            if (empty($name)) {
                $skipped++;
                continue;
            }

            $nip = $getVal('nip');
            $email = $getVal('email');
            $pass = $getVal('password');
            $phone = $getVal('phone');
            $roleInput = $getVal('role');
            $homeroomInput = $getVal('homeroom');
            $tmtInput = $getVal('tmt');
            $lastEduInput = $getVal('last_education');

            // Parse TMT secara fleksibel (serial Excel, YYYY/MM/DD, YYYY-MM-DD, dll.)
            $tmt = $this->parseFlexibleDate($tmtInput);

            // Sanitasi dan format Email/Username
            if (!empty($email)) {
                $email = strtolower(trim($email));
                if (!str_contains($email, '@')) {
                    $email = $email . '@alfahmi.com';
                }
            } else {
                if (!empty($nip)) {
                    $cleanNip = preg_replace('/[^0-9]/', '', $nip);
                    $email = ($cleanNip ?: 'guru') . '@sekolah.sch.id';
                } else {
                    $slugName = Str::slug($name, '');
                    $email = ($slugName ? substr($slugName, 0, 15) : 'guru') . '_' . rand(100, 999) . '@sekolah.sch.id';
                }
            }

            // Default password if empty
            $rawPass = !empty($pass) ? $pass : 'guru123';

            // Smart Detect Role berdasarkan Jabatan pada template Excel
            $targetRole = null;
            if (!empty($roleInput)) {
                $targetRole = $this->smartDetectRole($roleInput, $allRoles);
            }
            if (!$targetRole) {
                $targetRole = $defaultRole;
            }

            DB::beginTransaction();
            try {
                // Temukan user: PRIORITASKAN NIP DAHULU (agar akun sebelumnya bisa dikoreksi email & role-nya), lalu Email
                $existingUser = null;
                if (!empty($nip)) {
                    $existingUser = User::where('nip', $nip)->first();
                }
                if (!$existingUser && !empty($email)) {
                    $existingUser = User::where('email', $email)->first();
                }

                if ($existingUser) {
                    $updateData = [
                        'name' => $name,
                        'email' => $email,
                        'status' => 'active',
                    ];
                    if (!empty($nip)) $updateData['nip'] = $nip;
                    if (!empty($roleInput)) $updateData['jabatan'] = $roleInput;
                    if (!empty($phone)) $updateData['phone'] = $phone;
                    if (!empty($tmt)) $updateData['tmt'] = $tmt;
                    if (!empty($lastEduInput)) $updateData['last_education'] = $lastEduInput;
                    if (!empty($pass)) {
                        $updateData['password'] = Hash::make($pass);
                    }
                    $existingUser->update($updateData);

                    if ($targetRole) {
                        $existingUser->syncRoles([$targetRole]);
                    }
                    $user = $existingUser;
                    $updatedCount++;
                } else {
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'nip' => $nip,
                        'jabatan' => $roleInput ?: null,
                        'phone' => $phone,
                        'tmt' => $tmt,
                        'last_education' => $lastEduInput ?: null,
                        'password' => Hash::make($rawPass),
                        'status' => 'active',
                    ]);

                    if ($targetRole) {
                        $user->assignRole($targetRole);
                    }
                    $createdCount++;
                }

                // Penugasan Wali Kelas jika tercantum
                if (!empty($homeroomInput)) {
                    $cleanHome = $normalize($homeroomInput);
                    $matchedClass = $allClasses->first(function ($c) use ($cleanHome, $normalize) {
                        return $normalize($c->name) === $cleanHome;
                    });
                    if ($matchedClass) {
                        $matchedClass->update(['homeroom_teacher_id' => $user->id]);
                    }
                }

                DB::commit();
                $imported++;
            } catch (\Exception $ex) {
                DB::rollBack();
                $skipped++;
            }
        }

        $msg = "Import Excel selesai! {$imported} data guru & pegawai diproses ({$createdCount} akun baru, {$updatedCount} diperbarui), {$skipped} baris dilewati.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'imported' => $imported,
                'created' => $createdCount,
                'updated' => $updatedCount,
                'skipped' => $skipped,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', $msg);
    }

    /**
     * Smart detector to resolve role based on jabatan text from Excel
     */
    protected function smartDetectRole(string $input, $allRoles): ?Role
    {
        $raw = strtolower(trim($input));
        $norm = preg_replace('/[^a-z0-9]/', '', $raw);

        // 1. Kepala Sekolah
        if (str_contains($raw, 'kepala sekolah') || str_contains($raw, 'kepsek') || str_contains($raw, 'headmaster')) {
            return $allRoles->firstWhere('slug', 'kepala-sekolah') ?? $allRoles->firstWhere('slug', 'kepala_sekolah');
        }

        // 2. Guru Al-Qur'an / Tahfidz / Halaqah
        if (str_contains($raw, 'quran') || str_contains($raw, 'qur\'an') || str_contains($raw, 'tahfidz') || str_contains($raw, 'tahfiz') || str_contains($raw, 'halaqah') || str_contains($raw, 'tilawati') || str_contains($raw, 'ummi')) {
            return $allRoles->firstWhere('slug', 'guru-quran') ?? $allRoles->firstWhere('slug', 'guru_quran');
        }

        // 3. Guru BK / Konseling
        if (str_contains($raw, 'bimbingan konseling') || str_contains($raw, 'guru bk') || str_contains($raw, 'konselor') || $norm === 'bk' || $norm === 'gurubk') {
            return $allRoles->firstWhere('slug', 'guru-bk') ?? $allRoles->firstWhere('slug', 'bk');
        }

        // 4. Bendahara / Keuangan
        if (str_contains($raw, 'bendahara') || str_contains($raw, 'keuangan') || str_contains($raw, 'finance') || str_contains($raw, 'kasir')) {
            return $allRoles->firstWhere('slug', 'bendahara');
        }

        // 5. Kehumasan / Humas
        if (str_contains($raw, 'humas') || str_contains($raw, 'kehumasan') || str_contains($raw, 'public relation')) {
            return $allRoles->firstWhere('slug', 'wakasek-kehumasan') 
                ?? $allRoles->firstWhere('slug', 'staff') 
                ?? $allRoles->firstWhere('slug', 'tata-usaha');
        }

        // 6. Kurikulum
        if (str_contains($raw, 'kurikulum')) {
            return $allRoles->firstWhere('slug', 'wakasek-kurikulum') ?? $allRoles->firstWhere('slug', 'guru');
        }

        // 7. Kesiswaan
        if (str_contains($raw, 'kesiswaan')) {
            return $allRoles->firstWhere('slug', 'wakasek-kesiswaan') ?? $allRoles->firstWhere('slug', 'guru');
        }

        // 8. Staf Admin / Tata Usaha / Operator
        if (str_contains($raw, 'tata usaha') || str_contains($raw, 'administrasi') || str_contains($raw, 'staf admin') || str_contains($raw, 'staff admin') || $norm === 'tu') {
            return $allRoles->firstWhere('slug', 'tata-usaha') ?? $allRoles->firstWhere('slug', 'staff') ?? $allRoles->firstWhere('slug', 'operator');
        }
        if (str_contains($raw, 'operator')) {
            return $allRoles->firstWhere('slug', 'operator') ?? $allRoles->firstWhere('slug', 'admin');
        }

        // 9. Staf umum / security / kebersihan
        if (str_contains($raw, 'staf') || str_contains($raw, 'staff') || str_contains($raw, 'security') || str_contains($raw, 'satpam') || str_contains($raw, 'kebersihan')) {
            return $allRoles->firstWhere('slug', 'staff') ?? $allRoles->firstWhere('slug', 'tata-usaha');
        }

        // 10. Guru / Wali Kelas / Guru Bidang Studi
        if (str_contains($raw, 'guru') || str_contains($raw, 'wali kelas') || str_contains($raw, 'bidang studi') || str_contains($raw, 'mapel') || str_contains($raw, 'pendidik') || str_contains($raw, 'pengajar')) {
            return $allRoles->firstWhere('slug', 'guru') ?? $allRoles->firstWhere('slug', 'teacher');
        }

        // Fallback: match by slug or name in roles
        $directMatch = $allRoles->first(function ($r) use ($norm) {
            $rSlug = preg_replace('/[^a-z0-9]/', '', strtolower($r->slug));
            $rName = preg_replace('/[^a-z0-9]/', '', strtolower($r->name));
            return $rSlug === $norm || $rName === $norm;
        });

        if ($directMatch) {
            return $directMatch;
        }

        return $allRoles->firstWhere('slug', 'guru') ?? $allRoles->firstWhere('slug', 'teacher');
    }

    /**
     * Flexible date parser for Excel dates (serial numbers, YYYY/MM/DD, DD/MM/YYYY, etc.)
     */
    protected function parseFlexibleDate($val): ?string
    {
        if (empty($val)) return null;

        // If numeric (Excel serial date)
        if (is_numeric($val) && (float)$val > 1000) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('Y-m-d');
            } catch (\Throwable $e) {}
        }

        $str = trim((string)$val);
        $str = str_replace(['/', '.'], '-', $str);

        try {
            return \Carbon\Carbon::parse($str)->format('Y-m-d');
        } catch (\Throwable $e) {}

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $str, $m)) {
            return sprintf('%04d-%02d-%02d', (int)$m[1], (int)$m[2], (int)$m[3]);
        }
        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $str, $m)) {
            return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]);
        }

        return null;
    }
}
