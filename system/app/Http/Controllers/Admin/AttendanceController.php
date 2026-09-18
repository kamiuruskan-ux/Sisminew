<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Major;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Attendance::with(['student', 'class', 'recordedBy'])
            ->when($user->isTeacher(), function ($q) use ($user) {
                return $q->where('recorded_by', $user->id);
            })
            ->when($request->class_id, function ($q) use ($request) {
                return $q->where('class_id', $request->class_id);
            })
            ->when($request->major_id, function ($q) use ($request) {
                return $q->whereHas('student', function ($sq) use ($request) {
                    $sq->where('major_id', $request->major_id)
                       ->orWhereHas('class', fn($cq) => $cq->where('major_id', $request->major_id));
                });
            })
            ->when($request->student_id, function ($q) use ($request) {
                return $q->where('student_id', $request->student_id);
            })
            ->when($request->start_date, function ($q) use ($request) {
                return $q->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                return $q->where('date', '<=', $request->end_date);
            })
            ->when($request->date, function ($q) use ($request) {
                return $q->where('date', $request->date);
            })
            ->when($request->status, function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->latest('date');

        $attendances = $query->paginate(20);
        $classes = ClassModel::all();
        $majors = Major::all();
        $students = Student::with(['user', 'class'])->get();

        return view('admin.attendances.index', compact('attendances', 'classes', 'majors', 'students'));
    }

    /**
     * Print PDF Report for Attendance with Letterhead Kop Surat.
     */
    public function printReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($request->filled('date')) {
            $startDate = $request->input('date');
            $endDate = $request->input('date');
        } elseif (!$startDate && !$endDate) {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        } elseif ($startDate && !$endDate) {
            $endDate = $startDate;
        } elseif (!$startDate && $endDate) {
            $startDate = $endDate;
        }

        $classId = $request->input('class_id');
        $majorId = $request->input('major_id');
        $studentId = $request->input('student_id');
        $status = $request->input('status');
        $type = $request->input('type', 'all');

        $query = Attendance::with(['student.user', 'student.class.major', 'student.major', 'class', 'recordedBy'])
            ->whereBetween('date', [$startDate, $endDate])
            ->when($classId, function ($q) use ($classId) {
                return $q->where('class_id', $classId);
            })
            ->when($majorId, function ($q) use ($majorId) {
                return $q->whereHas('student', function ($sq) use ($majorId) {
                    $sq->where('major_id', $majorId)
                       ->orWhereHas('class', fn($cq) => $cq->where('major_id', $majorId));
                });
            })
            ->when($studentId, function ($q) use ($studentId) {
                return $q->where('student_id', $studentId);
            })
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->orderBy('date', 'asc');

        $attendances = $query->get();

        $selectedClass = $classId ? ClassModel::with('major')->find($classId) : null;
        $selectedMajor = $majorId ? Major::find($majorId) : null;

        // Group attendance summary per student
        $studentSummaries = $attendances->groupBy('student_id')->map(function ($items) {
            $student = $items->first()->student;
            $present = $items->where('status', 'present')->count();
            $late = $items->where('status', 'late')->count();
            $excused = $items->where('status', 'excused')->count();
            $absent = $items->where('status', 'absent')->count();
            $total = $items->count();

            return [
                'student' => $student,
                'present' => $present,
                'late' => $late,
                'excused' => $excused,
                'absent' => $absent,
                'total' => $total,
                'percentage' => $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0,
            ];
        });

        $raportSettings = [
            'school_name' => Setting::get('school_name', 'SMA Negeri 1 Contoh'),
            'school_address' => Setting::get('school_address', 'Jl. Pendidikan No. 123'),
            'school_city' => Setting::get('school_city', 'Jakarta Pusat'),
            'school_province' => Setting::get('school_province', 'DKI Jakarta'),
            'school_postal_code' => Setting::get('school_postal_code', '10110'),
            'school_phone' => Setting::get('school_phone', '(021) 1234567'),
            'school_email' => Setting::get('school_email', 'info@sekolah.sch.id'),
            'school_website' => Setting::get('school_website', 'www.sekolah.sch.id'),
            'school_logo' => Setting::get('letterhead_logo_path') ? asset(Setting::get('letterhead_logo_path')) : Setting::getLogoUrl(),
            'school_logo_right' => Setting::get('letterhead_logo_right_path') ? asset(Setting::get('letterhead_logo_right_path')) : null,
            'letterhead_header_top' => Setting::get('letterhead_header_top'),
            'letterhead_sub' => Setting::get('letterhead_sub'),
            'city' => Setting::get('school_city', 'Jakarta'),
            'date' => date('d F Y'),
            'principal_name' => Setting::get('school_principal_name', 'Kepala Sekolah, M.Pd.'),
            'principal_nip' => Setting::get('school_principal_nip', '-'),
            'stamp_path' => Setting::get('raport_stamp_path', Setting::get('student_card_stamp_path')) ? asset(Setting::get('raport_stamp_path', Setting::get('student_card_stamp_path'))) : null,
            'signature_path' => Setting::get('raport_signature_path', Setting::get('student_card_signature_path')) ? asset(Setting::get('raport_signature_path', Setting::get('student_card_signature_path'))) : null,
        ];

        return view('admin.attendances.print', compact(
            'attendances', 'studentSummaries', 'startDate', 'endDate', 'selectedClass', 'selectedMajor',
            'raportSettings', 'status', 'type'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = ClassModel::orderBy('name')->get();
        $majors = \App\Models\Major::orderBy('name')->get();
        $students = Student::with(['class', 'user', 'major'])->get();

        return view('admin.attendances.create', compact('classes', 'majors', 'students'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('attendances') && is_array($request->attendances)) {
            $request->validate([
                'class_id' => 'required|exists:classes,id',
                'date' => 'required|date',
                'attendances' => 'required|array',
            ]);

            $date = $request->date;
            $recordedBy = Auth::id();
            $count = 0;

            foreach ($request->attendances as $stId => $item) {
                if (empty($item['student_id'])) continue;
                $status = $item['status'] ?? 'present';

                Attendance::updateOrCreate(
                    [
                        'student_id' => $item['student_id'],
                        'date' => $date,
                    ],
                    [
                        'class_id' => $request->class_id,
                        'status' => $status,
                        'recorded_by' => $recordedBy,
                        'check_in' => $status === 'present' || $status === 'late' ? now()->format('H:i:s') : null,
                    ]
                );
                $count++;
            }

            return redirect()->route('admin.attendances.index')
                ->with('success', "Presensi {$count} siswa berhasil disimpan!");
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|string',
        ]);

        $validated['recorded_by'] = Auth::id();

        // Check if attendance already exists for this student on this date
        $existing = Attendance::where('student_id', $validated['student_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($existing) {
            $existing->update($validated);
            return redirect()->route('admin.attendances.index')
                ->with('success', 'Kehadiran siswa berhasil diperbarui!');
        }

        Attendance::create($validated);

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Kehadiran berhasil dicatat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        return view('admin.attendances.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        $classes = ClassModel::all();
        $students = Student::all();

        return view('admin.attendances.edit', compact('attendance', 'classes', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($validated);

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Kehadiran berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Kehadiran berhasil dihapus!');
    }

    /**
     * Display student attendance configuration settings page.
     */
    public function settings()
    {
        return view('admin.attendances.settings');
    }

    /**
     * Update student attendance configuration settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'attendance_active_method' => 'required|in:rfid,finger,face,qr',
            
            // RFID Device Settings
            'attendance_rfid_device_key' => 'nullable|string|max:255',

            // Fingerprint Device Settings
            'attendance_finger_device_ip' => 'nullable|string|max:255',
            'attendance_finger_device_port' => 'nullable|string|max:20',
            'attendance_finger_secret' => 'nullable|string|max:255',

            // Face Recognition Settings
            'attendance_face_confidence' => 'nullable|integer|min:50|max:100',

            // QR Code Scan Settings
            'attendance_qr_refresh_seconds' => 'nullable|integer|min:5|max:300',

            // Time & Rules
            'attendance_entry_time' => 'required|string',
            'attendance_late_time' => 'required|string',
            'attendance_exit_time' => 'required|string',
            'attendance_early_exit_tolerance' => 'nullable|integer|min:0|max:120',

            // Automation & Preferences
            'attendance_wa_notify_parents' => 'nullable|in:0,1',
            'attendance_sound_feedback' => 'nullable|in:0,1',
            'attendance_allow_self_checkin' => 'nullable|in:0,1',
        ]);

        $activeMethod = $request->input('attendance_active_method', 'qr');
        Setting::set('attendance_active_method', $activeMethod);
        Setting::set('attendance_default_method', $activeMethod);

        Setting::set('attendance_method_rfid', $activeMethod === 'rfid' ? '1' : '0');
        Setting::set('attendance_method_finger', $activeMethod === 'finger' ? '1' : '0');
        Setting::set('attendance_method_face', $activeMethod === 'face' ? '1' : '0');
        Setting::set('attendance_method_qr', $activeMethod === 'qr' ? '1' : '0');

        // Device specifics
        Setting::set('attendance_rfid_device_key', $request->input('attendance_rfid_device_key'));
        Setting::set('attendance_finger_device_ip', $request->input('attendance_finger_device_ip'));
        Setting::set('attendance_finger_device_port', $request->input('attendance_finger_device_port', '4370'));
        Setting::set('attendance_finger_secret', $request->input('attendance_finger_secret'));
        Setting::set('attendance_face_confidence', $request->input('attendance_face_confidence', '75'));
        Setting::set('attendance_qr_refresh_seconds', $request->input('attendance_qr_refresh_seconds', '30'));

        // Time Rules
        Setting::set('attendance_entry_time', $request->input('attendance_entry_time', '07:00'));
        Setting::set('attendance_late_time', $request->input('attendance_late_time', '07:15'));
        Setting::set('attendance_exit_time', $request->input('attendance_exit_time', '15:00'));
        Setting::set('attendance_early_exit_tolerance', $request->input('attendance_early_exit_tolerance', '15'));

        // Preferences
        Setting::set('attendance_wa_notify_parents', $request->has('attendance_wa_notify_parents') ? '1' : '0');
        Setting::set('attendance_sound_feedback', $request->has('attendance_sound_feedback') ? '1' : '0');
        Setting::set('attendance_allow_self_checkin', $request->has('attendance_allow_self_checkin') ? '1' : '0');

        return redirect()->route('admin.attendances.settings')
            ->with('success', 'Konfigurasi Pengaturan Presensi Siswa berhasil diperbarui!');
    }
}
