<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeStudySession;
use App\Models\EmployeeStudyAttendance;
use App\Models\User;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class KajianPekananController extends Controller
{
    /**
     * Check if user is supervisor / admin
     */
    protected function isSupervisor(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        return $user->hasRole([
            'super-admin', 
            'admin', 
            'operator', 
            'kepala-sekolah', 
            'wakasek-kurikulum', 
            'wakasek-kesiswaan', 
            'wakasek-kehumasan',
            'yayasan'
        ]);
    }

    /**
     * Tampilkan daftar kegiatan kajian pekanan pegawai
     * Laporan Individu Pegawai (Self-Service)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSupervisor = $this->isSupervisor();
        $search = $request->input('search');
        $month = $request->input('month');
        $teacherFilter = $request->input('teacher_id');

        $query = EmployeeStudySession::with(['creator', 'attendances.user'])
            ->when(!$isSupervisor, function ($q) use ($user) {
                // Regular employees only see their own reported kajian
                $q->where('created_by', $user->id);
            })
            ->when($isSupervisor && $teacherFilter, function ($q) use ($teacherFilter) {
                $q->where('created_by', $teacherFilter);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('speaker', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('material_summary', 'like', "%{$search}%");
                });
            })
            ->when($month, function ($q) use ($month) {
                $date = Carbon::createFromFormat('Y-m', $month);
                $q->whereYear('date', $date->year)->whereMonth('date', $date->month);
            })
            ->orderBy('date', 'desc');

        $sessions = $query->paginate(12)->withQueryString();

        // Statistik
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        if ($isSupervisor) {
            $totalSessions = EmployeeStudySession::count();
            $monthSessions = EmployeeStudySession::whereBetween('date', [$currentMonthStart, $currentMonthEnd])->count();
            $totalEmployees = User::whereDoesntHave('roles', function ($q) {
                $q->where('slug', 'student');
            })->count();
            $teachers = User::whereDoesntHave('roles', function ($q) {
                $q->where('slug', 'student');
            })->orderBy('name')->get();
        } else {
            $totalSessions = EmployeeStudySession::where('created_by', $user->id)->count();
            $monthSessions = EmployeeStudySession::where('created_by', $user->id)
                ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
                ->count();
            $totalEmployees = 1;
            $teachers = collect([$user]);
        }

        $totalAttendancesRecorded = $totalSessions;

        return view('admin.kajian-pekanan.index', compact(
            'sessions', 
            'totalSessions', 
            'monthSessions',
            'totalAttendancesRecorded', 
            'totalEmployees', 
            'teachers',
            'isSupervisor',
            'teacherFilter',
            'search', 
            'month'
        ));
    }

    /**
     * Simpan data laporan kajian pekanan individu pegawai
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'speaker' => 'required|string|max:255',
            'date' => 'required|date',
            'time_start' => 'nullable|string|max:10',
            'time_end' => 'nullable|string|max:10',
            'location' => 'required|string|max:255',
            'material_summary' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx|max:10240',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'kajian_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kajian'), $filename);
            $attachmentPath = 'uploads/kajian/' . $filename;
        }

        $session = EmployeeStudySession::create([
            'title' => $validated['title'],
            'speaker' => $validated['speaker'],
            'date' => $validated['date'],
            'time_start' => $validated['time_start'] ?? null,
            'time_end' => $validated['time_end'] ?? null,
            'location' => $validated['location'],
            'material_summary' => $validated['material_summary'] ?? null,
            'attachment_path' => $attachmentPath,
            'created_by' => Auth::id(),
        ]);

        // Laporan individu: hanya catat kehadiran diri sendiri (tidak checkin guru lain)
        EmployeeStudyAttendance::firstOrCreate(
            [
                'session_id' => $session->id,
                'user_id' => Auth::id(),
            ],
            [
                'status' => 'hadir',
                'notes' => 'Laporan mandiri keikutsertaan kajian pekanan',
            ]
        );

        return redirect()->route('admin.kajian-pekanan.index')
            ->with('success', 'Laporan kegiatan kajian pekanan Anda berhasil disimpan dan diintegrasikan ke nilai KPI Pilar 4!');
    }

    /**
     * Detail kegiatan kajian + form rekap presensi pegawai
     */
    public function show(Request $request, EmployeeStudySession $kajian_pekanan)
    {
        $session = $kajian_pekanan;
        $session->load('creator');

        $user = auth()->user();
        if (!$this->isSupervisor() && $session->created_by != $user->id) {
            abort(403, 'Anda hanya dapat melihat laporan kajian pekanan yang Anda laporkan sendiri.');
        }

        // Laporan individu: hanya tampilkan data presensi pelapor / sesi ini
        $attendances = EmployeeStudyAttendance::with('user.roles')
            ->where('session_id', $session->id)
            ->get();

        $employees = $attendances->map(fn($att) => $att->user)->filter();
        if ($employees->isEmpty() && $session->creator) {
            $employees = collect([$session->creator]);
        }

        $existingAttendances = $attendances->keyBy('user_id');

        // Summary counts
        $hadirCount = $attendances->where('status', 'hadir')->count();
        $izinCount = $attendances->where('status', 'izin')->count();
        $sakitCount = $attendances->where('status', 'sakit')->count();
        $alpaCount = $attendances->where('status', 'alpa')->count();
        $totalCount = max(1, $attendances->count());
        $persentase = round(($hadirCount / $totalCount) * 100, 1);

        return view('admin.kajian-pekanan.show', compact(
            'session', 'employees', 'existingAttendances',
            'hadirCount', 'izinCount', 'sakitCount', 'alpaCount', 'totalCount', 'persentase'
        ));
    }

    /**
     * Simpan update rekap presensi massal pegawai
     */
    public function updateAttendance(Request $request, EmployeeStudySession $kajian_pekanan)
    {
        $session = $kajian_pekanan;
        $attendancesData = $request->input('attendance', []);

        DB::transaction(function () use ($session, $attendancesData) {
            foreach ($attendancesData as $userId => $data) {
                EmployeeStudyAttendance::updateOrCreate(
                    [
                        'session_id' => $session->id,
                        'user_id' => $userId,
                    ],
                    [
                        'status' => in_array($data['status'] ?? '', ['hadir', 'izin', 'sakit', 'alpa']) ? $data['status'] : 'hadir',
                        'notes' => $data['notes'] ?? null,
                    ]
                );
            }
        });

        return redirect()->route('admin.kajian-pekanan.show', $session->id)
            ->with('success', 'Rekap kehadiran pegawai pada kajian pekanan ini berhasil diperbarui!');
    }

    /**
     * Update metadata kegiatan kajian
     */
    public function update(Request $request, EmployeeStudySession $kajian_pekanan)
    {
        $session = $kajian_pekanan;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'speaker' => 'required|string|max:255',
            'date' => 'required|date',
            'time_start' => 'nullable|string|max:10',
            'time_end' => 'nullable|string|max:10',
            'location' => 'required|string|max:255',
            'material_summary' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx|max:10240',
        ]);

        if ($request->hasFile('attachment')) {
            if ($session->attachment_path && File::exists(public_path($session->attachment_path))) {
                File::delete(public_path($session->attachment_path));
            }
            $file = $request->file('attachment');
            $filename = 'kajian_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kajian'), $filename);
            $validated['attachment_path'] = 'uploads/kajian/' . $filename;
        }

        $session->update($validated);

        return redirect()->route('admin.kajian-pekanan.show', $session->id)
            ->with('success', 'Informasi kegiatan kajian berhasil diperbarui!');
    }

    /**
     * Hapus kegiatan kajian beserta riwayat presensinya
     */
    public function destroy(EmployeeStudySession $kajian_pekanan)
    {
        if ($kajian_pekanan->attachment_path && File::exists(public_path($kajian_pekanan->attachment_path))) {
            File::delete(public_path($kajian_pekanan->attachment_path));
        }

        $kajian_pekanan->delete();

        return redirect()->route('admin.kajian-pekanan.index')
            ->with('success', 'Kegiatan kajian pekanan dan riwayat kehadirannya berhasil dihapus!');
    }

    /**
     * Cetak Berita Acara / Laporan Kehadiran Kajian Pekanan
     */
    public function printReport(Request $request, EmployeeStudySession $kajian_pekanan)
    {
        $session = $kajian_pekanan;
        $attendances = EmployeeStudyAttendance::with('user.roles')
            ->where('session_id', $session->id)
            ->join('users', 'employee_study_attendances.user_id', '=', 'users.id')
            ->orderBy('users.name', 'asc')
            ->select('employee_study_attendances.*')
            ->get();

        $raportSettings = [
            'school_name' => Setting::get('school_name', 'SDIT AL-FAHMI PALU'),
            'school_address' => Setting::get('school_address', 'Jl. Pendidikan No. 123'),
            'school_city' => Setting::get('school_city', 'Palu'),
            'school_phone' => Setting::get('school_phone', '-'),
            'school_logo' => Setting::get('letterhead_logo_path') ? asset(Setting::get('letterhead_logo_path')) : Setting::getLogoUrl(),
            'principal_name' => Setting::get('school_principal_name', 'Kepala Sekolah'),
            'principal_nip' => Setting::get('school_principal_nip', '-'),
        ];

        return view('admin.kajian-pekanan.print', compact('session', 'attendances', 'raportSettings'));
    }
}
