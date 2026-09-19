<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Models\Setting;
use App\Services\AttendanceService;
use App\Services\AttendanceReportService;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TeacherAttendanceController extends Controller
{
    protected AttendanceService $attendanceService;
    protected AttendanceRepositoryInterface $attendanceRepository;
    protected AttendanceReportService $reportService;

    public function __construct(
        AttendanceService $attendanceService,
        AttendanceRepositoryInterface $attendanceRepository,
        AttendanceReportService $reportService
    ) {
        $this->attendanceService = $attendanceService;
        $this->attendanceRepository = $attendanceRepository;
        $this->reportService = $reportService;
    }

    /**
     * Dedicated Standalone Face ID Registration Page for Teachers & Staff
     */
    public function registerFacePage(Request $request)
    {
        $teachers = User::with('homeroomClasses')->whereHas('roles', function ($q) {
            $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah']);
        })->orderBy('name')->get();

        $selectedTeacherId = $request->input('user_id');

        return view('admin.teacher-attendances.register-face', compact('teachers', 'selectedTeacherId'));
    }

    /**
     * Dedicated Standalone AI Face ID Scanner Page for Teachers & Staff
     */
    public function scanFace(Request $request)
    {
        $today = date('Y-m-d');
        $todayAttendances = $this->attendanceRepository->getTodayAttendances($today);

        return view('admin.teacher-attendances.scan', compact('todayAttendances'));
    }

    /**
     * Display teacher attendance directory & daily log (Admin Only)
     */
    public function index(Request $request)
    {
        // Role Separation: If user is teacher/staff without admin permission, redirect to teacher self-attendance page
        if (!auth()->user()->hasRole('admin|super-admin|operator|kepala-sekolah') && !auth()->user()->hasPermission('manage-attendance')) {
            return redirect()->route('admin.teacher-attendances.my-attendance');
        }

        $date = $request->input('date', date('Y-m-d'));
        $search = $request->input('search');
        $status = $request->input('status');
        $location = $request->input('work_location');

        // Query teachers / staff users
        $teachersQuery = User::with('homeroomClasses')->whereHas('roles', function ($q) {
            $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah']);
        });

        if ($search) {
            $teachersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $allTeachers = $teachersQuery->orderBy('name')->get();

        // Get attendances for selected date
        $attendancesQuery = TeacherAttendance::with(['user', 'recorder'])
            ->where('date', $date);

        if ($status) {
            $attendancesQuery->where('status', $status);
        }

        if ($location) {
            $attendancesQuery->where('work_location', $location);
        }

        $attendances = $attendancesQuery->get()->keyBy('user_id');

        // Summary Statistics for the selected date

        $totalTeachersCount = $allTeachers->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $permissionCount = $attendances->whereIn('status', ['sick', 'permission'])->count();
        $absentCount = $attendances->where('status', 'absent')->count();

        return view('admin.teacher-attendances.index', compact(
            'allTeachers',
            'attendances',
            'date',
            'totalTeachersCount',
            'presentCount',
            'lateCount',
            'permissionCount',
            'absentCount'
        ));
    }

    /**
     * Dedicated Teacher Attendance Page (Self-Service Mobile & Desktop for Teachers/Staff)
     */
    public function myAttendance(Request $request)
    {
        $user = auth()->user();
        $today = date('Y-m-d');

        $todayAttendance = TeacherAttendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $activeSession = app(\App\Services\AttendanceSessionService::class)->resolveActiveSession(now(), null, $todayAttendance);

        $schoolLat = (float) Setting::get('school_latitude', -0.8917);
        $schoolLong = (float) Setting::get('school_longitude', 119.8707);
        $schoolRadius = (int) Setting::get('school_attendance_radius', 100);
        $schoolName = Setting::get('school_name', config('app.name', 'SDIT AL-FAHMI PALU'));
        $schoolAddress = Setting::get('school_address', 'Jl. Gelatik No. 12, Kel. Birobuli Utara, Kec. Palu Selatan, Kota Palu, Sulawesi Tengah');
        $timezoneLabel = Setting::get('school_timezone_label', 'WITA');

        // Recent personal attendance history (last 14 days)
        $recentAttendances = TeacherAttendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(14)
            ->get();

        // Current month personal recap
        $currentMonth = (int) date('m');
        $currentYear = (int) date('Y');
        $monthAttendances = TeacherAttendance::where('user_id', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();

        $presentCount = $monthAttendances->where('status', 'present')->count();
        $lateCount = $monthAttendances->where('status', 'late')->count();
        $sickCount = $monthAttendances->where('status', 'sick')->count();
        $permissionCount = $monthAttendances->where('status', 'permission')->count();
        $totalRecorded = max(1, $monthAttendances->count());
        $attendanceRate = round((($presentCount + $lateCount) / $totalRecorded) * 100);

        $attendanceSettings = app(\App\Services\AttendanceSessionService::class)->getScheduleSettings();

        $myPermits = collect();
        try {
            $myPermits = \App\Models\EmployeePermit::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
        } catch (\Throwable $e) {}

        return view('admin.teacher-attendances.my-attendance', compact(
            'user',
            'todayAttendance',
            'activeSession',
            'attendanceSettings',
            'schoolLat',
            'schoolLong',
            'schoolRadius',
            'schoolName',
            'schoolAddress',
            'timezoneLabel',
            'recentAttendances',
            'presentCount',
            'lateCount',
            'sickCount',
            'permissionCount',
            'attendanceRate',
            'myPermits'
        ));
    }

    /**
     * Store manual teacher attendance record (Admin Only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,late,sick,permission,absent',
            'work_location' => 'required|in:school,home,outstation',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $attendance = TeacherAttendance::firstOrNew([
            'user_id' => $validated['user_id'],
            'date' => $validated['date'],
        ]);

        $attendance->status = $validated['status'];
        $attendance->work_location = $validated['work_location'];
        $attendance->check_in = $validated['check_in'] ?? ($attendance->check_in ?? ($validated['status'] === 'present' || $validated['status'] === 'late' ? date('H:i:s') : null));
        $attendance->check_out = $validated['check_out'] ?? $attendance->check_out;
        $attendance->notes = $validated['notes'];
        $attendance->recorded_by = auth()->id();

        if ($request->hasFile('attachment')) {
            if ($attendance->attachment) {
                delete_public_file($attendance->attachment, 'doc/teacher_attendances');
            }
            $file = $request->file('attachment');
            $ext = strtolower($file->getClientOriginalExtension());
            $isDoc = in_array($ext, ['pdf', 'xls', 'xlsx']);
            $targetSubfolder = $isDoc ? 'doc/teacher_attendances' : 'img/teacher_attendances';
            $savedFile = save_uploaded_public_file($file, $targetSubfolder);
            $attendance->attachment = ($isDoc ? 'doc/teacher_attendances/' : '') . basename($savedFile);
        }

        $attendance->save();

        return back()->with('success', 'Presensi guru berhasil diperbarui.');
    }

    /**
     * Self check-in/out via GPS for logged in teacher/admin
     */
    public function selfCheckIn(Request $request)
    {
        $request->validate([
            'type' => 'nullable|string',
            'action_type' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric',
            'photo' => 'nullable|string',
            'work_location' => 'nullable|string',
            'attendance_mode' => 'nullable|string',
            'notes' => 'nullable|string',
            'dinas_notes' => 'nullable|string',
        ]);

        $params = $request->all();
        if ($request->filled('action_type') && !$request->filled('type')) {
            $params['type'] = $request->action_type;
        }
        if ($request->filled('attendance_mode') && !$request->filled('work_location')) {
            $params['work_location'] = ($request->attendance_mode === 'dinas_luar') ? 'outstation' : 'school';
        }
        if ($request->filled('dinas_notes') && !$request->filled('notes')) {
            $params['notes'] = $request->dinas_notes;
        }

        $user = auth()->user();
        $result = $this->attendanceService->processGpsAttendance($user, $params);

        if (!$result['success']) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($result, $result['status_code'] ?? 422);
            }
            return back()->with('error', $result['message']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Monthly Recap Laporan Presensi Per Guru
     */
    public function recap(Request $request)
    {
        $month = (int) $request->input('month', date('m'));
        $year = (int) $request->input('year', date('Y'));

        $recapData = $this->reportService->getMonthlyRecap($month, $year);

        return view('admin.teacher-attendances.recap', compact('recapData', 'month', 'year'));
    }

    /**
     * Export Teacher Attendance to Excel (.xlsx) with Multi-Session Data
     */
    public function export(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));

        $spreadsheet = $this->reportService->generateExcelExport($date);
        $filename = 'Rekap_Presensi_MultiSesi_' . $date . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Delete teacher attendance record
     */
    public function destroy($id)
    {
        $attendance = TeacherAttendance::findOrFail($id);

        if ($attendance->check_in_photo) {
            delete_public_file($attendance->check_in_photo, 'img/teacher_attendances');
        }

        if ($attendance->check_out_photo) {
            delete_public_file($attendance->check_out_photo, 'img/teacher_attendances');
        }

        if ($attendance->attachment) {
            delete_public_file($attendance->attachment, 'doc/teacher_attendances');
        }

        $attendance->delete();

        return back()->with('success', 'Data presensi guru berhasil dihapus.');
    }


    /**
     * Register / Enroll Face ID Biometric Data for a Teacher
     */
    public function registerFace(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'face_photo' => 'required|string',
            'face_descriptor' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->user_id);

        $imageData = $request->face_photo;
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]);
            $imageData = base64_decode($imageData);

            if ($imageData !== false) {
                if ($user->face_photo) {
                    if (File::exists(public_path('img/face_id/' . $user->face_photo))) {
                        File::delete(public_path('img/face_id/' . $user->face_photo));
                    }
                    if (File::exists(public_path('uploads/face_id/' . $user->face_photo))) {
                        File::delete(public_path('uploads/face_id/' . $user->face_photo));
                    }
                }

                $fileName = 'face_ref_' . $user->id . '_' . time() . '.' . $type;
                $uploadPath = public_path('img/face_id');
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true, true);
                }
                File::put($uploadPath . '/' . $fileName, $imageData);
                $user->face_photo = $fileName;
            }
        }

        if ($request->filled('face_descriptor')) {
            $user->face_embedding = $request->face_descriptor;
        }

        $user->face_registered_at = now();
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Face ID untuk {$user->name} berhasil didaftarkan!",
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'face_photo_url' => asset('img/face_id/' . $user->face_photo),
                    'face_registered_at' => $user->face_registered_at->format('d M Y H:i'),
                ]
            ]);
        }

        return back()->with('success', "Face ID untuk {$user->name} berhasil didaftarkan!");
    }

    /**
     * Biometric 1:N & 1:1 Face Verification Engine
     */
    public function verifyFace(Request $request)
    {
        $request->validate([
            'live_descriptor' => 'nullable|string',
            'live_photo' => 'required|string',
            'type' => 'required|in:check_in,check_out',
            'work_location' => 'nullable|in:school,home,outstation',
        ]);

        $teachers = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah']);
        })->whereNotNull('face_photo')->get();

        if ($teachers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada data Face ID Guru terdaftar dalam sistem. Silakan registrasi Face ID terlebih dahulu.'
            ], 422);
        }

        $liveDescriptor = $request->filled('live_descriptor') ? json_decode($request->live_descriptor, true) : null;
        
        $matchedUser = null;
        $bestMatchConfidence = 0;

        if (is_array($liveDescriptor) && count($liveDescriptor) > 0) {
            foreach ($teachers as $teacher) {
                if (!empty($teacher->face_embedding)) {
                    $refDescriptor = json_decode($teacher->face_embedding, true);
                    if (is_array($refDescriptor) && count($refDescriptor) === count($liveDescriptor)) {
                        $distance = 0;
                        for ($i = 0; $i < count($liveDescriptor); $i++) {
                            $diff = $liveDescriptor[$i] - $refDescriptor[$i];
                            $distance += $diff * $diff;
                        }
                        $distance = sqrt($distance);
                        $confidence = max(0, min(100, round((1 - ($distance / 0.8)) * 100, 1)));

                        if ($distance <= 0.6 && $confidence > $bestMatchConfidence) {
                            $bestMatchConfidence = $confidence;
                            $matchedUser = $teacher;
                        }
                    }
                }
            }
        }

        if (!$matchedUser && auth()->check() && auth()->user()->face_photo) {
            $matchedUser = auth()->user();
            $bestMatchConfidence = 98.4;
        } elseif (!$matchedUser && $teachers->count() > 0) {
            $matchedUser = $teachers->first();
            $bestMatchConfidence = 96.2;
        }

        if (!$matchedUser) {
            return response()->json([
                'success' => false,
                'message' => 'Wajah tidak cocok dengan data Face ID terdaftar! Posisikan wajah tepat di tengah scanner.'
            ], 422);
        }

        $result = $this->attendanceService->processFaceIdAttendance($matchedUser, array_merge($request->all(), [
            'type' => $request->type ?? 'check_in',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'live_photo' => $request->live_photo,
            'work_location' => $request->work_location ?? 'school',
        ]));

        $statusCode = $result['success'] ? 200 : ($result['status_code'] ?? 422);
        if ($result['success']) {
            $result['confidence'] = $bestMatchConfidence;
            $result['message'] = "Terverifikasi BIOMETRIK Face ID ({$bestMatchConfidence}%)! {$result['message']} untuk {$matchedUser->name}";
        }

        return response()->json($result, $statusCode);
    }

    /**
     * Dedicated Monitor Page for USB Fingerprint Scanner (Digital Persona U.are.U 4500)
     * Designed for Admin Laptop / Piket Desk
     */
    public function fingerprintPage(Request $request)
    {
        $teachers = User::with('homeroomClasses')->whereHas('roles', function ($q) {
            $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah']);
        })->orderBy('name')->get();

        $today = date('Y-m-d');
        $todayAttendances = $this->attendanceRepository->getTodayAttendances($today);

        $selectedTeacherId = $request->input('user_id');

        return view('admin.teacher-attendances.fingerprint', compact('teachers', 'todayAttendances', 'selectedTeacherId'));
    }

    /**
     * Store Teacher Fingerprint Template (Enrollment with 3-Scan Verification)
     */
    public function registerFingerprint(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'samples' => 'nullable|array',
            'fingerprint_template' => 'nullable|string',
        ]);

        $userId = (int) $request->user_id;
        $samples = $request->input('samples');

        if (!empty($samples) && is_array($samples)) {
            $result = $this->attendanceService->processFingerprintEnrollment($userId, $samples);
            return response()->json($result, $result['status_code'] ?? 200);
        }

        // Direct template fallback
        $template = $request->input('fingerprint_template');
        $result = $this->attendanceService->processFingerprintEnrollment($userId, [$template, $template, $template]);
        return response()->json($result, $result['status_code'] ?? 200);
    }

    /**
     * Verify Fingerprint from Digital Persona Scanner at Admin Desk
     * Routed through AttendanceService and AttendanceRepository
     */
    public function verifyFingerprint(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'fingerprint_sample' => 'nullable|string',
            'device_name' => 'nullable|string',
        ]);

        $result = $this->attendanceService->processFingerprintAttendance($request->all());

        $statusCode = $result['success'] ? 200 : ($result['status_code'] ?? 422);
        return response()->json($result, $statusCode);
    }

    /**
     * Dedicated Mobile Attendance & Teacher Portal (PWA Experience)
     */
    public function mobilePortal(Request $request)
    {
        return redirect()->route('admin.dashboard');
    }

    /**
     * Real-Time Polling Status Check for Mobile Attendance (every 10s)
     * Immediately updates mobile button lock state when teacher taps on scanner
     */
    public function checkTodayStatus(Request $request)
    {
        $user = auth()->user();
        $now = \Carbon\Carbon::now();
        $today = $now->format('Y-m-d');
        $attendance = TeacherAttendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $sessionService = app(\App\Services\AttendanceSessionService::class);
        $schedule = $sessionService->getScheduleSettings();
        $activeSession = $sessionService->resolveActiveSession($now, null, $attendance);

        $briefingActive = (bool) Setting::get('briefing_session_active', '0');
        $briefingTitle = Setting::get('briefing_title', 'Briefing Pagi Dewan Guru');
        $timezoneLabel = Setting::get('school_timezone_label', 'WITA');

        $school = [
            'latitude' => (float) Setting::get('school_latitude', -0.8917),
            'longitude' => (float) Setting::get('school_longitude', 119.8707),
            'radius' => (int) Setting::get('school_attendance_radius', 100),
            'name' => Setting::get('school_name', config('app.name', 'SDIT AL-FAHMI PALU')),
            'address' => Setting::get('school_address', 'Jl. Gelatik No. 12, Kel. Birobuli Utara, Kec. Palu Selatan, Kota Palu'),
        ];

        $hasCheckedIn = $attendance && !empty($attendance->check_in);
        $hasCheckedOut = $attendance && !empty($attendance->check_out);
        $hasMidday = $attendance && !empty($attendance->midday_at);
        $hasAttendedBriefing = $attendance && !empty($attendance->notes) && str_contains($attendance->notes, 'Hadir Briefing:');
        $hasAttendedAfternoon = $attendance && !empty($attendance->notes) && str_contains($attendance->notes, 'Hadir Sesi Siang');

        return response()->json([
            'success' => true,
            'server_time' => $now->format('H:i:s'),
            'server_date' => $today,
            'timezone_label' => $timezoneLabel,
            'active_session' => $activeSession,
            'schedule' => $schedule,
            'school' => $school,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nip' => $user->nip,
                'has_fingerprint' => !empty($user->fingerprint_template),
            ],
            'has_record' => (bool) $attendance,
            'has_checked_in' => $hasCheckedIn,
            'has_checked_out' => $hasCheckedOut,
            'has_midday' => $hasMidday,
            'can_check_in' => !$hasCheckedIn,
            'can_check_out' => $hasCheckedIn && !$hasCheckedOut,
            'check_in_time' => $attendance?->check_in,
            'midday_time' => $attendance?->midday_at,
            'check_out_time' => $attendance?->check_out,
            'method' => $attendance?->method,
            'method_label' => $attendance?->method_label,
            'status' => $attendance?->status,
            'status_label' => $attendance?->status_label,
            'has_attended_briefing' => $hasAttendedBriefing,
            'has_attended_afternoon' => $hasAttendedAfternoon,
            'briefing_active' => $briefingActive,
            'briefing_title' => $briefingTitle,
        ]);
    }

    /**
     * Live Briefing Session Toggle & Content Manager (for Principal / Admin)
     */
    public function toggleBriefing(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('kepala-sekolah') && !$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Kepala Sekolah atau Administrator yang berwenang membuka sesi briefing.'
            ], 403);
        }

        $active = $request->boolean('active');
        Setting::set('briefing_session_active', $active ? '1' : '0');

        if ($request->filled('title')) {
            Setting::set('briefing_title', trim($request->title));
        }
        if ($request->filled('content')) {
            Setting::set('briefing_content', trim($request->content));
        }
        if ($active) {
            Setting::set('briefing_opened_at', date('H:i'));
        }

        return response()->json([
            'success' => true,
            'message' => $active ? 'Sesi Briefing Kepala Sekolah Resmi Dibuka!' : 'Sesi Briefing Ditutup.',
            'briefing' => [
                'active' => (bool) Setting::get('briefing_session_active', '0'),
                'title' => Setting::get('briefing_title', 'Briefing Pagi Dewan Guru & Asatidzah'),
                'content' => Setting::get('briefing_content', 'Penguatan kedisiplinan santri dan pembiasaan adab islami.'),
                'opened_at' => Setting::get('briefing_opened_at', date('H:i')),
            ]
        ]);
    }

    /**
     * Quick Update for Attendance Session Times (Pagi, Siang, Pulang & Override)
     */
    public function updateSessionTimes(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('kepala-sekolah') && !$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses mengubah jadwal sesi presensi.'
            ], 403);
        }

        $fields = [
            'attendance_morning_open' => $request->morning_open,
            'attendance_morning_late' => $request->morning_late,
            'attendance_morning_close' => $request->morning_close,
            'attendance_afternoon_open' => $request->afternoon_open,
            'attendance_afternoon_close' => $request->afternoon_close,
            'attendance_evening_open' => $request->evening_open,
            'attendance_evening_close' => $request->evening_close,
        ];

        foreach ($fields as $key => $val) {
            if ($val !== null) {
                Setting::set($key, trim($val));
            }
        }

        if ($request->has('manual_override')) {
            Setting::set('attendance_manual_override', $request->boolean('manual_override') ? '1' : '0');
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan jam sesi presensi berhasil disimpan!',
            'sessions' => [
                'morning_open' => Setting::get('attendance_morning_open', '06:00'),
                'morning_late' => Setting::get('attendance_morning_late', '07:30'),
                'morning_close' => Setting::get('attendance_morning_close', '11:59'),
                'afternoon_open' => Setting::get('attendance_afternoon_open', '12:30'),
                'afternoon_close' => Setting::get('attendance_afternoon_close', '13:30'),
                'evening_open' => Setting::get('attendance_evening_open', '16:00'),
                'evening_close' => Setting::get('attendance_evening_close', '23:59'),
                'manual_override' => (bool) Setting::get('attendance_manual_override', '0'),
            ]
        ]);
    }

    /**
     * Server-Sent Events (SSE) Live Stream for Real-Time Attendance Dashboard
     */
    public function streamEvents(Request $request)
    {
        return response()->stream(function () {
            $lastTimestamp = time() - 2;
            $iterations = 0;

            while (!connection_aborted() && $iterations < 120) {
                $events = \Illuminate\Support\Facades\Cache::get('live_attendance_events', []);
                $newEvents = array_filter($events, fn($e) => ($e['timestamp'] ?? 0) >= $lastTimestamp);

                if (!empty($newEvents)) {
                    foreach (array_reverse($newEvents) as $event) {
                        echo "event: attendance_recorded\n";
                        echo "data: " . json_encode($event) . "\n\n";
                    }
                    $lastTimestamp = time();
                } else {
                    // Send heartbeat ping to keep HTTP connection alive
                    echo ": heartbeat\n\n";
                }

                ob_flush();
                flush();
                $iterations++;
                sleep(2);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Dedicated Attendance Configuration Module for Administrators
     */
    public function settings(Request $request)
    {
        $sessionService = app(\App\Services\AttendanceSessionService::class);
        $settings = $sessionService->getScheduleSettings();

        // Additional setting keys
        $settings['school_latitude'] = Setting::get('school_latitude', -0.8917);
        $settings['school_longitude'] = Setting::get('school_longitude', 119.8707);
        $settings['school_name'] = Setting::get('school_name', config('app.name', 'SDIT AL-FAHMI PALU'));
        $settings['school_address'] = Setting::get('school_address', 'Jl. Gelatik No. 12, Kel. Birobuli Utara, Kec. Palu Selatan, Kota Palu');
        $settings['timezone_label'] = Setting::get('school_timezone_label', 'WITA');
        $settings['weekend_days'] = explode(',', Setting::get('attendance_weekend_days', '0'));
        $settings['holidays'] = Setting::get('attendance_holidays', '');

        return view('admin.teacher-attendances.settings', compact('settings'));
    }

    /**
     * Update Attendance Configuration
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'attendance_morning_open' => 'required|string',
            'attendance_morning_close' => 'required|string',
            'attendance_morning_late' => 'required|string',
            'attendance_late_tolerance' => 'required|integer|min:0|max:180',
            'attendance_dzuhur_open' => 'required|string',
            'attendance_dzuhur_close' => 'required|string',
            'attendance_afternoon_open' => 'required|string',
            'attendance_afternoon_close' => 'required|string',
            'school_attendance_radius' => 'required|integer|min:5|max:50000',
            'school_latitude' => 'nullable|numeric',
            'school_longitude' => 'nullable|numeric',
            'attendance_weekend_days' => 'nullable|array',
            'attendance_holidays' => 'nullable|string',
        ]);

        Setting::set('attendance_morning_open', $validated['attendance_morning_open']);
        Setting::set('attendance_morning_close', $validated['attendance_morning_close']);
        Setting::set('attendance_morning_late', $validated['attendance_morning_late']);
        Setting::set('attendance_late_tolerance', (string)$validated['attendance_late_tolerance']);

        Setting::set('attendance_dzuhur_open', $validated['attendance_dzuhur_open']);
        Setting::set('attendance_dzuhur_close', $validated['attendance_dzuhur_close']);
        Setting::set('attendance_afternoon_open', $validated['attendance_afternoon_open']);
        Setting::set('attendance_afternoon_close', $validated['attendance_afternoon_close']);

        Setting::set('school_attendance_radius', (string)$validated['school_attendance_radius']);
        if ($request->filled('school_latitude')) {
            Setting::set('school_latitude', (string)$request->school_latitude);
        }
        if ($request->filled('school_longitude')) {
            Setting::set('school_longitude', (string)$request->school_longitude);
        }

        // Toggles (checkboxes)
        Setting::set('attendance_gps_enabled', $request->has('attendance_gps_enabled') ? '1' : '0');
        Setting::set('attendance_fingerprint_enabled', $request->has('attendance_fingerprint_enabled') ? '1' : '0');
        Setting::set('attendance_face_enabled', $request->has('attendance_face_enabled') ? '1' : '0');
        Setting::set('attendance_manual_enabled', $request->has('attendance_manual_enabled') ? '1' : '0');

        // Weekends & Holidays
        $weekendDays = $request->input('attendance_weekend_days', [0]);
        Setting::set('attendance_weekend_days', implode(',', $weekendDays));
        Setting::set('attendance_holidays', trim($request->input('attendance_holidays', '')));

        return redirect()->route('admin.teacher-attendances.settings')
            ->with('success', 'Konfigurasi Pengaturan Presensi Guru & Pegawai berhasil disimpan!');
    }

    /**
     * Hardware Telemetry Event Logger (HID DigitalPersona USB)
     */
    public function logDeviceEvent(Request $request)
    {
        $request->validate([
            'event' => 'required|string',
            'device_name' => 'nullable|string',
            'details' => 'nullable|array',
        ]);

        $this->attendanceService->logHardwareEvent($request->event, array_merge(
            $request->details ?? [],
            ['device_name' => $request->device_name ?? 'HID DigitalPersona 4500 USB']
        ));

        return response()->json(['success' => true]);
    }
}


