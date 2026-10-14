<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityAttendance;
use App\Models\ActivityEvent;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ActivityCalendarController extends Controller
{
    public function index(Request $request)
    {
        ActivityEvent::ensureTableExists();
        ActivityAttendance::ensureTableExists();

        $tab = $request->get('tab', 'calendar'); // 'calendar', 'attendance', 'recap'
        $month = (int) $request->get('month', date('m'));
        $year = (int) $request->get('year', date('Y'));

        // Events query
        $events = ActivityEvent::with(['creator', 'class'])
            ->where(function ($q) use ($month, $year) {
                $q->whereMonth('date_start', $month)->whereYear('date_start', $year)
                  ->orWhere(function ($sub) use ($month, $year) {
                      $sub->whereNotNull('date_end')
                          ->whereMonth('date_end', $month)
                          ->whereYear('date_end', $year);
                  });
            })
            ->orderBy('date_start', 'asc')
            ->orderBy('time_start', 'asc')
            ->get();

        // All active upcoming / recent events for attendance picker
        $allActiveEvents = ActivityEvent::where('is_active', true)
            ->orderByDesc('date_start')
            ->take(30)
            ->get();

        $selectedEventId = $request->get('event_id') ?? ($allActiveEvents->first()?->id ?? null);
        $selectedEvent = $selectedEventId ? ActivityEvent::with(['attendances.student.user', 'attendances.user'])->find($selectedEventId) : null;

        // Classes for student attendance
        $classes = ClassModel::orderBy('name', 'asc')->get();
        $selectedClassId = $request->get('class_id') ?? ($classes->first()?->id ?? null);

        // Participants list depending on target_audience
        $studentsList = collect();
        $teachersList = collect();

        if ($selectedEvent) {
            if (in_array($selectedEvent->target_audience, ['all', 'students', 'class'])) {
                $stQuery = Student::with(['user', 'class'])
                    ->where(function($q) {
                        $q->whereIn('student_status', ['active', 'Aktif'])->orWhereNull('student_status');
                    })
                    ->orderBy('nisn', 'asc');
                if ($selectedEvent->target_audience === 'class' && $selectedEvent->class_id) {
                    $stQuery->where('class_id', $selectedEvent->class_id);
                } elseif ($selectedClassId) {
                    $stQuery->where('class_id', $selectedClassId);
                }
                $studentsList = $stQuery->get();
            }

            if (in_array($selectedEvent->target_audience, ['all', 'teachers'])) {
                $teachersList = User::employees()->orderBy('name', 'asc')->get();
            }
        }

        // Existing attendances for selected event
        $existingAttendances = $selectedEvent 
            ? ActivityAttendance::where('event_id', $selectedEvent->id)->get()->keyBy(function($item) {
                return $item->participant_type === 'teacher' ? 't_' . $item->user_id : 's_' . $item->student_id;
            })
            : collect();

        // Calendar Grid Matrix Calculation
        $startOfMonth = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $startOfMonth->daysInMonth;
        $dayOfWeekOffset = ($startOfMonth->dayOfWeekIso - 1); // 0 (Mon) to 6 (Sun)

        return view('admin.activity-calendar.index', compact(
            'tab',
            'month',
            'year',
            'events',
            'allActiveEvents',
            'selectedEventId',
            'selectedEvent',
            'classes',
            'selectedClassId',
            'studentsList',
            'teachersList',
            'existingAttendances',
            'startOfMonth',
            'daysInMonth',
            'dayOfWeekOffset'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'date_start' => 'required|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'time_start' => 'nullable|string|max:10',
            'time_end' => 'nullable|string|max:10',
            'location' => 'nullable|string|max:255',
            'target_audience' => 'required|in:all,students,teachers,class',
            'class_id' => 'nullable|exists:classes,id',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;

        ActivityEvent::create($validated);

        return redirect()->route('admin.activity-calendar.index', [
            'month' => date('m', strtotime($validated['date_start'])),
            'year' => date('Y', strtotime($validated['date_start'])),
        ])->with('success', 'Agenda kegiatan sekolah berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $event = ActivityEvent::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'date_start' => 'required|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'time_start' => 'nullable|string|max:10',
            'time_end' => 'nullable|string|max:10',
            'location' => 'nullable|string|max:255',
            'target_audience' => 'required|in:all,students,teachers,class',
            'class_id' => 'nullable|exists:classes,id',
            'description' => 'nullable|string',
        ]);

        $event->update($validated);

        return back()->with('success', 'Agenda kegiatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $event = ActivityEvent::findOrFail($id);
        $event->attendances()->delete();
        $event->delete();

        return back()->with('success', 'Agenda kegiatan berhasil dihapus.');
    }

    public function saveAttendance(Request $request, $id)
    {
        $event = ActivityEvent::findOrFail($id);
        $user = auth()->user();

        $validated = $request->validate([
            'attendances' => 'required|array',
            'participant_type' => 'required|in:student,teacher',
        ]);

        $pType = $validated['participant_type'];
        $nowTime = date('H:i');

        DB::beginTransaction();
        try {
            foreach ($validated['attendances'] as $participantId => $item) {
                $status = $item['status'] ?? 'present';
                $notes = $item['notes'] ?? null;

                if ($pType === 'student') {
                    ActivityAttendance::updateOrCreate(
                        [
                            'event_id' => $event->id,
                            'participant_type' => 'student',
                            'student_id' => $participantId,
                        ],
                        [
                            'status' => $status,
                            'notes' => $notes,
                            'check_in_time' => $nowTime,
                            'recorded_by' => $user->id,
                        ]
                    );
                } else {
                    ActivityAttendance::updateOrCreate(
                        [
                            'event_id' => $event->id,
                            'participant_type' => 'teacher',
                            'user_id' => $participantId,
                        ],
                        [
                            'status' => $status,
                            'notes' => $notes,
                            'check_in_time' => $nowTime,
                            'recorded_by' => $user->id,
                        ]
                    );
                }
            }

            DB::commit();

            return back()->with('success', 'Presensi & absensi kegiatan berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    public function exportAttendance($id)
    {
        $event = ActivityEvent::with(['attendances.student.user', 'attendances.student.class', 'attendances.user'])->findOrFail($id);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Hadir');

        $sheet->setCellValue('A1', 'DAFTAR KEHADIRAN / ABSENSI KEGIATAN SEKOLAH');
        $sheet->setCellValue('A2', 'Kegiatan: ' . $event->title . ' (' . $event->category_label . ')');
        $sheet->setCellValue('A3', 'Tanggal: ' . $event->date_start->format('d/m/Y') . ' | Lokasi: ' . ($event->location ?? '-'));
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);

        $headers = ['A5' => 'No', 'B5' => 'Nama Peserta', 'C5' => 'Tipe', 'D5' => 'Kelas / Jabatan', 'E5' => 'Status Kehadiran', 'F5' => 'Waktu Hadir', 'G5' => 'Catatan'];
        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }
        $sheet->getStyle('A5:G5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 6;
        $no = 1;
        foreach ($event->attendances as $att) {
            $name = $att->participant_type === 'teacher' ? ($att->user?->name ?? 'Guru') : ($att->student?->user?->name ?? 'Santri');
            $detail = $att->participant_type === 'teacher' ? ($att->user?->jabatan ?? 'Pegawai') : ($att->student?->class?->name ?? '-');

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $name);
            $sheet->setCellValue('C' . $row, $att->participant_type === 'teacher' ? 'Guru / Pegawai' : 'Santri');
            $sheet->setCellValue('D' . $row, $detail);
            $sheet->setCellValue('E' . $row, ucfirst($att->status));
            $sheet->setCellValue('F' . $row, $att->check_in_time ?? '-');
            $sheet->setCellValue('G' . $row, $att->notes ?? '-');

            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:G{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }
            $row++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Absensi_Kegiatan_' . \Illuminate\Support\Str::slug($event->title) . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
