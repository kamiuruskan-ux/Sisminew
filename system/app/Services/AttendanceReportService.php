<?php

namespace App\Services;

use App\Models\TeacherAttendance;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AttendanceReportService
{
    /**
     * Get monthly recap data for all teachers
     */
    public function getMonthlyRecap(int $month, int $year): array
    {
        $teachers = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah']);
        })->orderBy('name')->get();

        $monthlyAttendances = TeacherAttendance::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->groupBy('user_id');

        return $teachers->map(function ($teacher) use ($monthlyAttendances) {
            $userAttendances = $monthlyAttendances->get($teacher->id, collect());
            $totalPresent = $userAttendances->where('status', 'present')->count();
            $totalLate = $userAttendances->where('status', 'late')->count();
            $totalSick = $userAttendances->where('status', 'sick')->count();
            $totalPermission = $userAttendances->where('status', 'permission')->count();
            $totalAbsent = $userAttendances->where('status', 'absent')->count();
            $totalRecorded = $userAttendances->count();

            // Multi-session attendance statistics
            $morningCount = $userAttendances->filter(fn($a) => !empty($a->check_in))->count();
            $middayCount = $userAttendances->filter(fn($a) => !empty($a->midday_at) || (!empty($a->notes) && str_contains($a->notes, 'Hadir Sesi Siang')))->count();
            $checkoutCount = $userAttendances->filter(fn($a) => !empty($a->check_out))->count();

            $percentage = $totalRecorded > 0 ? round((($totalPresent + $totalLate) / $totalRecorded) * 100, 1) : 0;

            return [
                'teacher' => $teacher,
                'present' => $totalPresent,
                'late' => $totalLate,
                'sick' => $totalSick,
                'permission' => $totalPermission,
                'absent' => $totalAbsent,
                'total' => $totalRecorded,
                'morning_count' => $morningCount,
                'midday_count' => $middayCount,
                'checkout_count' => $checkoutCount,
                'percentage' => $percentage,
            ];
        })->toArray();
    }

    /**
     * Generate multi-session Excel spreadsheet export
     */
    public function generateExcelExport(string $date): Spreadsheet
    {
        $attendances = TeacherAttendance::with(['user', 'recorder'])
            ->where('date', $date)
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Presensi Guru ' . $date);

        // Comprehensive Headers as per Item 9 requirements
        $headers = [
            'A1' => 'No',
            'B1' => 'Nama Guru / Pegawai',
            'C1' => 'NIP',
            'D1' => 'Tanggal',
            'E1' => 'Sesi Pagi (Masuk)',
            'F1' => 'Sesi Siang (Dzuhur)',
            'G1' => 'Sesi Pulang',
            'H1' => 'Nama Sesi',
            'I1' => 'Metode Presensi',
            'J1' => 'Status Kehadiran',
            'K1' => 'Lokasi Kerja',
            'L1' => 'Perangkat Digunakan',
            'M1' => 'Status Verifikasi',
            'N1' => 'Catatan',
            'O1' => 'Dicatat Oleh',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        // Header Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'] // Sleek Dark Slate
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
        ];
        $sheet->getStyle('A1:O1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $row = 2;
        $no = 1;
        foreach ($attendances as $att) {
            $middayDisplay = $att->midday_at ?? '-';
            if ($middayDisplay === '-' && !empty($att->notes) && str_contains($att->notes, 'Hadir Sesi Siang')) {
                if (preg_match('/\[Hadir Sesi Siang @ ([0-9:]+)\]/', $att->notes, $matches)) {
                    $middayDisplay = $matches[1];
                } else {
                    $middayDisplay = 'Hadir';
                }
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $att->user->name ?? '-');
            $sheet->setCellValue('C' . $row, $att->user->nip ?? '-');
            $sheet->setCellValue('D' . $row, $att->date ? $att->date->format('Y-m-d') : $date);
            $sheet->setCellValue('E' . $row, $att->check_in ?? '-');
            $sheet->setCellValue('F' . $row, $middayDisplay);
            $sheet->setCellValue('G' . $row, $att->check_out ?? '-');
            $sheet->setCellValue('H' . $row, $att->session_name ?? 'Sesi Harian');
            $sheet->setCellValue('I' . $row, $att->method_label ?? 'Manual');
            $sheet->setCellValue('J' . $row, $att->status_label);
            $sheet->setCellValue('K' . $row, $att->location_label);
            $sheet->setCellValue('L' . $row, $att->device_info ?? 'Standard');
            $sheet->setCellValue('M' . $row, ucfirst($att->verification_status ?? 'verified'));
            $sheet->setCellValue('N' . $row, $att->notes ?? '');
            $sheet->setCellValue('O' . $row, $att->recorder->name ?? 'Sistem / Mandiri');

            // Alternating row background
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:O{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }

            $row++;
        }

        // Auto size columns
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
