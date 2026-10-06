<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\HalaqahRecord;
use App\Models\QuranHalaqahMember;
use App\Models\QuranTarget;
use App\Models\QuranTasmiExam;
use App\Models\QuranJilidExam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentQuranController extends Controller
{
    /**
     * Halaman Mutaba'ah Al-Qur'an Santri (Tahsin, Tahfidz, Tilawah)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'Profil santri tidak ditemukan.');
        }

        $activeYear = AcademicYear::getActive();

        // 1. Guru Pembimbing Halaqah
        $halaqahMembership = QuranHalaqahMember::with('teacher')
            ->where('student_id', $student->id)
            ->first();

        // 2. Query Riwayat Setoran Siswa
        $query = HalaqahRecord::with(['teacher'])
            ->where('student_id', $student->id)
            ->latest('assessment_date')
            ->latest('id');

        // Filter Program
        $filterProgram = $request->get('program', 'all');
        if ($filterProgram !== 'all' && in_array($filterProgram, ['tahsin', 'tahfidz', 'tilawah'])) {
            $query->where('program_type', $filterProgram);
        }

        // Filter Kategori (Ziyadah / Muroja'ah)
        $filterCategory = $request->get('category', 'all');
        if ($filterCategory !== 'all' && in_array($filterCategory, ['ziyadah', 'murojaah'])) {
            $query->where('record_category', $filterCategory);
        }

        // Filter Bulan
        if ($request->filled('month')) {
            $month = (int) $request->month;
            $year = (int) ($request->year ?? Carbon::now()->year);
            $query->whereMonth('assessment_date', $month)->whereYear('assessment_date', $year);
        }

        $records = $query->paginate(15)->withQueryString();

        // 3. Ringkasan Statistik Siswa (All Time)
        $baseQuery = HalaqahRecord::where('student_id', $student->id);
        $totalSetoran = (clone $baseQuery)->count();
        $tahsinCount = (clone $baseQuery)->where('program_type', 'tahsin')->count();
        $tahfidzCount = (clone $baseQuery)->where('program_type', 'tahfidz')->count();
        $tilawahCount = (clone $baseQuery)->where('program_type', 'tilawah')->count();
        $ziyadahCount = (clone $baseQuery)->where('record_category', 'ziyadah')->count();
        $murojaahCount = (clone $baseQuery)->where('record_category', 'murojaah')->count();

        $avgScore = round((clone $baseQuery)->avg('score_cognitive') ?? 0, 1);
        $mumtazCount = (clone $baseQuery)->where('predicate', 'Mumtaz')->count();

        // Kehadiran Halaqah
        $hadirCount = (clone $baseQuery)->where('attendance_status', 'hadir')->count();
        $attendanceRate = $totalSetoran > 0 ? round(($hadirCount / $totalSetoran) * 100, 1) : 100;

        // Capaian Terakhir per Program
        $latestTahsin = (clone $baseQuery)->where('program_type', 'tahsin')->latest('assessment_date')->latest('id')->first();
        $latestTahfidz = (clone $baseQuery)->where('program_type', 'tahfidz')->latest('assessment_date')->latest('id')->first();
        $latestTilawah = (clone $baseQuery)->where('program_type', 'tilawah')->latest('assessment_date')->latest('id')->first();

        // 4. Target Capaian Tingkat Kelas Santri
        $studentGrade = $student->class?->grade 
            ?: (preg_match('/^(\d+)/', $student->class?->name ?? '', $m) ? (int)$m[1] : 1);
        
        $targets = QuranTarget::where('grade', $studentGrade)
            ->orderBy('semester', 'asc')
            ->get();

        // 5. Riwayat Ujian Tasmi' & Syahadah
        $tasmiExams = QuranTasmiExam::with('teacher')
            ->where('student_id', $student->id)
            ->latest('exam_date')
            ->get();

        // 6. Riwayat Ujian Kenaikan Jilid Tahsin
        $jilidExams = QuranJilidExam::with('teacher')
            ->where('student_id', $student->id)
            ->latest('exam_date')
            ->get();

        return view('student.quran.index', compact(
            'student',
            'halaqahMembership',
            'records',
            'totalSetoran',
            'tahsinCount',
            'tahfidzCount',
            'tilawahCount',
            'ziyadahCount',
            'murojaahCount',
            'avgScore',
            'mumtazCount',
            'attendanceRate',
            'latestTahsin',
            'latestTahfidz',
            'latestTilawah',
            'targets',
            'tasmiExams',
            'jilidExams',
            'studentGrade',
            'filterProgram',
            'filterCategory'
        ));
    }
}
