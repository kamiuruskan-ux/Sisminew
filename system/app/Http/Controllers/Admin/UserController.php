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

        // Base query excluding students and canteen users
        $query = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('slug', ['student', 'siswa', 'calon-siswa', 'kantin', 'canteen']);
        })->with(['roles', 'homeroomClasses', 'quranClasses']);

        if ($activeTab === 'guru') {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('slug', [
                    'guru', 'teacher', 'guru-quran', 'admin', 'operator', 
                    'tata-usaha', 'staff', 'kepala-sekolah', 'wakasek-kesiswaan', 
                    'wakasek-kurikulum', 'wakasek-kehumasan'
                ]);
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
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
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

        // Sample Data 2
        $sheet->setCellValue('A3', 'Ustadzah Siti Aminah, S.Pd.');
        $sheet->setCellValueExplicit('B3', '199003202015022001', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('C3', 'siti.aminah@sekolah.sch.id');
        $sheet->setCellValue('D3', 'guru123');
        $sheet->setCellValueExplicit('E3', '085712345678', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('F3', 'Perempuan');
        $sheet->setCellValue('G3', 'guru');
        $sheet->setCellValue('H3', 'VIII-B');

        // Sample Data 3 (Staff TU)
        $sheet->setCellValue('A4', 'Muhammad Rizky (Tata Usaha)');
        $sheet->setCellValueExplicit('B4', '199512102020031005', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('C4', 'rizky.tu@sekolah.sch.id');
        $sheet->setCellValue('D4', 'staff123');
        $sheet->setCellValueExplicit('E4', '081398765432', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('F4', 'Laki-laki');
        $sheet->setCellValue('G4', 'tata-usaha');
        $sheet->setCellValue('H4', '');

        foreach (range('A', 'H') as $col) {
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
            'name'      => ['nama_lengkap', 'nama', 'nama guru', 'nama pegawai', 'name', 'nama lengkap'],
            'nip'       => ['nip', 'nrh', 'no_nip', 'nomor induk pegawai', 'nip / nrh', 'nip/nrh'],
            'email'     => ['email', 'e-mail', 'surel', 'username', 'email / username'],
            'password'  => ['password', 'kata sandi', 'pass', 'sandi', 'pin'],
            'phone'     => ['no_hp', 'no hp', 'nohp', 'telepon', 'phone', 'no wa', 'whatsapp', 'no_whatsapp'],
            'gender'    => ['jenis_kelamin', 'jenis kelamin', 'jk', 'gender', 'l/p', 'l_p'],
            'role'      => ['peran', 'jabatan', 'role', 'posisi', 'peran / jabatan'],
            'homeroom'  => ['wali_kelas', 'wali kelas', 'bina_kelas', 'kelas', 'rombel'],
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

            // Fallback email if empty
            if (empty($email)) {
                if (!empty($nip)) {
                    $cleanNip = preg_replace('/[^0-9]/', '', $nip);
                    $email = ($cleanNip ?: 'guru') . '@sekolah.sch.id';
                } else {
                    $slugName = Str::slug($name, '');
                    $email = ($slugName ? substr($slugName, 0, 15) : 'guru') . '_' . rand(100, 999) . '@sekolah.sch.id';
                }
            }
            $email = strtolower(trim($email));

            // Default password if empty
            $rawPass = !empty($pass) ? $pass : 'guru123';

            // Resolve role
            $targetRole = null;
            if (!empty($roleInput)) {
                $cleanRole = $normalize($roleInput);
                $targetRole = $allRoles->first(function ($r) use ($cleanRole, $normalize) {
                    return $normalize($r->slug) === $cleanRole || $normalize($r->name) === $cleanRole;
                });
            }
            if (!$targetRole) {
                $targetRole = $defaultRole;
            }

            DB::beginTransaction();
            try {
                // Find existing user by email or NIP
                $existingUser = null;
                if (!empty($email)) {
                    $existingUser = User::where('email', $email)->first();
                }
                if (!$existingUser && !empty($nip)) {
                    $existingUser = User::where('nip', $nip)->first();
                }

                if ($existingUser) {
                    $updateData = [
                        'name' => $name,
                        'status' => 'active',
                    ];
                    if (!empty($nip)) $updateData['nip'] = $nip;
                    if (!empty($phone)) $updateData['phone'] = $phone;
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
                        'phone' => $phone,
                        'password' => Hash::make($rawPass),
                        'status' => 'active',
                    ]);

                    if ($targetRole) {
                        $user->assignRole($targetRole);
                    }
                    $createdCount++;
                }

                // Homeroom assignment if specified
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
}
