<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::adminOnly()->withCount('users');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $this->ensureSpecializedRolesAndPermissions();

        $roles = $query->latest()->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->ensureSpecializedRolesAndPermissions();
        $permissions = Permission::all()->groupBy('group');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'nullable|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'dashboard_permission' => 'nullable',
        ]);

        $validated['slug'] = $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['name']);

        $role = Role::create($validated);

        // Assign permissions
        $permissionIds = $validated['permissions'] ?? [];
        if (!empty($request->dashboard_permission)) {
            $permissionIds[] = $request->dashboard_permission;
        }
        $role->permissions()->sync(array_unique(array_filter($permissionIds)));

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    public function show(Role $role)
    {
        if ($role->isNonAdminRole()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role ini diperuntukkan untuk portal khusus.');
        }

        return view('admin.roles.show', compact('role'));
    }

    public function edit($encodedId)
    {
        $id = decode_id($encodedId);
        $role = Role::with('permissions')->findOrFail($id);

        if ($role->isSuperAdmin()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role Super Admin adalah role utama sistem dan tidak dapat diubah.');
        }

        if ($role->isNonAdminRole()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role ini diperuntukkan untuk portal khusus dan tidak dikelola melalui Admin Roles.');
        }

        $this->ensureSpecializedRolesAndPermissions();
        $permissions = Permission::all()->groupBy('group');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    private function ensureSpecializedRolesAndPermissions()
    {
        // 1. Specialized Roles
        $rolesToEnsure = [
            'guru-quran' => [
                'name'        => 'Guru Al-Qur\'an',
                'description' => 'Guru Pengampu Halaqah Al-Qur\'an, Tahsin, Tahfidz & Raport Al-Qur\'an',
                'is_active'   => true,
            ],
            'kepala-sekolah' => [
                'name'        => 'Kepala Sekolah',
                'description' => 'Pimpinan Lembaga Pendidikan — Pemantauan operasional, pengesahan izin pegawai, rekap presensi, dan supervisi akademik',
                'is_active'   => true,
            ],
            'wakasek-kesiswaan' => [
                'name'        => 'Wakasek Bidang Kesiswaan',
                'description' => 'Wakil Kepala Sekolah Bidang Kesiswaan — Data siswa, KTS, Bimbingan Konseling, kedisiplinan & presensi santri',
                'is_active'   => true,
            ],
            'wakasek-kurikulum' => [
                'name'        => 'Wakasek Bidang Kurikulum',
                'description' => 'Wakil Kepala Sekolah Bidang Kurikulum — Struktur akademik, mapel, jadwal pembelajaran, LMS/CBT & e-raport digital',
                'is_active'   => true,
            ],
            'wakasek-kehumasan' => [
                'name'        => 'Wakasek Bidang Kehumasan',
                'description' => 'Wakil Kepala Sekolah Bidang Kehumasan — Humas, SPMB, publikasi artikel web, galeri & broadcast WhatsApp',
                'is_active'   => true,
            ],
        ];

        $roleModels = [];
        foreach ($rolesToEnsure as $slug => $data) {
            $r = Role::firstOrCreate(['slug' => $slug], $data);
            if (!$r->is_active) {
                $r->update(['is_active' => true]);
            }
            $roleModels[$slug] = $r;
        }

        // 2. Comprehensive Permissions List
        $permissionsList = [
            // Dashboard
            ['name' => 'View Dashboard Admin',       'slug' => 'view-dashboard-admin',       'group' => 'dashboard'],
            ['name' => 'View Dashboard Bendahara',   'slug' => 'view-dashboard-bendahara',   'group' => 'dashboard'],
            ['name' => 'View Dashboard Guru',        'slug' => 'view-dashboard-guru',        'group' => 'dashboard'],
            ['name' => 'View Dashboard BK',          'slug' => 'view-dashboard-bk',          'group' => 'dashboard'],
            ['name' => 'View Dashboard Operator TU', 'slug' => 'view-dashboard-operator',    'group' => 'dashboard'],
            ['name' => 'View Dashboard Staff',       'slug' => 'view-dashboard-staff',       'group' => 'dashboard'],

            // Master Data Siswa
            ['name' => 'Lihat Data Siswa',            'slug' => 'view-students',              'group' => 'Data Siswa & Kartu'],
            ['name' => 'Tambah Data Siswa',           'slug' => 'create-students',            'group' => 'Data Siswa & Kartu'],
            ['name' => 'Edit Data Siswa',             'slug' => 'edit-students',              'group' => 'Data Siswa & Kartu'],
            ['name' => 'Hapus Data Siswa',            'slug' => 'delete-students',            'group' => 'Data Siswa & Kartu'],
            ['name' => 'Lihat Daftar Alumni',         'slug' => 'view-alumni',                'group' => 'Data Siswa & Kartu'],
            ['name' => 'Cetak Kartu Siswa (KTS)',     'slug' => 'view-student-cards',         'group' => 'Data Siswa & Kartu'],
            ['name' => 'Daftar Face ID Siswa',        'slug' => 'view-face-id',               'group' => 'Data Siswa & Kartu'],

            // Struktur Akademik
            ['name' => 'Lihat Data Kelas',            'slug' => 'view-classes',               'group' => 'Struktur Akademik'],
            ['name' => 'Kelola Data Kelas',           'slug' => 'manage-classes',             'group' => 'Struktur Akademik'],
            ['name' => 'Lihat Data Jurusan',          'slug' => 'view-majors',                'group' => 'Struktur Akademik'],
            ['name' => 'Lihat Mata Pelajaran',        'slug' => 'view-subjects',              'group' => 'Struktur Akademik'],
            ['name' => 'Kelola Mata Pelajaran',       'slug' => 'manage-subjects',            'group' => 'Struktur Akademik'],
            ['name' => 'Lihat Jadwal Pelajaran',      'slug' => 'view-schedules',             'group' => 'Struktur Akademik'],
            ['name' => 'Kelola Jadwal Pelajaran',     'slug' => 'manage-schedules',           'group' => 'Struktur Akademik'],
            ['name' => 'Lihat Tahun Akademik',        'slug' => 'view-academic-years',        'group' => 'Struktur Akademik'],
            ['name' => 'Kelola Tahun Akademik',       'slug' => 'manage-academic-years',      'group' => 'Struktur Akademik'],
            ['name' => 'Perpustakaan Digital',        'slug' => 'view-library',               'group' => 'Struktur Akademik'],

            // Pembelajaran (LMS & CBT)
            ['name' => 'Akses Modul LMS & CBT',       'slug' => 'view-learning',              'group' => 'Akademik LMS & CBT'],
            ['name' => 'LMS & Live Class',            'slug' => 'view-lms',                   'group' => 'Akademik LMS & CBT'],
            ['name' => 'Lihat Materi & Modul Ajar',   'slug' => 'view-materials',             'group' => 'Akademik LMS & CBT'],
            ['name' => 'Kelola Materi & Modul Ajar',  'slug' => 'manage-materials',           'group' => 'Akademik LMS & CBT'],
            ['name' => 'Lihat Tugas Siswa',           'slug' => 'view-assignments',           'group' => 'Akademik LMS & CBT'],
            ['name' => 'Kelola Tugas Siswa',          'slug' => 'manage-assignments',         'group' => 'Akademik LMS & CBT'],
            ['name' => 'Lihat Ujian CBT Online',      'slug' => 'view-exams',                 'group' => 'Akademik LMS & CBT'],
            ['name' => 'Kelola Soal & Ujian CBT',     'slug' => 'manage-exams',               'group' => 'Akademik LMS & CBT'],
            ['name' => 'Kelola Server CBT',           'slug' => 'manage-cbt-server',          'group' => 'Akademik LMS & CBT'],

            // Halaqah Al-Qur'an (Tahsin & Tahfidz)
            ['name' => 'Lihat Halaqah Al-Qur\'an',    'slug' => 'view-halaqah',               'group' => 'Halaqah Al-Qur\'an'],
            ['name' => 'Kelola Halaqah & Mutaba\'ah', 'slug' => 'manage-halaqah',             'group' => 'Halaqah Al-Qur\'an'],
            ['name' => 'Lihat Raport Al-Qur\'an',     'slug' => 'view-quran-raport',          'group' => 'Halaqah Al-Qur\'an'],
            ['name' => 'Kelola & Cetak Raport Qur\'an','slug' => 'manage-quran-raport',       'group' => 'Halaqah Al-Qur\'an'],

            // E-Raport Digital
            ['name' => 'Lihat Raport Mapel Umum',     'slug' => 'view-raport',                'group' => 'E-Raport Digital'],
            ['name' => 'Entri Nilai Siswa',           'slug' => 'view-grades',                'group' => 'E-Raport Digital'],
            ['name' => 'Kelola & Kunci Nilai Raport', 'slug' => 'manage-grades',              'group' => 'E-Raport Digital'],
            ['name' => 'Pengaturan Template Raport',  'slug' => 'manage-raport-settings',     'group' => 'E-Raport Digital'],

            // Presensi Pegawai & Guru
            ['name' => 'Presensi Guru & Tendik',      'slug' => 'view-teacher-attendance',    'group' => 'Presensi Pegawai & Guru'],
            ['name' => 'Input Manual Presensi Guru',  'slug' => 'manage-teacher-attendance',  'group' => 'Presensi Pegawai & Guru'],
            ['name' => 'Lihat Izin & Cuti Pegawai',   'slug' => 'view-employee-permits',      'group' => 'Presensi Pegawai & Guru'],
            ['name' => 'Verifikasi Izin Pegawai',     'slug' => 'approve-employee-permits',   'group' => 'Presensi Pegawai & Guru'],
            ['name' => 'Rekap Bulanan Guru',          'slug' => 'view-teacher-recap',         'group' => 'Presensi Pegawai & Guru'],
            ['name' => 'Pengaturan Presensi Guru',    'slug' => 'manage-teacher-attendance-settings', 'group' => 'Presensi Pegawai & Guru'],
            ['name' => 'Tugas & Checklist Pegawai',   'slug' => 'view-employee-tasks',        'group' => 'Presensi Pegawai & Guru'],

            // Presensi Siswa
            ['name' => 'Rekap Presensi Siswa',        'slug' => 'view-student-attendance',    'group' => 'Presensi Siswa'],
            ['name' => 'Kelola Presensi Siswa',       'slug' => 'manage-student-attendance',  'group' => 'Presensi Siswa'],
            ['name' => 'Permohonan Izin Siswa',       'slug' => 'view-student-permits',       'group' => 'Presensi Siswa'],
            ['name' => 'Pengaturan Presensi Siswa',   'slug' => 'manage-student-attendance-settings', 'group' => 'Presensi Siswa'],
            ['name' => 'Terminal Presensi Siswa (QR)','slug' => 'view-terminal-attendance',    'group' => 'Presensi Siswa'],

            // Bimbingan & Konseling
            ['name' => 'Akses Modul BK',              'slug' => 'view-bk',                    'group' => 'Bimbingan & Konseling'],
            ['name' => 'Sesi Konseling Siswa',        'slug' => 'view-bk-counseling',         'group' => 'Bimbingan & Konseling'],
            ['name' => 'Kelola Sesi Konseling',       'slug' => 'manage-bk',                  'group' => 'Bimbingan & Konseling'],
            ['name' => 'Pelanggaran & SP Siswa',      'slug' => 'view-bk-violations',         'group' => 'Bimbingan & Konseling'],
            ['name' => 'Kategori & Poin Pelanggaran', 'slug' => 'manage-bk-categories',       'group' => 'Bimbingan & Konseling'],
            ['name' => 'Asesmen & Minat Bakat',       'slug' => 'view-bk-assessments',        'group' => 'Bimbingan & Konseling'],

            // Penerimaan Siswa (SPMB)
            ['name' => 'Lihat Pendaftaran SPMB',      'slug' => 'view-spmb',                  'group' => 'Penerimaan Siswa (SPMB)'],
            ['name' => 'Verifikasi Berkas SPMB',      'slug' => 'verify-spmb',                'group' => 'Penerimaan Siswa (SPMB)'],
            ['name' => 'Kelola Gelombang SPMB',       'slug' => 'manage-spmb-waves',          'group' => 'Penerimaan Siswa (SPMB)'],
            ['name' => 'Pengaturan Biaya & SPMB',     'slug' => 'manage-spmb-settings',       'group' => 'Penerimaan Siswa (SPMB)'],

            // Keuangan & Kas
            ['name' => 'Akses Modul Keuangan',        'slug' => 'view-financial',             'group' => 'Keuangan & Kas'],
            ['name' => 'Pembayaran Siswa (POS SPP)',  'slug' => 'view-student-payments',      'group' => 'Keuangan & Kas'],
            ['name' => 'Tabungan Santri',             'slug' => 'view-savings',               'group' => 'Keuangan & Kas'],
            ['name' => 'Jurnal Kas Masuk & Keluar',   'slug' => 'view-financial-transactions','group' => 'Keuangan & Kas'],
            ['name' => 'Kelola Transaksi Keuangan',   'slug' => 'manage-financial',           'group' => 'Keuangan & Kas'],
            ['name' => 'Setting Tarif & Pos Tagihan', 'slug' => 'manage-payment-bills',       'group' => 'Keuangan & Kas'],
            ['name' => 'Tracking Tunggakan Siswa',    'slug' => 'view-payment-tracking',      'group' => 'Keuangan & Kas'],
            ['name' => 'Laporan Arus Kas Keuangan',   'slug' => 'view-financial-reports',     'group' => 'Keuangan & Kas'],

            // Digital E-Kantin
            ['name' => 'Akses Admin E-Kantin',        'slug' => 'view-canteen-admin',         'group' => 'Digital E-Kantin'],
            ['name' => 'Kelola Produk & Menu Kantin', 'slug' => 'manage-canteen-admin',       'group' => 'Digital E-Kantin'],

            // Publikasi & Konten Web
            ['name' => 'Lihat Berita & Artikel',      'slug' => 'view-posts',                 'group' => 'Publikasi & Informasi Web'],
            ['name' => 'Tulis & Kelola Berita',       'slug' => 'manage-posts',               'group' => 'Publikasi & Informasi Web'],
            ['name' => 'Lihat Pengumuman & Agenda',   'slug' => 'view-announcements',         'group' => 'Publikasi & Informasi Web'],
            ['name' => 'Kelola Pengumuman',           'slug' => 'manage-announcements',       'group' => 'Publikasi & Informasi Web'],
            ['name' => 'Lihat Galeri Foto',           'slug' => 'view-gallery',               'group' => 'Publikasi & Informasi Web'],
            ['name' => 'Kelola Galeri Foto',          'slug' => 'manage-gallery',             'group' => 'Publikasi & Informasi Web'],
            ['name' => 'Kelola Banner Slider Web',    'slug' => 'manage-sliders',             'group' => 'Publikasi & Informasi Web'],
            ['name' => 'Kelola Program & Kurikulum',  'slug' => 'manage-curriculum',          'group' => 'Publikasi & Informasi Web'],
            ['name' => 'Kelola Ekstrakurikuler Web',  'slug' => 'manage-extracurriculars',    'group' => 'Publikasi & Informasi Web'],
            ['name' => 'Broadcast Pesan WhatsApp',    'slug' => 'view-broadcast',             'group' => 'Publikasi & Informasi Web'],
            ['name' => 'Pusat Notifikasi Sistem',     'slug' => 'view-notifications',         'group' => 'Publikasi & Informasi Web'],

            // Pengaturan Sistem
            ['name' => 'Lihat Data Guru & Pegawai',   'slug' => 'view-users',                 'group' => 'Pengaturan Sistem'],
            ['name' => 'Kelola Akun Guru & Pegawai',  'slug' => 'manage-users',               'group' => 'Pengaturan Sistem'],
            ['name' => 'Lihat Role & Hak Akses',      'slug' => 'view-roles',                 'group' => 'Pengaturan Sistem'],
            ['name' => 'Kelola Role & Hak Akses',     'slug' => 'manage-roles',               'group' => 'Pengaturan Sistem'],
            ['name' => 'Lihat Konfigurasi Website',   'slug' => 'view-settings',              'group' => 'Pengaturan Sistem'],
            ['name' => 'Ubah Konfigurasi Website',    'slug' => 'edit-settings',              'group' => 'Pengaturan Sistem'],
            ['name' => 'Keamanan Sistem & Login Log', 'slug' => 'view-security',              'group' => 'Pengaturan Sistem'],
            ['name' => 'Database Maintenance & Backup','slug' => 'manage-database-maintenance','group' => 'Pengaturan Sistem'],
        ];

        foreach ($permissionsList as $p) {
            Permission::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // 3. Assign Default Presets to New Roles If Empty
        $all = Permission::all();
        $getIds = fn(array $slugs) => $all->whereIn('slug', $slugs)->pluck('id')->toArray();

        foreach ($roleModels as $slug => $r) {
            if ($r->permissions()->count() === 0) {
                if ($slug === 'guru-quran') {
                    $r->permissions()->sync($getIds([
                        'view-dashboard-guru', 'view-students',
                        'view-halaqah', 'manage-halaqah', 'view-quran-raport', 'manage-quran-raport',
                        'view-announcements', 'view-notifications',
                    ]));
                } elseif ($slug === 'kepala-sekolah') {
                    $r->permissions()->sync($getIds([
                        'view-dashboard-admin', 'view-users', 'view-students', 'view-alumni', 'view-student-cards',
                        'view-classes', 'view-majors', 'view-subjects', 'view-schedules', 'view-academic-years', 'view-library',
                        'view-learning', 'view-lms', 'view-materials', 'view-assignments', 'view-exams',
                        'view-halaqah', 'view-quran-raport', 'view-raport', 'view-grades',
                        'view-teacher-attendance', 'view-employee-permits', 'approve-employee-permits', 'view-teacher-recap', 'view-employee-tasks',
                        'view-student-attendance', 'view-student-permits',
                        'view-bk', 'view-bk-counseling', 'view-bk-violations', 'view-bk-assessments',
                        'view-spmb', 'verify-spmb',
                        'view-financial', 'view-student-payments', 'view-savings', 'view-financial-transactions', 'view-payment-tracking', 'view-financial-reports',
                        'view-posts', 'view-announcements', 'view-gallery', 'view-broadcast', 'view-notifications',
                        'view-settings',
                    ]));
                } elseif ($slug === 'wakasek-kurikulum') {
                    $r->permissions()->sync($getIds([
                        'view-dashboard-admin', 'view-users', 'view-students', 'view-alumni',
                        'view-classes', 'manage-classes', 'view-majors', 'view-subjects', 'manage-subjects', 'view-schedules', 'manage-schedules', 'view-academic-years', 'manage-academic-years', 'view-library',
                        'view-learning', 'view-lms', 'view-materials', 'manage-materials', 'view-assignments', 'manage-assignments', 'view-exams', 'manage-exams', 'manage-cbt-server',
                        'view-halaqah', 'manage-halaqah', 'view-quran-raport', 'manage-quran-raport',
                        'view-raport', 'view-grades', 'manage-grades', 'manage-raport-settings',
                        'manage-curriculum', 'view-announcements', 'create-announcements', 'view-notifications',
                    ]));
                } elseif ($slug === 'wakasek-kesiswaan') {
                    $r->permissions()->sync($getIds([
                        'view-dashboard-admin', 'view-students', 'create-students', 'edit-students', 'view-alumni', 'view-student-cards', 'view-face-id',
                        'view-classes',
                        'view-student-attendance', 'manage-student-attendance', 'view-student-permits', 'manage-student-attendance-settings', 'view-terminal-attendance',
                        'view-bk', 'manage-bk', 'view-bk-counseling', 'view-bk-violations', 'manage-bk-categories', 'view-bk-assessments',
                        'manage-extracurriculars', 'view-spmb', 'verify-spmb',
                        'view-announcements', 'create-announcements', 'view-notifications',
                    ]));
                } elseif ($slug === 'wakasek-kehumasan') {
                    $r->permissions()->sync($getIds([
                        'view-dashboard-staff',
                        'view-spmb', 'create-spmb', 'verify-spmb', 'manage-spmb-waves', 'manage-spmb-settings',
                        'view-posts', 'manage-posts', 'view-announcements', 'manage-announcements',
                        'view-gallery', 'manage-gallery', 'manage-sliders', 'manage-curriculum', 'manage-extracurriculars',
                        'view-broadcast', 'view-notifications',
                    ]));
                }
            }
        }
    }

    public function update(Request $request, $encodedId)
    {
        $id = decode_id($encodedId);
        $role = Role::findOrFail($id);

        if ($role->isSuperAdmin()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role Super Admin adalah role utama sistem dan tidak dapat diubah.');
        }

        if ($role->isNonAdminRole()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role ini diperuntukkan untuk portal khusus dan tidak dikelola melalui Admin Roles.');
        }

        if ($role->isProtectedSystemRole()) {
            // Keep original name and slug for system protected roles
            $validated = $request->validate([
                'description' => 'nullable|string',
                'permissions' => 'nullable|array',
                'dashboard_permission' => 'nullable',
            ]);
            $validated['name'] = $role->name;
            $validated['slug'] = $role->slug;
        } else {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
                'slug' => 'nullable|string|max:255|unique:roles,slug,' . $role->id,
                'description' => 'nullable|string',
                'permissions' => 'nullable|array',
                'dashboard_permission' => 'nullable',
            ]);
            $validated['slug'] = $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['name']);
        }

        $role->update($validated);

        // Sync permissions
        $permissionIds = $validated['permissions'] ?? [];
        if (!empty($request->dashboard_permission)) {
            $permissionIds[] = $request->dashboard_permission;
        }
        $role->permissions()->sync(array_unique(array_filter($permissionIds)));

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy($encodedId)
    {
        $id = decode_id($encodedId);
        $role = Role::findOrFail($id);
        
        // Prevent deleting protected system roles
        if ($role->isProtectedSystemRole()) {
            return back()->with('error', 'Role bawaan sistem tidak dapat dihapus.');
        }

        if ($role->isNonAdminRole()) {
            return back()->with('error', 'Role portal khusus tidak dapat dihapus melalui Admin Roles.');
        }

        $role->delete();

        return back()->with('success', 'Role berhasil dihapus.');
    }
}
