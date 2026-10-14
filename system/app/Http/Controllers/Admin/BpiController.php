<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\BpiAgenda;
use App\Models\BpiAttendance;
use App\Models\BpiGroup;
use App\Models\BpiGroupMember;
use App\Models\BpiReport;
use App\Models\ClassModel;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BpiController extends Controller
{
    public function index(Request $request)
    {
        BpiGroup::ensureTableExists();

        $user = auth()->user();
        $activeYear = AcademicYear::getActive();

        // Ambil kelompok BPI
        $groupQuery = BpiGroup::with(['mentor', 'members.student.user'])->where('is_active', true);
        
        // Jika bukan super-admin/kepala sekolah, prioritaskan kelompok bimbingannya
        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            // Bisa melihat kelompok yang dia binai, atau semua jika belum difilter
            $userGroups = (clone $groupQuery)->where('mentor_id', $user->id)->get();
            $groups = $userGroups->isNotEmpty() ? $userGroups : $groupQuery->get();
        } else {
            $groups = $groupQuery->orderBy('name')->get();
        }

        $selectedGroupId = $request->get('bpi_group_id') ?: ($groups->first()?->id ?? null);
        $currentTab = $request->get('tab', 'agenda'); // agenda, history, groups, report_input

        $selectedGroup = $groups->firstWhere('id', $selectedGroupId);

        // Anggota santri dalam kelompok yang dipilih
        $groupStudents = collect();
        if ($selectedGroup) {
            $groupStudents = $selectedGroup->members->map(fn($m) => $m->student)->filter()->values();
        }

        // Jika kelompok belum punya anggota, sediakan santri aktif dari kelas untuk dipilih
        $allStudents = Student::with('user', 'class')->where('status', 'active')->get()->sortBy(fn($s) => $s->user?->name ?? '');
        $mentors = User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['guru', 'admin', 'super-admin']);
        })->orWhere('user_type', 'guru')->orderBy('name')->get();

        // Riwayat Agenda Pertemuan BPI
        $agendaQuery = BpiAgenda::with(['group', 'mentor', 'attendances.student.user'])
            ->latest('date')
            ->latest('id');

        if ($selectedGroupId) {
            $agendaQuery->where('bpi_group_id', $selectedGroupId);
        }

        $agendas = $agendaQuery->paginate(10, ['*'], 'bpi_page')->withQueryString();

        // Data Raport BPI untuk Tab Input Raport
        $reports = [];
        if ($selectedGroup && $groupStudents->isNotEmpty()) {
            $reportRecords = BpiReport::where('bpi_group_id', $selectedGroup->id)
                ->where('academic_year_id', $activeYear?->id)
                ->whereIn('student_id', $groupStudents->pluck('id'))
                ->get()
                ->keyBy('student_id');

            foreach ($groupStudents as $st) {
                $rep = $reportRecords->get($st->id);
                $reports[$st->id] = [
                    'sholat_fardhu' => $rep?->sholat_fardhu ?? 'A',
                    'sholat_sunnah' => $rep?->sholat_sunnah ?? 'A',
                    'tilawah_quran' => $rep?->tilawah_quran ?? 'A',
                    'dzikir_doa' => $rep?->dzikir_doa ?? 'A',
                    'puasa_sunnah' => $rep?->puasa_sunnah ?? 'B',
                    'adab_guru_ortu' => $rep?->adab_guru_ortu ?? 'A',
                    'kedisiplinan_kebersihan' => $rep?->kedisiplinan_kebersihan ?? 'A',
                    'ukhuwah_sosial' => $rep?->ukhuwah_sosial ?? 'A',
                    'keaktifan_halaqah' => $rep?->keaktifan_halaqah ?? 'A',
                    'total_score' => $rep?->total_score ?? 88,
                    'final_predicate' => $rep?->final_predicate ?? 'A',
                    'mentor_notes' => $rep?->mentor_notes ?? 'Alhamdulillah ananda menunjukkan komitmen ibadah dan akhlak yang mulia. Pertahankan semangat mutaba\'ah yaumiyah.',
                    'target_improvement' => $rep?->target_improvement ?? 'Meningkatkan istiqomah shalat dhuha dan menambah jumlah tilawah harian.',
                ];
            }
        }

        return view('admin.bpi.index', compact(
            'groups',
            'selectedGroup',
            'selectedGroupId',
            'currentTab',
            'groupStudents',
            'allStudents',
            'mentors',
            'agendas',
            'reports',
            'activeYear'
        ));
    }

    public function storeAgenda(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'bpi_group_id' => 'required|exists:bpi_groups,id',
            'date' => 'required|date',
            'meeting_number' => 'nullable|integer|min:1',
            'topic' => 'required|string|max:200',
            'material_summary' => 'required|string',
            'tilawah_surah' => 'nullable|string|max:150',
            'mutabaah_notes' => 'nullable|string',
            'general_notes' => 'nullable|string',
            'students' => 'required|array|min:1',
            'students.*.attendance' => 'nullable|in:hadir,sakit,izin,alpa',
            'students.*.tilawah_pages' => 'nullable|integer|min:0',
            'students.*.attitude' => 'nullable|numeric|min:0|max:100',
            'students.*.notes' => 'nullable|string',
        ], [
            'bpi_group_id.required' => 'Pilih kelompok BPI terlebih dahulu.',
            'topic.required' => 'Materi atau tema halaqah wajib diisi.',
            'material_summary.required' => 'Ringkasan materi atau pembinaan wajib diisi.',
            'students.required' => 'Daftar absensi santri bimbingan belum ada.',
        ]);

        DB::beginTransaction();
        try {
            BpiGroup::ensureTableExists();

            $agenda = BpiAgenda::create([
                'bpi_group_id' => $validated['bpi_group_id'],
                'mentor_id' => $user->id,
                'academic_year_id' => AcademicYear::getActive()?->id,
                'date' => $validated['date'],
                'meeting_number' => $validated['meeting_number'] ?? 1,
                'topic' => $validated['topic'],
                'material_summary' => $validated['material_summary'],
                'tilawah_surah' => $validated['tilawah_surah'] ?? 'Tilawah Bersama',
                'mutabaah_notes' => $validated['mutabaah_notes'] ?? null,
                'general_notes' => $validated['general_notes'] ?? null,
            ]);

            foreach ($validated['students'] as $studentId => $item) {
                $status = $item['attendance'] ?? 'hadir';
                $pages = isset($item['tilawah_pages']) && is_numeric($item['tilawah_pages']) ? (int)$item['tilawah_pages'] : 0;
                $attitude = isset($item['attitude']) && is_numeric($item['attitude']) ? (float)$item['attitude'] : 85;

                BpiAttendance::create([
                    'bpi_agenda_id' => $agenda->id,
                    'student_id' => $studentId,
                    'attendance_status' => $status,
                    'tilawah_pages' => $pages,
                    'score_attitude' => $attitude,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.bpi.index', [
                'tab' => 'history',
                'bpi_group_id' => $validated['bpi_group_id']
            ])->with('success', 'Agenda & mutaba\'ah halaqah BPI berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan agenda BPI: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyAgenda($id)
    {
        $user = auth()->user();
        $agenda = BpiAgenda::findOrFail($id);

        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah']) && $agenda->mentor_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses menghapus agenda ini.');
        }

        $agenda->delete();

        return back()->with('success', 'Agenda halaqah BPI berhasil dihapus.');
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'mentor_id' => 'required|exists:users,id',
            'level' => 'nullable|string|max:50',
            'gender' => 'required|in:ikhwan,akhwat,all',
            'target_juz' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        DB::beginTransaction();
        try {
            BpiGroup::ensureTableExists();

            $group = BpiGroup::create([
                'name' => $validated['name'],
                'mentor_id' => $validated['mentor_id'],
                'academic_year_id' => AcademicYear::getActive()?->id,
                'level' => $validated['level'] ?? null,
                'gender' => $validated['gender'],
                'target_juz' => $validated['target_juz'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            if (!empty($validated['student_ids'])) {
                foreach ($validated['student_ids'] as $stId) {
                    BpiGroupMember::create([
                        'bpi_group_id' => $group->id,
                        'student_id' => $stId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.bpi.index', [
                'tab' => 'groups',
                'bpi_group_id' => $group->id
            ])->with('success', 'Kelompok Bina Pribadi Islam berhasil ditambahkan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan kelompok: ' . $e->getMessage())->withInput();
        }
    }

    public function saveReport(Request $request)
    {
        $validated = $request->validate([
            'bpi_group_id' => 'required|exists:bpi_groups,id',
            'reports' => 'required|array',
            'reports.*.sholat_fardhu' => 'required|string',
            'reports.*.sholat_sunnah' => 'required|string',
            'reports.*.tilawah_quran' => 'required|string',
            'reports.*.dzikir_doa' => 'required|string',
            'reports.*.puasa_sunnah' => 'required|string',
            'reports.*.adab_guru_ortu' => 'required|string',
            'reports.*.kedisiplinan_kebersihan' => 'required|string',
            'reports.*.ukhuwah_sosial' => 'required|string',
            'reports.*.keaktifan_halaqah' => 'required|string',
            'reports.*.total_score' => 'nullable|numeric|min:0|max:100',
            'reports.*.final_predicate' => 'required|string',
            'reports.*.mentor_notes' => 'nullable|string',
            'reports.*.target_improvement' => 'nullable|string',
        ]);

        $activeYear = AcademicYear::getActive();
        $semester = $activeYear?->semester ?? 'ganjil';
        $user = auth()->user();

        DB::beginTransaction();
        try {
            BpiGroup::ensureTableExists();

            foreach ($validated['reports'] as $studentId => $data) {
                BpiReport::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'bpi_group_id' => $validated['bpi_group_id'],
                        'academic_year_id' => $activeYear?->id,
                        'semester' => $semester,
                    ],
                    [
                        'sholat_fardhu' => $data['sholat_fardhu'],
                        'sholat_sunnah' => $data['sholat_sunnah'],
                        'tilawah_quran' => $data['tilawah_quran'],
                        'dzikir_doa' => $data['dzikir_doa'],
                        'puasa_sunnah' => $data['puasa_sunnah'],
                        'adab_guru_ortu' => $data['adab_guru_ortu'],
                        'kedisiplinan_kebersihan' => $data['kedisiplinan_kebersihan'],
                        'ukhuwah_sosial' => $data['ukhuwah_sosial'],
                        'keaktifan_halaqah' => $data['keaktifan_halaqah'],
                        'total_score' => $data['total_score'] ?? 88,
                        'final_predicate' => $data['final_predicate'],
                        'mentor_notes' => $data['mentor_notes'] ?? null,
                        'target_improvement' => $data['target_improvement'] ?? null,
                        'mentor_id' => $user->id,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.bpi.index', [
                'tab' => 'report_input',
                'bpi_group_id' => $validated['bpi_group_id']
            ])->with('success', 'Penilaian Raport Bina Pribadi Islam (BPI) berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan penilaian rapor BPI: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Daftar Raport BPI (Pilih santri untuk cetak)
     */
    public function raportList(Request $request)
    {
        BpiGroup::ensureTableExists();

        $groups = BpiGroup::with(['mentor', 'members.student.user', 'members.student.class'])->where('is_active', true)->get();
        $selectedGroupId = $request->get('bpi_group_id') ?: ($groups->first()?->id ?? null);
        $selectedGroup = $groups->firstWhere('id', $selectedGroupId);

        $activeYear = AcademicYear::getActive();

        $studentsWithReport = collect();
        if ($selectedGroup) {
            $reportMap = BpiReport::where('bpi_group_id', $selectedGroup->id)
                ->where('academic_year_id', $activeYear?->id)
                ->get()
                ->keyBy('student_id');

            $studentsWithReport = $selectedGroup->members->map(function($m) use ($reportMap, $selectedGroup) {
                $st = $m->student;
                if (!$st) return null;
                $st->bpi_report = $reportMap->get($st->id);
                $st->bpi_group = $selectedGroup;
                return $st;
            })->filter()->values();
        }

        return view('admin.bpi.raport-list', compact('groups', 'selectedGroup', 'selectedGroupId', 'studentsWithReport', 'activeYear'));
    }

    /**
     * Cetak Raport Khusus Bina Pribadi Islam (BPI)
     */
    public function printRaport(Request $request, $studentId)
    {
        BpiGroup::ensureTableExists();

        $student = Student::with(['user', 'class'])->findOrFail($studentId);
        $activeYear = AcademicYear::getActive();
        $semester = $request->get('semester', $activeYear?->semester ?? 'ganjil');

        // Cari record kelompok santri
        $groupMember = BpiGroupMember::with(['group.mentor'])->where('student_id', $studentId)->first();
        $group = $groupMember?->group;

        // Ambil data nilai raport BPI
        $report = BpiReport::where('student_id', $studentId)
            ->when($group, fn($q) => $q->where('bpi_group_id', $group->id))
            ->where('academic_year_id', $activeYear?->id)
            ->first();

        // Rekap kehadiran BPI santri ini
        $totalMeetings = 0;
        $attendedMeetings = 0;
        $totalTilawahPages = 0;
        if ($group) {
            $agendas = BpiAgenda::where('bpi_group_id', $group->id)->pluck('id');
            $totalMeetings = $agendas->count();
            $attendances = BpiAttendance::whereIn('bpi_agenda_id', $agendas)
                ->where('student_id', $studentId)
                ->get();
            $attendedMeetings = $attendances->where('attendance_status', 'hadir')->count();
            $totalTilawahPages = $attendances->sum('tilawah_pages');
        }

        // Ambil Data Pengaturan Sekolah untuk Header / Kop
        $schoolName = Setting::get('school_name', 'SDIT AL-FAHMI PALU');
        $schoolAddress = Setting::get('school_address', 'Jl. Anoa II No. 12, Kel. Tanamodindi, Kec. Mantikulore, Kota Palu, Sulawesi Tengah');
        $schoolCity = Setting::get('school_city', 'Palu');
        $schoolLogo = Setting::get('school_logo');
        $headmasterName = Setting::get('headmaster_name', 'M. Fathan, S.Pd.I');
        $headmasterNip = Setting::get('headmaster_nip');

        return view('admin.bpi.raport-print', compact(
            'student',
            'group',
            'report',
            'totalMeetings',
            'attendedMeetings',
            'totalTilawahPages',
            'activeYear',
            'semester',
            'schoolName',
            'schoolAddress',
            'schoolCity',
            'schoolLogo',
            'headmasterName',
            'headmasterNip'
        ));
    }
}
