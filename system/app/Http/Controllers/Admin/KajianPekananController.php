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
     * Tampilkan daftar kegiatan kajian pekanan pegawai
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $month = $request->input('month');

        $query = EmployeeStudySession::with(['creator', 'attendances.user'])
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

        $sessions = $query->paginate(10)->withQueryString();

        // Statistik Keseluruhan
        $totalSessions = EmployeeStudySession::count();
        $totalAttendancesRecorded = EmployeeStudyAttendance::where('status', 'hadir')->count();
        $totalEmployees = User::whereDoesntHave('roles', function ($q) {
            $q->where('slug', 'student');
        })->count();

        return view('admin.kajian-pekanan.index', compact('sessions', 'totalSessions', 'totalAttendancesRecorded', 'totalEmployees', 'search', 'month'));
    }

    /**
     * Simpan data sesi kajian pekanan baru
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

        // Otomatis inisialisasi presensi untuk semua pegawai jika belum ada
        $employees = User::whereDoesntHave('roles', function ($q) {
            $q->where('slug', 'student');
        })->get();

        foreach ($employees as $emp) {
            EmployeeStudyAttendance::firstOrCreate(
                [
                    'session_id' => $session->id,
                    'user_id' => $emp->id,
                ],
                [
                    'status' => 'hadir', // Default hadir untuk kemudahan penginputan
                    'notes' => null,
                ]
            );
        }

        return redirect()->route('admin.kajian-pekanan.show', $session->id)
            ->with('success', 'Kegiatan kajian pekanan berhasil dijadwalkan! Silakan sesuaikan daftar kehadiran pegawai.');
    }

    /**
     * Detail kegiatan kajian + form rekap presensi pegawai
     */
    public function show(Request $request, EmployeeStudySession $kajian_pekanan)
    {
        $session = $kajian_pekanan;
        $session->load('creator');

        // Pastikan semua pegawai memiliki baris presensi untuk sesi ini
        $employees = User::with('roles')->whereDoesntHave('roles', function ($q) {
            $q->where('slug', 'student');
        })->orderBy('name', 'asc')->get();

        $existingAttendances = EmployeeStudyAttendance::where('session_id', $session->id)
            ->get()
            ->keyBy('user_id');

        // Inisialisasi jika ada pegawai baru yang belum ada di daftar sesi
        foreach ($employees as $emp) {
            if (!$existingAttendances->has($emp->id)) {
                $newAtt = EmployeeStudyAttendance::create([
                    'session_id' => $session->id,
                    'user_id' => $emp->id,
                    'status' => 'hadir',
                    'notes' => null,
                ]);
                $existingAttendances->put($emp->id, $newAtt);
            }
        }

        // Summary counts
        $hadirCount = $session->hadir_count;
        $izinCount = $session->izin_count;
        $sakitCount = $session->sakit_count;
        $alpaCount = $session->alpa_count;
        $totalCount = $employees->count();
        $persentase = $totalCount > 0 ? round(($hadirCount / $totalCount) * 100, 1) : 0;

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
