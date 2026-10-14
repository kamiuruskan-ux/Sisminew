<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Extracurricular;
use App\Models\ExtracurricularAgenda;
use App\Models\ExtracurricularAgendaStudent;
use App\Models\ExtracurricularEvaluation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExtracurricularLearningController extends Controller
{
    public function index(Request $request)
    {
        ExtracurricularAgenda::ensureTableExists();

        $user = auth()->user();
        $extracurriculars = Extracurricular::orderBy('name')->get();
        $classes = ClassModel::orderBy('name')->get();
        $activeYear = AcademicYear::getActive();

        $selectedExtraId = $request->get('extracurricular_id') ?: ($extracurriculars->first()?->id ?? null);
        $selectedClassId = $request->get('class_id') ?: ($classes->first()?->id ?? null);
        $currentTab = $request->get('tab', 'agenda'); // agenda, history, evaluation

        // Santri untuk form input agenda / evaluasi
        $students = collect();
        if ($selectedClassId) {
            $students = Student::with('user')
                ->where('class_id', $selectedClassId)
                ->where(function($q) {
                    $q->whereIn('student_status', ['active', 'Aktif'])->orWhereNull('student_status');
                })
                ->get()
                ->sortBy(fn($s) => $s->user?->name ?? '');
        }

        // Riwayat Agenda
        $agendaQuery = ExtracurricularAgenda::with(['extracurricular', 'coach', 'students.student.user'])
            ->latest('date')
            ->latest('id');

        if ($selectedExtraId) {
            $agendaQuery->where('extracurricular_id', $selectedExtraId);
        }

        $agendas = $agendaQuery->paginate(10, ['*'], 'agenda_page')->withQueryString();

        // Data Penilaian Capaian Ekskul untuk Rapor
        $evaluations = [];
        if ($selectedExtraId && $selectedClassId) {
            $evalRecords = ExtracurricularEvaluation::where('extracurricular_id', $selectedExtraId)
                ->where('academic_year_id', $activeYear?->id)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');

            foreach ($students as $st) {
                $eval = $evalRecords->get($st->id);
                $evaluations[$st->id] = [
                    'predicate' => $eval?->predicate ?? 'A',
                    'score' => $eval?->score ?? 85,
                    'description' => $eval?->description ?? 'Aktif mengikuti kegiatan ekstrakurikuler dengan penuh antusias dan disiplin tinggi.',
                ];
            }
        }

        // Data Rekapitulasi Pembina Ekskul (Khusus Admin & Kepala Sekolah)
        $coaches = User::whereIn('id', ExtracurricularAgenda::pluck('coach_id'))
            ->orWhereHas('roles', fn($q) => $q->whereIn('slug', ['guru', 'teacher']))
            ->distinct()
            ->get();

        $coachSummaries = $coaches->map(function($coach) use ($activeYear) {
            $agendas = ExtracurricularAgenda::with(['extracurricular', 'students'])
                ->where('coach_id', $coach->id)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->get();

            $totalMeetings = $agendas->count();
            if ($totalMeetings === 0) return null;

            $approvedMeetings = $agendas->where('status', 'approved')->count();
            $pendingMeetings = $agendas->where('status', 'submitted')->count();
            $rejectedMeetings = $agendas->where('status', 'rejected')->count();
            $extraNames = $agendas->pluck('extracurricular.name')->filter()->unique()->implode(', ');

            $allStudents = $agendas->flatMap->students;
            $attendedCount = $allStudents->where('attendance_status', 'hadir')->count();
            $avgSkill = $allStudents->avg('score_skill') ?? 0;
            $avgAttitude = $allStudents->avg('score_attitude') ?? 0;

            return (object)[
                'coach' => $coach,
                'extra_names' => $extraNames ?: 'Ekstrakurikuler',
                'total_meetings' => $totalMeetings,
                'approved_meetings' => $approvedMeetings,
                'pending_meetings' => $pendingMeetings,
                'rejected_meetings' => $rejectedMeetings,
                'attended_count' => $attendedCount,
                'avg_skill' => round($avgSkill, 1),
                'avg_attitude' => round($avgAttitude, 1),
                'latest_teaching' => $agendas->sortByDesc('date')->first()?->date,
            ];
        })->filter()->sortByDesc('total_meetings')->values();

        return view('admin.extracurriculars.learning', compact(
            'extracurriculars',
            'classes',
            'activeYear',
            'selectedExtraId',
            'selectedClassId',
            'currentTab',
            'students',
            'agendas',
            'evaluations',
            'coachSummaries'
        ));
    }

    public function storeAgenda(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'extracurricular_id' => 'required|exists:extracurriculars,id',
            'date' => 'required|date',
            'meeting_number' => 'nullable|integer|min:1',
            'activity_title' => 'required|string|max:200',
            'material_taught' => 'required|string',
            'location' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
            'students' => 'required|array|min:1',
            'students.*.attendance' => 'nullable|in:hadir,sakit,izin,alpa',
            'students.*.skill' => 'nullable|numeric|min:0|max:100',
            'students.*.attitude' => 'nullable|numeric|min:0|max:100',
            'students.*.notes' => 'nullable|string',
        ], [
            'extracurricular_id.required' => 'Pilih ekstrakurikuler terlebih dahulu.',
            'activity_title.required' => 'Judul atau agenda kegiatan wajib diisi.',
            'material_taught.required' => 'Materi/latihan yang diajarkan wajib diisi.',
            'students.required' => 'Daftar absensi santri belum tersedia.',
        ]);

        DB::beginTransaction();
        try {
            ExtracurricularAgenda::ensureTableExists();

            $agenda = ExtracurricularAgenda::create([
                'extracurricular_id' => $validated['extracurricular_id'],
                'coach_id' => $user->id,
                'academic_year_id' => AcademicYear::getActive()?->id,
                'date' => $validated['date'],
                'meeting_number' => $validated['meeting_number'] ?? 1,
                'activity_title' => $validated['activity_title'],
                'material_taught' => $validated['material_taught'],
                'location' => $validated['location'] ?? 'Area Kampus Sekolah',
                'notes' => $validated['notes'] ?? null,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            foreach ($validated['students'] as $studentId => $item) {
                $status = $item['attendance'] ?? 'hadir';
                $scoreSkill = isset($item['skill']) && is_numeric($item['skill']) ? (float)$item['skill'] : 80;
                $scoreAttitude = isset($item['attitude']) && is_numeric($item['attitude']) ? (float)$item['attitude'] : 80;

                ExtracurricularAgendaStudent::create([
                    'extracurricular_agenda_id' => $agenda->id,
                    'student_id' => $studentId,
                    'attendance_status' => $status,
                    'score_skill' => $scoreSkill,
                    'score_attitude' => $scoreAttitude,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.extracurriculars.learning.index', [
                'tab' => 'history',
                'extracurricular_id' => $validated['extracurricular_id']
            ])->with('success', 'Agenda pembelajaran & absensi ekstrakurikuler berhasil diajukan untuk disetujui Kepala Sekolah!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan agenda ekstrakurikuler: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Setujui Agenda Pembelajaran oleh Kepala Sekolah
     */
    public function approveAgenda(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            abort(403, 'Hanya Kepala Sekolah dan Administrator yang berhak menyetujui laporan pembelajaran.');
        }

        $agenda = ExtracurricularAgenda::findOrFail($id);
        $agenda->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->input('approval_notes'),
        ]);

        return back()->with('success', 'Laporan agenda ekskul berhasil disetujui (Approved).');
    }

    /**
     * Minta Revisi / Tolak Agenda Pembelajaran oleh Kepala Sekolah
     */
    public function rejectAgenda(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            abort(403, 'Hanya Kepala Sekolah dan Administrator yang berhak mengelola persetujuan.');
        }

        $agenda = ExtracurricularAgenda::findOrFail($id);
        $agenda->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->input('approval_notes') ?? 'Mohon periksa kembali materi/absensi latihan.',
        ]);

        return back()->with('success', 'Laporan agenda ekskul dikembalikan untuk revisi.');
    }

    public function destroyAgenda($id)
    {
        $user = auth()->user();
        $agenda = ExtracurricularAgenda::findOrFail($id);

        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah']) && $agenda->coach_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses menghapus agenda ini.');
        }

        $agenda->delete();

        return back()->with('success', 'Agenda ekstrakurikuler berhasil dihapus.');
    }

    public function saveEvaluations(Request $request)
    {
        $validated = $request->validate([
            'extracurricular_id' => 'required|exists:extracurriculars,id',
            'class_id' => 'required|exists:classes,id',
            'semester' => 'nullable|string',
            'evaluations' => 'required|array',
            'evaluations.*.predicate' => 'required|string',
            'evaluations.*.score' => 'nullable|numeric|min:0|max:100',
            'evaluations.*.description' => 'required|string',
        ]);

        $activeYear = AcademicYear::getActive();
        $semester = $validated['semester'] ?? ($activeYear?->semester ?? 'ganjil');
        $user = auth()->user();

        DB::beginTransaction();
        try {
            ExtracurricularAgenda::ensureTableExists();

            foreach ($validated['evaluations'] as $studentId => $data) {
                ExtracurricularEvaluation::updateOrCreate(
                    [
                        'extracurricular_id' => $validated['extracurricular_id'],
                        'student_id' => $studentId,
                        'academic_year_id' => $activeYear?->id,
                        'semester' => $semester,
                    ],
                    [
                        'predicate' => $data['predicate'],
                        'score' => $data['score'] ?? 85,
                        'description' => $data['description'],
                        'evaluated_by' => $user->id,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.extracurriculars.learning.index', [
                'tab' => 'evaluation',
                'extracurricular_id' => $validated['extracurricular_id'],
                'class_id' => $validated['class_id']
            ])->with('success', 'Penilaian capaian ekstrakurikuler untuk rapor berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }
}
