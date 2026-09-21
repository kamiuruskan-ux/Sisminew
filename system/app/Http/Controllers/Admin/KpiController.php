<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use App\Models\KpiEvaluation;
use App\Models\KpiEvaluationItem;
use App\Services\KpiCalculationEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KpiController extends Controller
{
    protected KpiCalculationEngineService $kpiEngine;

    public function __construct(KpiCalculationEngineService $kpiEngine)
    {
        $this->kpiEngine = $kpiEngine;
    }

    /**
     * Dashboard KPI Guru & Pegawai
     */
    public function index(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $search = trim($request->input('search', ''));
        $roleFilter = $request->input('role', '');

        // Fetch teachers and staff members
        $employeeRoles = ['guru', 'teacher', 'guru-quran', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah', 'wakasek-kesiswaan', 'wakasek-kurikulum', 'wakasek-kehumasan', 'bendahara', 'guru-bk'];
        
        $query = User::where(function ($q) use ($employeeRoles) {
            $q->whereHas('roles', function ($rq) use ($employeeRoles) {
                $rq->whereIn('slug', $employeeRoles);
            })->orWhere(function ($rq) {
                $rq->whereNotNull('jabatan')->where('jabatan', '!=', '');
            });
        });

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($roleFilter)) {
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('slug', $roleFilter);
            });
        }

        $teachers = $query->orderBy('name')->get();

        // Calculate KPI for each teacher
        $kpiList = [];
        $totalScores = 0;
        $predicateCounts = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
        $gatePassedCount = 0;
        $componentSums = [
            'comp_1' => 0,
            'comp_2' => 0,
            'comp_3' => 0,
            'comp_4' => 0,
            'comp_5' => 0,
        ];

        foreach ($teachers as $teacher) {
            $kpi = $this->kpiEngine->calculateEmployeeKpi($teacher->id, $year, $month);
            $kpi['user'] = $teacher;
            $kpiList[] = $kpi;

            $totalScores += $kpi['final_score'];
            $predicateCounts[$kpi['predicate']] = ($predicateCounts[$kpi['predicate']] ?? 0) + 1;
            if ($kpi['gate_passed']) {
                $gatePassedCount++;
            }

            $componentSums['comp_1'] += $kpi['score_comp_1'];
            $componentSums['comp_2'] += $kpi['score_comp_2'];
            $componentSums['comp_3'] += $kpi['score_comp_3'];
            $componentSums['comp_4'] += $kpi['score_comp_4'];
            $componentSums['comp_5'] += $kpi['score_comp_5'];
        }

        // Leaderboard: sort by final_score descending
        $leaderboard = $kpiList;
        usort($leaderboard, fn($a, $b) => $b['final_score'] <=> $a['final_score']);
        $topPerformers = array_slice($leaderboard, 0, 5);

        $teacherCount = count($kpiList);
        $averageScore = $teacherCount > 0 ? round($totalScores / $teacherCount, 2) : 0;
        $gatePassRate = $teacherCount > 0 ? round(($gatePassedCount / $teacherCount) * 100, 1) : 100;

        $componentAverages = [
            'comp_1' => $teacherCount > 0 ? round($componentSums['comp_1'] / $teacherCount, 1) : 0,
            'comp_2' => $teacherCount > 0 ? round($componentSums['comp_2'] / $teacherCount, 1) : 0,
            'comp_3' => $teacherCount > 0 ? round($componentSums['comp_3'] / $teacherCount, 1) : 0,
            'comp_4' => $teacherCount > 0 ? round($componentSums['comp_4'] / $teacherCount, 1) : 0,
            'comp_5' => $teacherCount > 0 ? round($componentSums['comp_5'] / $teacherCount, 1) : 0,
        ];

        $settings = $this->kpiEngine->getSettings();
        $definitions = $this->kpiEngine->getIndicatorDefinitions();

        return view('admin.kpi.index', compact(
            'teachers',
            'kpiList',
            'topPerformers',
            'averageScore',
            'predicateCounts',
            'gatePassRate',
            'componentAverages',
            'year',
            'month',
            'search',
            'roleFilter',
            'settings',
            'definitions'
        ));
    }

    /**
     * Get JSON evaluation data for modal
     */
    public function getEvaluationData($userId, Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $user = User::findOrFail($userId);

        $kpi = $this->kpiEngine->calculateEmployeeKpi($userId, $year, $month);
        $definitions = $this->kpiEngine->getIndicatorDefinitions();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nip' => $user->nip ?? '-',
                'jabatan' => $user->jabatan ?? ($user->roles->first()->name ?? 'Guru / Pegawai'),
                'avatar' => $user->avatar_url ?? null,
            ],
            'kpi' => $kpi,
            'definitions' => $definitions,
        ]);
    }

    /**
     * Store or update supervisor evaluation
     */
    public function saveEvaluation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'period_year' => 'required|integer',
            'period_month' => 'required|integer|between:1,12',
            'items' => 'required|array',
            'feedback_appreciation' => 'nullable|string',
            'feedback_improvement' => 'nullable|string',
        ]);

        $userId = (int) $request->input('user_id');
        $year = (int) $request->input('period_year');
        $month = (int) $request->input('period_month');

        // First calculate base auto values
        $calculated = $this->kpiEngine->calculateEmployeeKpi($userId, $year, $month);
        $submittedItems = $request->input('items', []);

        DB::beginTransaction();
        try {
            // Find or create KpiEvaluation
            $evaluation = KpiEvaluation::updateOrCreate(
                [
                    'user_id' => $userId,
                    'period_year' => $year,
                    'period_month' => $month,
                    'period_type' => 'month',
                ],
                [
                    'evaluator_id' => auth()->id(),
                    'feedback_appreciation' => $request->input('feedback_appreciation'),
                    'feedback_improvement' => $request->input('feedback_improvement'),
                    'status' => 'published',
                    'published_at' => now(),
                ]
            );

            // Save indicator items
            $definitions = $this->kpiEngine->getIndicatorDefinitions();
            foreach ($definitions as $compKey => $compDef) {
                foreach ($compDef['indicators'] as $indCode => $indDef) {
                    $score = isset($submittedItems[$indCode]['score']) 
                        ? (float) $submittedItems[$indCode]['score']
                        : (float) ($calculated['items'][$indCode]['score'] ?? 80);
                    
                    $score = max(0, min(100, $score));
                    $notes = $submittedItems[$indCode]['notes'] ?? ($calculated['items'][$indCode]['notes'] ?? null);
                    $sourceType = $indDef['type'];
                    $sourceDetail = $calculated['items'][$indCode]['source_detail'] ?? null;

                    KpiEvaluationItem::updateOrCreate(
                        [
                            'kpi_evaluation_id' => $evaluation->id,
                            'indicator_code' => $indCode,
                        ],
                        [
                            'score' => $score,
                            'source_type' => $sourceType,
                            'source_detail' => $sourceDetail,
                            'notes' => $notes,
                        ]
                    );
                }
            }

            // Recalculate component averages and final score
            $updatedKpi = $this->kpiEngine->calculateEmployeeKpi($userId, $year, $month);

            $evaluation->update([
                'score_comp_1' => $updatedKpi['score_comp_1'],
                'score_comp_2' => $updatedKpi['score_comp_2'],
                'score_comp_3' => $updatedKpi['score_comp_3'],
                'score_comp_4' => $updatedKpi['score_comp_4'],
                'score_comp_5' => $updatedKpi['score_comp_5'],
                'final_score' => $updatedKpi['final_score'],
                'predicate' => $updatedKpi['predicate'],
                'attendance_percentage' => $updatedKpi['attendance_percentage'],
                'gate_passed' => $updatedKpi['gate_passed'],
                'is_capped' => $updatedKpi['is_capped'],
            ]);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Evaluasi Kinerja Guru berhasil disimpan & diperbarui!',
                    'evaluation' => $evaluation,
                    'kpi' => $updatedKpi,
                ]);
            }

            return redirect()->back()->with('success', 'Evaluasi Kinerja Guru berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan evaluasi: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menyimpan evaluasi: ' . $e->getMessage());
        }
    }

    /**
     * Update KPI Settings (Weights, Gate, Penalty)
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'weight_comp_1' => 'required|numeric|min:0|max:100',
            'weight_comp_2' => 'required|numeric|min:0|max:100',
            'weight_comp_3' => 'required|numeric|min:0|max:100',
            'weight_comp_4' => 'required|numeric|min:0|max:100',
            'weight_comp_5' => 'required|numeric|min:0|max:100',
            'gate_threshold' => 'required|numeric|min:50|max:100',
            'late_penalty_rate' => 'required|numeric|min:0|max:5',
        ]);

        Setting::set('kpi_weight_comp_1', $request->input('weight_comp_1'));
        Setting::set('kpi_weight_comp_2', $request->input('weight_comp_2'));
        Setting::set('kpi_weight_comp_3', $request->input('weight_comp_3'));
        Setting::set('kpi_weight_comp_4', $request->input('weight_comp_4'));
        Setting::set('kpi_weight_comp_5', $request->input('weight_comp_5'));
        Setting::set('kpi_gate_threshold', $request->input('gate_threshold'));
        Setting::set('kpi_late_penalty_rate', $request->input('late_penalty_rate'));

        return redirect()->route('admin.kpi.index')->with('success', 'Konfigurasi Bobot KPI & Gate Kehadiran berhasil diperbarui!');
    }

    /**
     * Lembar Rapor Kinerja Guru Detail (Interactive View & Radar Chart)
     */
    public function showRaport($userId, Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $user = User::with('roles')->findOrFail($userId);

        $kpi = $this->kpiEngine->calculateEmployeeKpi($userId, $year, $month);
        $definitions = $this->kpiEngine->getIndicatorDefinitions();
        $settings = $this->kpiEngine->getSettings();

        return view('admin.kpi.raport', compact('user', 'kpi', 'definitions', 'settings', 'year', 'month'));
    }

    /**
     * Cetak Lembar Rapor KPI Resmi (A4 Print ber-Kop Sekolah)
     */
    public function printRaport($userId, Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $user = User::with('roles')->findOrFail($userId);

        $kpi = $this->kpiEngine->calculateEmployeeKpi($userId, $year, $month);
        $definitions = $this->kpiEngine->getIndicatorDefinitions();
        $settings = $this->kpiEngine->getSettings();

        // Retrieve Principal (Kepala Sekolah) for signature
        $principalRole = ['kepala-sekolah', 'super-admin'];
        $principal = User::whereHas('roles', function ($q) use ($principalRole) {
            $q->whereIn('slug', $principalRole);
        })->first();

        return view('admin.kpi.print', compact('user', 'kpi', 'definitions', 'settings', 'year', 'month', 'principal'));
    }

    /**
     * Ekspor Rekap KPI Seluruh Guru ke Excel (.xlsx)
     */
    public function exportExcel(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));

        $employeeRoles = ['guru', 'teacher', 'guru-quran', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah', 'wakasek-kesiswaan', 'wakasek-kurikulum', 'wakasek-kehumasan', 'bendahara', 'guru-bk'];
        
        $teachers = User::where(function ($q) use ($employeeRoles) {
            $q->whereHas('roles', function ($rq) use ($employeeRoles) {
                $rq->whereIn('slug', $employeeRoles);
            })->orWhere(function ($rq) {
                $rq->whereNotNull('jabatan')->where('jabatan', '!=', '');
            });
        })->orderBy('name')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("KPI_{$month}_{$year}");

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthName = $months[$month] ?? "Bulan {$month}";

        // Title Header
        $schoolName = strtoupper(Setting::get('school_name', 'SEKOLAH ISLAM TERPADU AL-FAHMI'));
        $sheet->setCellValue('A1', "REKAPITULASI PENILAIAN KINERJA GURU & PEGAWAI (KPI)");
        $sheet->setCellValue('A2', "{$schoolName} - PERIODE: {$monthName} {$year}");

        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(13);
        $sheet->mergeCells('A1:M1');
        $sheet->mergeCells('A2:M2');

        // Column Headers
        $headers = [
            'A4' => 'NO',
            'B4' => 'NIP / ID',
            'C4' => 'NAMA LENGKAP',
            'D4' => 'JABATAN / UNIT',
            'E4' => 'KEHADIRAN (%)',
            'F4' => 'GATE KEHADIRAN',
            'G4' => 'KOMP 1: DISIPLIN',
            'H4' => 'KOMP 2: PEDAGOGIK',
            'I4' => 'KOMP 3: PROFESIONAL',
            'J4' => 'KOMP 4: TARBIYAH',
            'K4' => 'KOMP 5: SOSIAL',
            'L4' => 'SKOR AKHIR',
            'M4' => 'PREDIKAT',
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col, $title);
        }

        // Header style
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']], // Slate-800
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '64748B']]],
        ];
        $sheet->getStyle('A4:M4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(28);

        $row = 5;
        $no = 1;

        foreach ($teachers as $teacher) {
            $kpi = $this->kpiEngine->calculateEmployeeKpi($teacher->id, $year, $month);

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $teacher->nip ?? '-');
            $sheet->setCellValue("C{$row}", $teacher->name);
            $sheet->setCellValue("D{$row}", $teacher->jabatan ?? ($teacher->roles->first()->name ?? 'Guru'));
            $sheet->setCellValue("E{$row}", $kpi['attendance_percentage'] . '%');
            $sheet->setCellValue("F{$row}", $kpi['gate_passed'] ? 'LOLOS (>=85%)' : 'DIBAWAH GATE (<85%)');
            $sheet->setCellValue("G{$row}", $kpi['score_comp_1']);
            $sheet->setCellValue("H{$row}", $kpi['score_comp_2']);
            $sheet->setCellValue("I{$row}", $kpi['score_comp_3']);
            $sheet->setCellValue("J{$row}", $kpi['score_comp_4']);
            $sheet->setCellValue("K{$row}", $kpi['score_comp_5']);
            $sheet->setCellValue("L{$row}", $kpi['final_score']);
            $sheet->setCellValue("M{$row}", $kpi['predicate'] . " ({$kpi['predicate_label']})");

            // Row style
            $sheet->getStyle("A{$row}:M{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}:M{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("L{$row}:M{$row}")->getFont()->setBold(true);

            // Conditional fill for Predicate
            if ($kpi['predicate'] === 'A') {
                $sheet->getStyle("M{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D1FAE5'); // Emerald
            } elseif ($kpi['predicate'] === 'B') {
                $sheet->getStyle("M{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0E7FF'); // Indigo
            } elseif ($kpi['predicate'] === 'C') {
                $sheet->getStyle("M{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF3C7'); // Amber
            } else {
                $sheet->getStyle("M{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2'); // Rose
            }

            $row++;
        }

        // Auto-fit columns
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = "Rekap_KPI_Guru_{$monthName}_{$year}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
