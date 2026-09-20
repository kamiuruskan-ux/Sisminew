<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('roles') || !Schema::hasTable('permissions')) {
            return;
        }

        // 1. Create / Ensure 5 Specialized School Roles
        $newRoles = [
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
        foreach ($newRoles as $slug => $data) {
            $roleModels[$slug] = Role::firstOrCreate(['slug' => $slug], $data);
            if (!$roleModels[$slug]->is_active) {
                $roleModels[$slug]->update(['is_active' => true]);
            }
        }

        // 2. Register Granular Sub-Menu Permissions
        $granularPermissions = [
            // --- Master Data Siswa & Kartu ---
            ['name' => 'Lihat Data Siswa',            'slug' => 'view-students',          'group' => 'students', 'description' => 'Akses halaman data siswa aktif'],
            ['name' => 'Tambah Data Siswa',           'slug' => 'create-students',        'group' => 'students', 'description' => 'Tambah pendaftaran siswa baru'],
            ['name' => 'Edit Data Siswa',             'slug' => 'edit-students',          'group' => 'students', 'description' => 'Edit biodata siswa'],
            ['name' => 'Hapus Data Siswa',            'slug' => 'delete-students',        'group' => 'students', 'description' => 'Hapus data siswa'],
            ['name' => 'Lihat Daftar Alumni',         'slug' => 'view-alumni',            'group' => 'students', 'description' => 'Akses sub-menu data alumni & kelulusan'],
            ['name' => 'Cetak Kartu Siswa (KTS)',     'slug' => 'view-student-cards',     'group' => 'students', 'description' => 'Akses sub-menu cetak kartu tanda siswa'],
            ['name' => 'Daftar Face ID Siswa',        'slug' => 'view-face-id',           'group' => 'students', 'description' => 'Akses sub-menu perekaman Face ID siswa'],

            // --- Struktur Akademik ---
            ['name' => 'Lihat Data Kelas / Rombel',   'slug' => 'view-classes',           'group' => 'classes', 'description' => 'Akses data rombongan belajar'],
            ['name' => 'Kelola Data Kelas',           'slug' => 'manage-classes',         'group' => 'classes', 'description' => 'Tambah, edit, dan hapus kelas'],
            ['name' => 'Lihat Data Jurusan',          'slug' => 'view-majors',            'group' => 'academic-structure', 'description' => 'Akses data jurusan kejuruan'],
            ['name' => 'Lihat Mata Pelajaran',        'slug' => 'view-subjects',          'group' => 'academic-structure', 'description' => 'Akses sub-menu mata pelajaran'],
            ['name' => 'Kelola Mata Pelajaran',       'slug' => 'manage-subjects',        'group' => 'academic-structure', 'description' => 'Tambah & edit mata pelajaran'],
            ['name' => 'Lihat Jadwal Pelajaran',      'slug' => 'view-schedules',         'group' => 'academic-structure', 'description' => 'Akses sub-menu jadwal pelajaran'],
            ['name' => 'Kelola Jadwal Pelajaran',     'slug' => 'manage-schedules',       'group' => 'academic-structure', 'description' => 'Atur plotting jadwal mengajar'],
            ['name' => 'Lihat Tahun Akademik',        'slug' => 'view-academic-years',    'group' => 'academic-structure', 'description' => 'Akses tahun akademik / semester'],
            ['name' => 'Kelola Tahun Akademik',       'slug' => 'manage-academic-years',  'group' => 'academic-structure', 'description' => 'Atur semester aktif sekolah'],
            ['name' => 'Perpustakaan Digital',        'slug' => 'view-library',           'group' => 'academic-structure', 'description' => 'Akses katalog buku perpustakaan'],

            // --- Akademik, LMS & CBT ---
            ['name' => 'Akses Modul LMS & CBT',       'slug' => 'view-learning',          'group' => 'learning', 'description' => 'Akses navigasi modul pembelajaran'],
            ['name' => 'LMS & Live Class',            'slug' => 'view-lms',               'group' => 'learning', 'description' => 'Akses sub-menu kursus dan bab pembelajaran'],
            ['name' => 'Lihat Materi & Modul Ajar',   'slug' => 'view-materials',         'group' => 'learning', 'description' => 'Akses sub-menu modul ajar & materi'],
            ['name' => 'Kelola Materi & Modul Ajar',  'slug' => 'manage-materials',       'group' => 'learning', 'description' => 'Upload & kelola file materi'],
            ['name' => 'Lihat Tugas Siswa',           'slug' => 'view-assignments',       'group' => 'learning', 'description' => 'Akses sub-menu penugasan siswa'],
            ['name' => 'Kelola Tugas Siswa',          'slug' => 'manage-assignments',     'group' => 'learning', 'description' => 'Buat tugas & beri penilaian'],
            ['name' => 'Lihat Ujian CBT Online',      'slug' => 'view-exams',             'group' => 'learning', 'description' => 'Akses sub-menu ujian online CBT'],
            ['name' => 'Kelola Soal & Ujian CBT',     'slug' => 'manage-exams',           'group' => 'learning', 'description' => 'Buat paket soal & jadwalkan ujian'],
            ['name' => 'Kelola Server CBT',           'slug' => 'manage-cbt-server',      'group' => 'learning', 'description' => 'Pengaturan kapasitas & server ujian'],

            // --- Halaqah Al-Qur'an ---
            ['name' => 'Lihat Halaqah Al-Qur\'an',    'slug' => 'view-halaqah',           'group' => 'halaqah', 'description' => 'Akses menu Halaqah Tahsin & Tahfidz'],
            ['name' => 'Kelola Halaqah & Mutaba\'ah', 'slug' => 'manage-halaqah',         'group' => 'halaqah', 'description' => 'Kelola kelompok santri & mutaba\'ah setoran hafalan'],
            ['name' => 'Lihat Raport Al-Qur\'an',     'slug' => 'view-quran-raport',      'group' => 'halaqah', 'description' => 'Akses nilai & buku raport Al-Qur\'an'],
            ['name' => 'Kelola & Cetak Raport Qur\'an','slug' => 'manage-quran-raport',   'group' => 'halaqah', 'description' => 'Entri nilai tajwid/makhraj & cetak raport tahfidz'],

            // --- E-Raport Digital ---
            ['name' => 'Lihat Raport Mapel Umum',     'slug' => 'view-raport',            'group' => 'raport', 'description' => 'Akses buku raport akademik umum'],
            ['name' => 'Entri Nilai Siswa',           'slug' => 'view-grades',            'group' => 'raport', 'description' => 'Akses form entri nilai rapor mapel'],
            ['name' => 'Kelola & Kunci Nilai Raport', 'slug' => 'manage-grades',          'group' => 'raport', 'description' => 'Kunci atau finalisasi nilai rapor'],
            ['name' => 'Pengaturan Template Raport',  'slug' => 'manage-raport-settings', 'group' => 'raport', 'description' => 'Atur KKM, bobot nilai, dan format raport'],

            // --- Presensi Pegawai & Guru ---
            ['name' => 'Presensi Guru & Tendik',      'slug' => 'view-teacher-attendance','group' => 'teacher-attendance', 'description' => 'Akses monitoring presensi pegawai'],
            ['name' => 'Input Manual Presensi Guru',  'slug' => 'manage-teacher-attendance','group' => 'teacher-attendance', 'description' => 'Input koreksi presensi manual pegawai'],
            ['name' => 'Lihat Izin & Cuti Pegawai',   'slug' => 'view-employee-permits',  'group' => 'teacher-attendance', 'description' => 'Akses daftar permohonan izin/cuti pegawai'],
            ['name' => 'Verifikasi Izin Pegawai',     'slug' => 'approve-employee-permits','group' => 'teacher-attendance', 'description' => 'Persetujuan / verifikasi surat izin pegawai'],
            ['name' => 'Rekap Bulanan Guru',          'slug' => 'view-teacher-recap',     'group' => 'teacher-attendance', 'description' => 'Akses rekapitulasi presensi bulanan pegawai'],
            ['name' => 'Pengaturan Presensi Guru',    'slug' => 'manage-teacher-attendance-settings', 'group' => 'teacher-attendance', 'description' => 'Pengaturan geofence GPS, jam kerja & toleransi'],
            ['name' => 'Tugas & Checklist Pegawai',   'slug' => 'view-employee-tasks',    'group' => 'teacher-attendance', 'description' => 'Monitoring pembagian tugas harian staf'],

            // --- Presensi Siswa ---
            ['name' => 'Rekap Presensi Siswa',        'slug' => 'view-student-attendance','group' => 'student-attendance', 'description' => 'Akses daftar presensi harian siswa'],
            ['name' => 'Kelola Presensi Siswa',       'slug' => 'manage-student-attendance','group' => 'student-attendance', 'description' => 'Input & koreksi presensi siswa'],
            ['name' => 'Permohonan Izin Siswa',       'slug' => 'view-student-permits',   'group' => 'student-attendance', 'description' => 'Kelola surat izin & sakit siswa'],
            ['name' => 'Pengaturan Presensi Siswa',   'slug' => 'manage-student-attendance-settings', 'group' => 'student-attendance', 'description' => 'Atur jam & sesi presensi siswa'],
            ['name' => 'Terminal Presensi Siswa (QR)','slug' => 'view-terminal-attendance','group' => 'student-attendance', 'description' => 'Akses terminal scanner kartu QR siswa'],

            // --- Bimbingan & Konseling (BK) ---
            ['name' => 'Akses Modul BK',              'slug' => 'view-bk',                'group' => 'bk', 'description' => 'Akses umum modul bimbingan konseling'],
            ['name' => 'Sesi Konseling Siswa',        'slug' => 'view-bk-counseling',     'group' => 'bk', 'description' => 'Akses catatan sesi konseling santri'],
            ['name' => 'Kelola Sesi Konseling',       'slug' => 'manage-bk',              'group' => 'bk', 'description' => 'Buat & tindak lanjuti kasus konseling'],
            ['name' => 'Pelanggaran & SP Siswa',      'slug' => 'view-bk-violations',     'group' => 'bk', 'description' => 'Akses pencatatan pelanggaran siswa & SP'],
            ['name' => 'Kategori & Poin Pelanggaran', 'slug' => 'manage-bk-categories',   'group' => 'bk', 'description' => 'Atur tata tertib & tarif poin disiplin'],
            ['name' => 'Asesmen & Minat Bakat',       'slug' => 'view-bk-assessments',    'group' => 'bk', 'description' => 'Akses psikotes dan pemetaan potensi santri'],

            // --- Penerimaan Siswa Baru (SPMB) ---
            ['name' => 'Lihat Pendaftaran SPMB',      'slug' => 'view-spmb',              'group' => 'spmb', 'description' => 'Akses daftar calon santri baru'],
            ['name' => 'Verifikasi Berkas SPMB',      'slug' => 'verify-spmb',            'group' => 'spmb', 'description' => 'Validasi berkas & penentuan kelulusan'],
            ['name' => 'Kelola Gelombang SPMB',       'slug' => 'manage-spmb-waves',      'group' => 'spmb', 'description' => 'Atur jadwal & kuota gelombang SPMB'],
            ['name' => 'Pengaturan Biaya & SPMB',     'slug' => 'manage-spmb-settings',   'group' => 'spmb', 'description' => 'Konfigurasi formulir dan biaya seleksi'],

            // --- Keuangan & Kas ---
            ['name' => 'Akses Modul Keuangan',        'slug' => 'view-financial',         'group' => 'financial', 'description' => 'Akses umum navigasi keuangan sekolah'],
            ['name' => 'Pembayaran Siswa (POS SPP)',  'slug' => 'view-student-payments',  'group' => 'financial', 'description' => 'Akses kasir pembayaran SPP & tagihan'],
            ['name' => 'Tabungan Santri',             'slug' => 'view-savings',           'group' => 'financial', 'description' => 'Akses setor & tarik tabungan santri'],
            ['name' => 'Jurnal Kas Masuk & Keluar',   'slug' => 'view-financial-transactions', 'group' => 'financial', 'description' => 'Akses buku kas operasional sekolah'],
            ['name' => 'Kelola Transaksi Keuangan',   'slug' => 'manage-financial',       'group' => 'financial', 'description' => 'Input kas masuk/keluar & mutasi buku kas'],
            ['name' => 'Setting Tarif & Pos Tagihan', 'slug' => 'manage-payment-bills',   'group' => 'financial', 'description' => 'Atur besaran SPP, uang gedung, dll.'],
            ['name' => 'Tracking Tunggakan Siswa',    'slug' => 'view-payment-tracking',  'group' => 'financial', 'description' => 'Monitoring rekap santri menunggak'],
            ['name' => 'Laporan Arus Kas Keuangan',   'slug' => 'view-financial-reports', 'group' => 'financial', 'description' => 'Export laporan keuangan & laba rugi'],

            // --- Digital E-Kantin ---
            ['name' => 'Akses Admin E-Kantin',        'slug' => 'view-canteen-admin',     'group' => 'canteen-admin', 'description' => 'Akses panel kantin sekolah'],
            ['name' => 'Kelola Produk & Menu Kantin', 'slug' => 'manage-canteen-admin',   'group' => 'canteen-admin', 'description' => 'Manajemen menu, pesanan & saldo kantin'],

            // --- Publikasi & Website ---
            ['name' => 'Lihat Berita & Artikel',      'slug' => 'view-posts',             'group' => 'content', 'description' => 'Akses daftar berita & artikel website'],
            ['name' => 'Tulis & Kelola Berita',       'slug' => 'manage-posts',           'group' => 'content', 'description' => 'Buat, edit, dan publikasikan artikel'],
            ['name' => 'Lihat Pengumuman & Agenda',   'slug' => 'view-announcements',     'group' => 'content', 'description' => 'Akses daftar pengumuman sekolah'],
            ['name' => 'Kelola Pengumuman',           'slug' => 'manage-announcements',   'group' => 'content', 'description' => 'Buat & terbitkan agenda/pengumuman'],
            ['name' => 'Lihat Galeri Foto',           'slug' => 'view-gallery',           'group' => 'content', 'description' => 'Akses album galeri dokumentasi'],
            ['name' => 'Kelola Galeri Foto',          'slug' => 'manage-gallery',         'group' => 'content', 'description' => 'Upload & hapus foto dokumentasi'],
            ['name' => 'Kelola Banner Slider Web',    'slug' => 'manage-sliders',         'group' => 'content', 'description' => 'Atur foto slider di beranda website'],
            ['name' => 'Kelola Program & Kurikulum',  'slug' => 'manage-curriculum',      'group' => 'content', 'description' => 'Atur deskripsi program unggulan di web'],
            ['name' => 'Kelola Ekstrakurikuler Web',  'slug' => 'manage-extracurriculars','group' => 'content', 'description' => 'Atur profil ekskul santri di web'],
            ['name' => 'Broadcast Pesan WhatsApp',    'slug' => 'view-broadcast',         'group' => 'content', 'description' => 'Akses modul broadcast pesan massal WA'],
            ['name' => 'Pusat Notifikasi Sistem',     'slug' => 'view-notifications',     'group' => 'content', 'description' => 'Akses rekap log notifikasi sistem'],

            // --- Pengaturan Sistem & Keamanan ---
            ['name' => 'Lihat Data Guru & Pegawai',   'slug' => 'view-users',             'group' => 'settings', 'description' => 'Akses daftar akun pendidik & tenaga kependidikan'],
            ['name' => 'Kelola Akun Guru & Pegawai',  'slug' => 'manage-users',           'group' => 'settings', 'description' => 'Buat, edit data, dan reset password pegawai'],
            ['name' => 'Lihat Role & Hak Akses',      'slug' => 'view-roles',             'group' => 'settings', 'description' => 'Akses manajemen role dan izin akses'],
            ['name' => 'Kelola Role & Hak Akses',     'slug' => 'manage-roles',            'group' => 'settings', 'description' => 'Ubah hak akses dan kewenangan modul role'],
            ['name' => 'Lihat Konfigurasi Website',   'slug' => 'view-settings',          'group' => 'settings', 'description' => 'Akses halaman pengaturan website'],
            ['name' => 'Ubah Konfigurasi Website',    'slug' => 'edit-settings',          'group' => 'settings', 'description' => 'Simpan perubahan profil dan pengaturan web'],
            ['name' => 'Keamanan Sistem & Login Log', 'slug' => 'view-security',          'group' => 'settings', 'description' => 'Monitoring keamanan dan audit login'],
            ['name' => 'Database Maintenance & Backup','slug' => 'manage-database-maintenance', 'group' => 'settings', 'description' => 'Backup database dan jalankan migrasi'],
        ];

        foreach ($granularPermissions as $p) {
            Permission::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // 3. Assign Default Preset Permissions to New Roles
        $allPermissions = Permission::all();
        $getPermIds = fn(array $slugs) => $allPermissions->whereIn('slug', $slugs)->pluck('id')->toArray();

        // Preset: Guru Al-Qur'an
        if (isset($roleModels['guru-quran'])) {
            $guruQuranPerms = $getPermIds([
                'view-dashboard-guru',
                'view-students',
                'view-halaqah',
                'manage-halaqah',
                'view-quran-raport',
                'manage-quran-raport',
                'view-announcements',
                'view-notifications',
            ]);
            $roleModels['guru-quran']->permissions()->sync($guruQuranPerms);
        }

        // Preset: Kepala Sekolah
        if (isset($roleModels['kepala-sekolah'])) {
            $kepsekPerms = $getPermIds([
                'view-dashboard-admin',
                'view-users',
                'view-students', 'view-alumni', 'view-student-cards',
                'view-classes', 'view-majors', 'view-subjects', 'view-schedules', 'view-academic-years', 'view-library',
                'view-learning', 'view-lms', 'view-materials', 'view-assignments', 'view-exams',
                'view-halaqah', 'view-quran-raport',
                'view-raport', 'view-grades',
                'view-teacher-attendance', 'view-employee-permits', 'approve-employee-permits', 'view-teacher-recap', 'view-employee-tasks',
                'view-student-attendance', 'view-student-permits',
                'view-bk', 'view-bk-counseling', 'view-bk-violations', 'view-bk-assessments',
                'view-spmb', 'verify-spmb',
                'view-financial', 'view-student-payments', 'view-savings', 'view-financial-transactions', 'view-payment-tracking', 'view-financial-reports',
                'view-posts', 'view-announcements', 'view-gallery', 'view-broadcast', 'view-notifications',
                'view-settings',
            ]);
            $roleModels['kepala-sekolah']->permissions()->sync($kepsekPerms);
        }

        // Preset: Wakasek Bidang Kurikulum
        if (isset($roleModels['wakasek-kurikulum'])) {
            $kurikulumPerms = $getPermIds([
                'view-dashboard-admin',
                'view-users',
                'view-students', 'view-alumni',
                'view-classes', 'manage-classes', 'view-majors', 'view-subjects', 'manage-subjects', 'view-schedules', 'manage-schedules', 'view-academic-years', 'manage-academic-years', 'view-library',
                'view-learning', 'view-lms', 'view-materials', 'manage-materials', 'view-assignments', 'manage-assignments', 'view-exams', 'manage-exams', 'manage-cbt-server',
                'view-halaqah', 'manage-halaqah', 'view-quran-raport', 'manage-quran-raport',
                'view-raport', 'view-grades', 'manage-grades', 'manage-raport-settings',
                'manage-curriculum',
                'view-announcements', 'create-announcements', 'view-notifications',
            ]);
            $roleModels['wakasek-kurikulum']->permissions()->sync($kurikulumPerms);
        }

        // Preset: Wakasek Bidang Kesiswaan
        if (isset($roleModels['wakasek-kesiswaan'])) {
            $kesiswaanPerms = $getPermIds([
                'view-dashboard-admin',
                'view-students', 'create-students', 'edit-students', 'view-alumni', 'view-student-cards', 'view-face-id',
                'view-classes',
                'view-student-attendance', 'manage-student-attendance', 'view-student-permits', 'manage-student-attendance-settings', 'view-terminal-attendance',
                'view-bk', 'manage-bk', 'view-bk-counseling', 'view-bk-violations', 'manage-bk-categories', 'view-bk-assessments',
                'manage-extracurriculars',
                'view-spmb', 'verify-spmb',
                'view-announcements', 'create-announcements', 'view-notifications',
            ]);
            $roleModels['wakasek-kesiswaan']->permissions()->sync($kesiswaanPerms);
        }

        // Preset: Wakasek Bidang Kehumasan
        if (isset($roleModels['wakasek-kehumasan'])) {
            $humasPerms = $getPermIds([
                'view-dashboard-staff',
                'view-spmb', 'create-spmb', 'verify-spmb', 'manage-spmb-waves', 'manage-spmb-settings',
                'view-posts', 'manage-posts',
                'view-announcements', 'manage-announcements',
                'view-gallery', 'manage-gallery',
                'manage-sliders', 'manage-curriculum', 'manage-extracurriculars',
                'view-broadcast', 'view-notifications',
            ]);
            $roleModels['wakasek-kehumasan']->permissions()->sync($humasPerms);
        }

        // Also ensure Super Admin and Admin retain all appropriate permissions
        $superAdmin = Role::whereIn('slug', ['superadmin', 'super-admin', 'super_admin'])->first();
        if ($superAdmin) {
            $superAdmin->permissions()->sync($allPermissions->pluck('id')->toArray());
        }

        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching(
                $allPermissions->whereNotIn('slug', ['view-roles', 'manage-roles'])->pluck('id')->toArray()
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe reversible migration
    }
};
