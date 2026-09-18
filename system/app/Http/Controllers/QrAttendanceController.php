<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QrAttendanceController extends Controller
{
    /**
     * Show QR scanner page for teachers/admin
     */
    public function scan()
    {
        $classes = ClassModel::all();
        $students = Student::with('class')->get();
        return view('admin.attendances.scan', compact('classes', 'students'));
    }

    /**
     * Process QR code scan and record attendance
     */
    public function processScan(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required|string',
            'status' => 'nullable|string|in:auto,present,late,hadir,terlambat',
        ]);

        $statusInput = $request->status;
        if (!$statusInput || $statusInput === 'auto') {
            $lateTime = \App\Models\Setting::get('attendance_late_time', '07:15');
            $nowTime = now()->format('H:i');
            $status = ($nowTime <= $lateTime) ? 'present' : 'late';
        } else {
            $status = in_array($statusInput, ['hadir', 'present']) ? 'present' : 'late';
        }

        try {
            $rawQr = trim($request->qr_data);
            $qrData = json_decode($rawQr, true);

            if (is_array($qrData) && isset($qrData['student_id'])) {
                $student = Student::with(['user', 'class'])->find($qrData['student_id']);
            } else {
                // Pencarian langsung berdasarkan NISN, kode QR, atau ID
                $student = Student::with(['user', 'class'])
                    ->where('nisn', $rawQr)
                    ->orWhere('qr_code', $rawQr)
                    ->orWhere('id', $rawQr)
                    ->first();
            }

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data NISN / QR Code siswa tidak ditemukan (' . $rawQr . ')',
                ], 404);
            }

            $classId = $request->class_id ?: ($student->class_id ?? \App\Models\ClassModel::first()?->id);

            $today = Carbon::today();

            // Check if attendance already exists for today
            $existingAttendance = Attendance::where('student_id', $student->id)
                ->where('date', $today)
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kehadiran siswa ini sudah dicatat hari ini',
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->user->name ?? $student->name,
                        'nisn' => $student->nisn,
                        'class' => $student->class->name ?? '-',
                    ],
                    'attendance' => [
                        'status' => $existingAttendance->status,
                        'time' => $existingAttendance->created_at->format('H:i'),
                    ],
                ], 400);
            }

            // Create attendance record
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'class_id' => $classId,
                'date' => $today,
                'status' => $status,
                'scanned_via_qr' => true,
                'scanner_id' => Auth::id(),
                'recorded_by' => Auth::id(),
                'notes' => 'QR Scan Global at ' . now()->format('H:i'),
            ]);

            // Trigger WhatsApp Notification to Parent if enabled in settings
            $waEnabled = \App\Models\Setting::get('attendance_wa_notify_parents', '1') == '1';
            $parentPhone = $student->parent_phone ?? $student->phone;
            if ($waEnabled && $parentPhone) {
                $statusText = match ($status) {
                    'present', 'hadir' => 'HADIR TEPAT WAKTU',
                    'late', 'terlambat' => 'TERLAMBAT',
                    'sakit' => 'SAKIT',
                    'izin' => 'IZIN',
                    default => strtoupper($status),
                };
                $schoolName = \App\Models\Setting::get('school_name', 'Sekolah');
                $waMsg = "Pemberitahuan Presensi - {$schoolName}\n\nSiswa a.n. *{$student->user->name}* (NISN: {$student->nisn}) telah terdeteksi *{$statusText}* pada jam " . now()->format('H:i') . " WIB tanggal " . now()->format('d/m/Y') . ".\n\nTerima kasih.";

                \App\Services\WhatsAppService::sendMessage($parentPhone, $waMsg);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran berhasil dicatat!',
                'student' => [
                    'id' => $student->id,
                    'name' => $student->user->name,
                    'nisn' => $student->nisn,
                    'class' => $student->class->name ?? '-',
                ],
                'attendance' => [
                    'id' => $attendance->id,
                    'status' => $attendance->status,
                    'time' => $attendance->created_at->format('H:i'),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get today's attendance summary
     */
    public function todaySummary()
    {
        $today = Carbon::today();
        
        $attendances = Attendance::where('date', $today)
            ->where('scanned_via_qr', true)
            ->with(['student.user', 'student.class', 'scanner'])
            ->latest()
            ->get();

        $summary = [
            'total' => Attendance::where('date', $today)->count(),
            'qr_scans' => Attendance::where('date', $today)->where('scanned_via_qr', true)->count(),
            'manual' => Attendance::where('date', $today)->where('scanned_via_qr', false)->count(),
        ];

        return response()->json([
            'summary' => $summary,
            'attendances' => $attendances,
        ]);
    }

    /**
     * Show QR code history
     */
    public function history(Request $request)
    {
        $query = Attendance::with(['student.user', 'student.class', 'scanner'])
            ->where('scanned_via_qr', true)
            ->when($request->class_id, function ($q) use ($request) {
                return $q->where('class_id', $request->class_id);
            })
            ->when($request->major_id, function ($q) use ($request) {
                return $q->whereHas('student', function ($sq) use ($request) {
                    $sq->where('major_id', $request->major_id)
                       ->orWhereHas('class', fn($cq) => $cq->where('major_id', $request->major_id));
                });
            })
            ->when($request->start_date, function ($q) use ($request) {
                return $q->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                return $q->where('date', '<=', $request->end_date);
            })
            ->when($request->date && !$request->start_date && !$request->end_date, function ($q) use ($request) {
                return $q->where('date', $request->date);
            })
            ->when($request->student_id, function ($q) use ($request) {
                return $q->where('student_id', $request->student_id);
            })
            ->latest('date');

        $attendances = $query->paginate(20);
        $classes = ClassModel::all();
        $majors = Major::all();
        $students = Student::with(['user', 'class'])->get();

        return view('admin.attendances.qr-history', compact('attendances', 'classes', 'majors', 'students'));
    }
}
