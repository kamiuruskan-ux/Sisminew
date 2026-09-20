<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\HalaqahRecord;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuranRaportController extends Controller
{
    /**
     * Cek restriksi kelas untuk Guru Al-Qur'an.
     * Mengembalikan array of ID kelas jika guru-quran, atau null jika admin/super-admin.
     */
    private function getAllowedClassIds(): ?array
    {
        $user = Auth::user();
        if (!$user) return [];

        return $user->getAssignedClassIds();
    }

    /**
     * Multi-Template Capaian Santri Berdasarkan Tingkat Kelas / Halaqah
     */
    public static function getCapaianTemplates(): array
    {
        return [
            'kelas_1_2' => [
                'key' => 'kelas_1_2',
                'name' => Setting::get('quran_tpl_1_name', 'Tingkat Pemula (Kelas 1 - 2)'),
                'tahsin_aspect_1' => Setting::get('quran_tpl_1_tahsin_1', 'Standar Jilid & Pengenalan Huruf Hijaiyah Berharakat'),
                'tahsin_aspect_2' => Setting::get('quran_tpl_1_tahsin_2', 'Ketepatan Harakat, Panjang Pendek (Mad Asli) & Kelancaran'),
                'tahfidz_aspect_1' => Setting::get('quran_tpl_1_tahfidz_1', 'Hafalan Surah-surah Pendek Juz 30 (An-Nas s/d Ad-Duha)'),
                'tahfidz_aspect_2' => Setting::get('quran_tpl_1_tahfidz_2', 'Adab Tilawah & Fashohah Muroja\'ah Harian'),
            ],
            'kelas_3_4' => [
                'key' => 'kelas_3_4',
                'name' => Setting::get('quran_tpl_2_name', 'Tingkat Menengah (Kelas 3 - 4)'),
                'tahsin_aspect_1' => Setting::get('quran_tpl_2_tahsin_1', 'Standar Jilid Lanjutan & Kaidah Tajwid Dasar (Nun/Mim Sukun)'),
                'tahsin_aspect_2' => Setting::get('quran_tpl_2_tahsin_2', 'Makharijul Huruf, Mad Thabi\'i & Ghunnah'),
                'tahfidz_aspect_1' => Setting::get('quran_tpl_2_tahfidz_1', 'Hafalan Juz 30 Lengkap Mutqin (An-Naba\' s/d An-Nas)'),
                'tahfidz_aspect_2' => Setting::get('quran_tpl_2_tahfidz_2', 'Kelancaran Sambung Ayat & Muroja\'ah Mandiri'),
            ],
            'kelas_5_6' => [
                'key' => 'kelas_5_6',
                'name' => Setting::get('quran_tpl_3_name', 'Tingkat Lanjutan (Kelas 5 - 6)'),
                'tahsin_aspect_1' => Setting::get('quran_tpl_3_tahsin_1', 'Tilawah Al-Qur\'an Tartil & Standar Fashohah Lanjutan'),
                'tahsin_aspect_2' => Setting::get('quran_tpl_3_tahsin_2', 'Ahkamul Mad Wal Qashr, Waqaf & Ibtida\''),
                'tahfidz_aspect_1' => Setting::get('quran_tpl_3_tahfidz_1', 'Ziyadah Hafalan Juz 29 & Muroja\'ah Mutqin Juz 30'),
                'tahfidz_aspect_2' => Setting::get('quran_tpl_3_tahfidz_2', 'Ketahanan Hafalan (Tasmi\') & Tajwidul Kalam'),
            ],
            'intensif' => [
                'key' => 'intensif',
                'name' => Setting::get('quran_tpl_4_name', 'Halaqah Khusus / Intensif Tahfidz'),
                'tahsin_aspect_1' => Setting::get('quran_tpl_4_tahsin_1', 'Tahsin Al-Qur\'an Standar Fashahah Riwayat Hafs \'An \'Ashim'),
                'tahsin_aspect_2' => Setting::get('quran_tpl_4_tahsin_2', 'Sifat-sifat Huruf, Ahkam Tajwid & Gharibul Qur\'an'),
                'tahfidz_aspect_1' => Setting::get('quran_tpl_4_tahfidz_1', 'Setoran Ziyadah Hafalan Juz Pilihan (Juz 1, 2 atau 28, 29, 30)'),
                'tahfidz_aspect_2' => Setting::get('quran_tpl_4_tahfidz_2', 'Muroja\'ah Sab\'ah & Ujian Tasmi\' Sekali Duduk'),
            ],
        ];
    }

    /**
     * Dapatkan Seluruh Pengaturan Template Raport Al-Qur'an (Kota Default: Palu)
     */
    public static function getRaportSettings(): array
    {
        return [
            'kop_top' => Setting::get('quran_raport_kop_top', 'LEMBAGA PENDIDIKAN DAN TAHFIDZ AL-QUR\'AN'),
            'school_name' => Setting::get('quran_raport_school_name', Setting::get('school_name', 'SDIT AL-FAHMI PALU')),
            'school_address' => Setting::get('quran_raport_school_address', Setting::get('school_address', 'Jl. Lembu No. 02, Kota Palu, Sulawesi Tengah')),
            'school_phone' => Setting::get('quran_raport_school_phone', Setting::get('school_phone', '081234567890')),
            'school_email' => Setting::get('quran_raport_school_email', Setting::get('school_email', 'info@sditalfahmi-palu.com')),
            'school_website' => Setting::get('quran_raport_school_website', Setting::get('school_website', 'www.sditalfahmi-palu.com')),
            'logo_type' => Setting::get('quran_raport_logo_type', 'default'), // 'default', 'placeholder', 'custom'
            'custom_logo_path' => Setting::get('quran_raport_custom_logo_path'),
            'city' => Setting::get('quran_raport_city', 'Palu'), // Default Kota Palu
            'date' => Setting::get('quran_raport_date', now()->translatedFormat('d F Y')),
            'principal_name' => Setting::get('quran_raport_principal_name', Setting::get('principal_name', 'Ustadz Pembina, Lc., M.H.')),
            'principal_nip' => Setting::get('quran_raport_principal_nip', Setting::get('principal_nip', '-')),
            'principal_title' => Setting::get('quran_raport_principal_title', 'Kepala Sekolah / Mudir'),
            'signature_path' => Setting::get('quran_raport_signature_path', Setting::get('raport_signature_path')),
            'stamp_path' => Setting::get('quran_raport_stamp_path', Setting::get('stamp_image_path')),
        ];
    }

    /**
     * Tampilan Daftar Siswa & Filter untuk Cetak E-Raport Khusus Al-Qur'an
     */
    public function index(Request $request)
    {
        $allowedClassIds = $this->getAllowedClassIds();

        $classesQuery = ClassModel::withCount('students')->orderBy('name', 'asc');
        if ($allowedClassIds !== null) {
            $classesQuery->whereIn('id', $allowedClassIds);
        }
        $classes = $classesQuery->get();

        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $activeAcademicYear = AcademicYear::getActive() ?? $academicYears->first();

        $query = Student::with(['user', 'class', 'major', 'halaqahRecords']);

        if ($allowedClassIds !== null) {
            $query->whereIn('class_id', $allowedClassIds);
        }

        if ($request->filled('major_id')) {
            $query->where('major_id', $request->major_id);
        }

        if ($request->filled('class_id')) {
            if ($allowedClassIds === null || in_array($request->class_id, $allowedClassIds)) {
                $query->where('class_id', $request->class_id);
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->orderBy('nisn', 'asc')->paginate(20)->withQueryString();
        $selectedClass = $request->filled('class_id') ? ClassModel::find($request->class_id) : null;
        $templates = self::getCapaianTemplates();
        $raportSettings = self::getRaportSettings();

        return view('admin.quran-raport.index', compact(
            'students',
            'classes',
            'academicYears',
            'activeAcademicYear',
            'selectedClass',
            'templates',
            'raportSettings'
        ));
    }

    /**
     * Menu Edit Template Raport Al-Qur'an (Kop, Logo, Kota Palu, Tanggal, dan Multi-Template Capaian)
     */
    public function settings()
    {
        $raportSettings = self::getRaportSettings();
        $templates = self::getCapaianTemplates();

        return view('admin.quran-raport.settings', compact('raportSettings', 'templates'));
    }

    /**
     * Simpan Pengaturan Template Raport Al-Qur'an
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'quran_raport_kop_top' => 'nullable|string|max:255',
            'quran_raport_school_name' => 'required|string|max:255',
            'quran_raport_school_address' => 'nullable|string|max:500',
            'quran_raport_school_phone' => 'nullable|string|max:50',
            'quran_raport_school_email' => 'nullable|string|max:100',
            'quran_raport_school_website' => 'nullable|string|max:100',
            'quran_raport_city' => 'required|string|max:100',
            'quran_raport_date' => 'nullable|string|max:100',
            'quran_raport_principal_name' => 'required|string|max:255',
            'quran_raport_principal_nip' => 'nullable|string|max:50',
            'quran_raport_principal_title' => 'nullable|string|max:100',
            'quran_raport_logo_type' => 'required|in:default,placeholder,custom',
            'custom_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'signature_file' => 'nullable|image|mimes:png,webp|max:2048',
            'stamp_file' => 'nullable|image|mimes:png,webp|max:2048',
        ]);

        // Simpan Teks Pengaturan
        Setting::set('quran_raport_kop_top', $request->quran_raport_kop_top ?? 'LEMBAGA PENDIDIKAN DAN TAHFIDZ AL-QUR\'AN');
        Setting::set('quran_raport_school_name', $request->quran_raport_school_name);
        Setting::set('quran_raport_school_address', $request->quran_raport_school_address);
        Setting::set('quran_raport_school_phone', $request->quran_raport_school_phone);
        Setting::set('quran_raport_school_email', $request->quran_raport_school_email);
        Setting::set('quran_raport_school_website', $request->quran_raport_school_website);
        Setting::set('quran_raport_city', $request->quran_raport_city ?? 'Palu');
        Setting::set('quran_raport_date', $request->quran_raport_date);
        Setting::set('quran_raport_principal_name', $request->quran_raport_principal_name);
        Setting::set('quran_raport_principal_nip', $request->quran_raport_principal_nip);
        Setting::set('quran_raport_principal_title', $request->quran_raport_principal_title ?? 'Kepala Sekolah / Mudir');
        Setting::set('quran_raport_logo_type', $request->quran_raport_logo_type);

        // Upload Berkas Kustom Logo
        if ($request->hasFile('custom_logo')) {
            $saved = save_uploaded_public_file($request->file('custom_logo'), 'img/raport');
            Setting::set('quran_raport_custom_logo_path', $saved);
        }

        // Upload Berkas TTD
        if ($request->hasFile('signature_file')) {
            $saved = save_uploaded_public_file($request->file('signature_file'), 'img/raport');
            Setting::set('quran_raport_signature_path', $saved);
        }

        // Upload Berkas Stempel
        if ($request->hasFile('stamp_file')) {
            $saved = save_uploaded_public_file($request->file('stamp_file'), 'img/raport');
            Setting::set('quran_raport_stamp_path', $saved);
        }

        // Simpan 4 Template Capaian Santri Tiap Kelas
        for ($i = 1; $i <= 4; $i++) {
            if ($request->has("quran_tpl_{$i}_name")) {
                Setting::set("quran_tpl_{$i}_name", $request->input("quran_tpl_{$i}_name"));
                Setting::set("quran_tpl_{$i}_tahsin_1", $request->input("quran_tpl_{$i}_tahsin_1"));
                Setting::set("quran_tpl_{$i}_tahsin_2", $request->input("quran_tpl_{$i}_tahsin_2"));
                Setting::set("quran_tpl_{$i}_tahfidz_1", $request->input("quran_tpl_{$i}_tahfidz_1"));
                Setting::set("quran_tpl_{$i}_tahfidz_2", $request->input("quran_tpl_{$i}_tahfidz_2"));
            }
        }

        return redirect()->route('admin.quran-raport.settings')
            ->with('success', 'Pengaturan template dan capaian santri raport Al-Qur\'an berhasil disimpan.');
    }

    /**
     * Live Preview Cetak Raport Al-Qur'an Menggunakan Data Sampel / Siswa Pertama
     */
    public function preview(Request $request)
    {
        $allowedClassIds = $this->getAllowedClassIds();
        $query = Student::with(['user', 'class', 'major', 'halaqahRecords.teacher']);
        
        if ($allowedClassIds !== null) {
            $query->whereIn('class_id', $allowedClassIds);
        }

        $sampleStudent = $query->first();

        // Jika belum ada data siswa, buat mock model untuk preview
        if (!$sampleStudent) {
            $sampleStudent = new Student();
            $sampleStudent->id = 999;
            $sampleStudent->nisn = '0081234567';
            $sampleStudent->nis = '202601001';
            $sampleUser = new User();
            $sampleUser->name = 'MUHAMMAD AL-FATIH (CONTOH SANTRI)';
            $sampleStudent->setRelation('user', $sampleUser);

            $sampleClass = new ClassModel();
            $sampleClass->name = 'Kelas VII A (Tahfidz)';
            $sampleStudent->setRelation('class', $sampleClass);
        }

        $academicYear = AcademicYear::getActive();
        $semester = $request->get('semester', 'Ganjil');
        $templateKey = $request->get('template_key', 'kelas_1_2');
        $templates = self::getCapaianTemplates();
        $selectedTemplate = $templates[$templateKey] ?? $templates['kelas_1_2'];

        $raportSettings = self::getRaportSettings();
        $raportData = [
            $this->buildQuranRaportData($sampleStudent, $academicYear, $semester)
        ];

        $isPreview = true;

        return view('admin.quran-raport.print', compact(
            'raportData',
            'raportSettings',
            'academicYear',
            'semester',
            'selectedTemplate',
            'isPreview'
        ));
    }

    /**
     * Render Lembar Cetak E-Raport Khusus Al-Qur'an (Single / Bulk Cetak Siap Print A4 / PDF)
     */
    public function print(Request $request)
    {
        $allowedClassIds = $this->getAllowedClassIds();
        $query = Student::with(['user', 'class', 'major', 'halaqahRecords.teacher']);

        if ($request->filled('student_ids')) {
            $rawIds = is_array($request->student_ids) ? $request->student_ids : explode(',', $request->student_ids);
            $ids = array_map(function ($id) {
                return decrypt_id($id);
            }, array_filter($rawIds));
            $query->whereIn('id', array_filter($ids));
        } elseif ($request->filled('student_id')) {
            $studentId = decrypt_id($request->student_id);
            $query->where('id', $studentId);
        } elseif ($request->filled('class_id')) {
            $classId = decrypt_id($request->class_id);
            $query->where('class_id', $classId);
        } else {
            return redirect()->route('admin.quran-raport.index')->with('error', 'Pilih minimal satu santri atau kelas untuk mencetak raport Al-Quran.');
        }

        // Restriksi Guru Al-Qur'an
        if ($allowedClassIds !== null) {
            $query->whereIn('class_id', $allowedClassIds);
        }

        $students = $query->orderBy('nisn', 'asc')->get();

        if ($students->isEmpty()) {
            return redirect()->route('admin.quran-raport.index')->with('error', 'Tidak ada data santri yang dapat diakses atau ditemukan.');
        }

        $academicYear = $request->filled('academic_year_id')
            ? AcademicYear::find($request->academic_year_id)
            : AcademicYear::getActive();

        $semester = $request->get('semester', 'Ganjil');

        // Pilihan template capaian yang dipilih oleh Guru / Admin
        $templateKey = $request->get('template_key', 'kelas_1_2');
        $templates = self::getCapaianTemplates();
        $selectedTemplate = $templates[$templateKey] ?? $templates['kelas_1_2'];

        $raportSettings = self::getRaportSettings();

        $raportData = [];
        foreach ($students as $student) {
            $raportData[] = $this->buildQuranRaportData($student, $academicYear, $semester);
        }

        $isPreview = false;

        return view('admin.quran-raport.print', compact(
            'raportData',
            'raportSettings',
            'academicYear',
            'semester',
            'selectedTemplate',
            'isPreview'
        ));
    }

    /**
     * Kalkulasi otomatis data capaian Tahsin, Tahfidz, Nilai Adab, dan Kehadiran Halaqah
     */
    private function buildQuranRaportData(Student $student, ?AcademicYear $academicYear, string $semester): array
    {
        $records = collect();
        if ($student->id && $student->exists) {
            $records = HalaqahRecord::where('student_id', $student->id)
                ->when($academicYear, function ($q) use ($academicYear) {
                    return $q->where('academic_year_id', $academicYear->id);
                })
                ->latest('assessment_date')
                ->get();
        }

        // 1. Data Tahsin
        $tahsinRecords = $records->where('program_type', 'tahsin');
        $lastTahsin = $tahsinRecords->first();
        $avgTahsinCognitive = $tahsinRecords->isNotEmpty() ? round($tahsinRecords->avg('score_cognitive'), 1) : 88.5;
        $avgTahsinAdab = $tahsinRecords->isNotEmpty() ? round($tahsinRecords->avg('score_adab'), 1) : 90.0;
        $tahsinPredicate = HalaqahRecord::calculatePredicate($avgTahsinCognitive);

        // 2. Data Tahfidz
        $tahfidzRecords = $records->where('program_type', 'tahfidz');
        $lastTahfidz = $tahfidzRecords->first();
        $avgTahfidzCognitive = $tahfidzRecords->isNotEmpty() ? round($tahfidzRecords->avg('score_cognitive'), 1) : 92.0;
        $avgTahfidzAdab = $tahfidzRecords->isNotEmpty() ? round($tahfidzRecords->avg('score_adab'), 1) : 90.0;
        $tahfidzPredicate = HalaqahRecord::calculatePredicate($avgTahfidzCognitive);

        // Total Surat/Juz yang Disetorkan
        $surahList = $tahfidzRecords->pluck('surah_name')->filter()->unique()->values()->toArray();
        $juzList = $tahfidzRecords->pluck('juz_number')->filter()->unique()->values()->toArray();

        // 3. Rata-rata Keseluruhan Nilai Al-Qur'an
        $allCognitive = $records->pluck('score_cognitive');
        $overallScore = $allCognitive->isNotEmpty() ? round($allCognitive->avg(), 1) : round(($avgTahsinCognitive + $avgTahfidzCognitive) / 2, 1);
        $overallPredicate = HalaqahRecord::calculatePredicate($overallScore);

        // 4. Rekap Kehadiran Halaqah
        $attendance = [
            'hadir' => $records->isNotEmpty() ? $records->where('attendance_status', 'hadir')->count() : 18,
            'sakit' => $records->where('attendance_status', 'sakit')->count(),
            'izin'  => $records->where('attendance_status', 'izin')->count(),
            'alpa'  => $records->where('attendance_status', 'alpa')->count(),
        ];

        // 5. Guru / Musyrif Pembimbing
        $teacher = $records->first()?->teacher ?? $student->class?->homeroomTeacher ?? null;
        $teacherName = $teacher ? $teacher->name : 'Ustadz Pembimbing Halaqah';

        // 6. Catatan Pembimbing
        $teacherNote = $records->first()?->teacher_notes ?: "Alhamdulillah ananda menunjukkan adab dan ketekunan yang baik dalam mempelajari Al-Qur'an. Terus istiqamah dalam muroja'ah di rumah.";

        return [
            'student' => $student,
            'teacher_name' => $teacherName,
            'teacher_note' => $teacherNote,
            'attendance' => $attendance,
            'tahsin' => [
                'has_data' => $tahsinRecords->isNotEmpty(),
                'last_jilid' => $lastTahsin ? ($lastTahsin->tahsin_type === 'tilawah' ? 'Al-Quran / Tilawah' : ($lastTahsin->jilid_level ?? 'Jilid 1')) : 'Al-Qur\'an / Tilawah',
                'last_pages' => $lastTahsin && $lastTahsin->page_start ? "Halaman {$lastTahsin->page_start} s/d {$lastTahsin->page_end}" : 'Juz 1 Halaman 1-10',
                'score' => $avgTahsinCognitive,
                'predicate' => $tahsinPredicate,
                'total_setoran' => max(1, $tahsinRecords->count()),
            ],
            'tahfidz' => [
                'has_data' => $tahfidzRecords->isNotEmpty(),
                'last_surah' => $lastTahfidz ? "Surah {$lastTahfidz->surah_name}" : 'Surah An-Naba\'',
                'last_ayat' => $lastTahfidz && $lastTahfidz->ayat_start ? "Ayat {$lastTahfidz->ayat_start} s/d {$lastTahfidz->ayat_end}" : 'Ayat 1 s/d 40',
                'juz_list' => !empty($juzList) ? 'Juz ' . implode(', ', $juzList) : 'Juz 30',
                'surah_count' => count($surahList) ?: 1,
                'score' => $avgTahfidzCognitive,
                'predicate' => $tahfidzPredicate,
                'total_setoran' => max(1, $tahfidzRecords->count()),
            ],
            'overall_score' => $overallScore,
            'overall_predicate' => $overallPredicate,
        ];
    }
}
