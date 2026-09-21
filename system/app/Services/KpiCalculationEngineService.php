<?php

namespace App\Services;

use App\Models\User;
use App\Models\TeacherAttendance;
use App\Models\BriefingSession;
use App\Models\BriefingAttendance;
use App\Models\EmployeePermit;
use App\Models\TeachingAgenda;
use App\Models\EmployeeStudySession;
use App\Models\EmployeeStudyAttendance;
use App\Models\EmployeeMutabaah;
use App\Models\HalaqahRecord;
use App\Models\SchoolFeedback;
use App\Models\KpiEvaluation;
use App\Models\KpiEvaluationItem;
use App\Models\Setting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class KpiCalculationEngineService
{
    /**
     * Get KPI Configuration weights and thresholds
     */
    public function getSettings(): array
    {
        return [
            'weight_comp_1' => (float) Setting::get('kpi_weight_comp_1', 20),
            'weight_comp_2' => (float) Setting::get('kpi_weight_comp_2', 35),
            'weight_comp_3' => (float) Setting::get('kpi_weight_comp_3', 15),
            'weight_comp_4' => (float) Setting::get('kpi_weight_comp_4', 15),
            'weight_comp_5' => (float) Setting::get('kpi_weight_comp_5', 15),
            'gate_threshold' => (float) Setting::get('kpi_gate_threshold', 85), // Minimum 85% attendance
            'late_penalty_rate' => (float) Setting::get('kpi_late_penalty_rate', 0.5), // 0.5% per minute
        ];
    }

    /**
     * List of all 5 Pillars and their Indicator specifications
     */
    public function getIndicatorDefinitions(): array
    {
        return [
            'comp_1' => [
                'name' => 'Disiplin & Kehadiran Kerja',
                'weight_key' => 'weight_comp_1',
                'description' => 'Mengukur kepatuhan jam kerja, presensi harian, dan partisipasi briefing.',
                'indicators' => [
                    '1.1' => [
                        'code' => '1.1',
                        'name' => 'Presensi Masuk Pagi',
                        'type' => 'auto',
                        'desc' => 'Tervalidasi GPS & Waktu Target WITA (Penalti 0.5% per menit keterlambatan).',
                    ],
                    '1.2' => [
                        'code' => '1.2',
                        'name' => 'Presensi Siang (Dhuhur / Istirahat)',
                        'type' => 'auto',
                        'desc' => 'Validasi keberadaan saat jam istirahat untuk menjaga konsistensi bertugas.',
                    ],
                    '1.3' => [
                        'code' => '1.3',
                        'name' => 'Presensi Pulang',
                        'type' => 'auto',
                        'desc' => 'Validasi kepulangan tepat waktu sesuai ketentuan jam kerja.',
                    ],
                    '1.4' => [
                        'code' => '1.4',
                        'name' => 'Kehadiran Briefing Pagi / Taujih',
                        'type' => 'auto',
                        'desc' => 'Partisipasi briefing pagi rutin sekolah (Target kehadiran min 80%).',
                    ],
                ]
            ],
            'comp_2' => [
                'name' => 'Kompetensi Pedagogik / Pengajaran',
                'weight_key' => 'weight_comp_2',
                'description' => 'Mengukur kualitas perencanaan modul ajar, pelaksanaan KBM, dan ketertiban jurnal.',
                'indicators' => [
                    '2.1.1' => [
                        'code' => '2.1.1',
                        'name' => 'Supervisi Modul Ajar ADLX Terpadu',
                        'type' => 'manual',
                        'desc' => 'Penyusunan RPP/Modul ajar mendalam sesuai alur ADLX Terpadu.',
                    ],
                    '2.1.2' => [
                        'code' => '2.1.2',
                        'name' => 'Supervisi Modul Projek P3 / P5',
                        'type' => 'manual',
                        'desc' => 'Perencanaan dan pelaksanaan Projek Penguatan Profil Pelajar.',
                    ],
                    '2.1.3' => [
                        'code' => '2.1.3',
                        'name' => 'Bahan Ajar Digital & Media LMS',
                        'type' => 'manual',
                        'desc' => 'Pemanfaatan media pembelajaran interaktif, video, atau modul LMS.',
                    ],
                    '2.2' => [
                        'code' => '2.2',
                        'name' => 'Supervisi Fasilitasi Proses KBM',
                        'type' => 'manual',
                        'desc' => 'Hasil observasi langsung kegiatan belajar mengajar di kelas oleh KS.',
                    ],
                    '2.3' => [
                        'code' => '2.3',
                        'name' => 'Supervisi & Manajemen Asesmen KBM',
                        'type' => 'manual',
                        'desc' => 'Pelaksanaan asesmen diagnostik, formatif, sumatif AKM & UAS.',
                    ],
                    '2.4' => [
                        'code' => '2.4',
                        'name' => 'Pengelolaan Lingkungan Belajar',
                        'type' => 'manual',
                        'desc' => 'Kerapian, ketertiban, kebersihan, dan penciptaan suasana kondusif di kelas.',
                    ],
                    '2.5' => [
                        'code' => '2.5',
                        'name' => 'Ketertiban Jurnal Mengajar Harian',
                        'type' => 'auto',
                        'desc' => 'Otomatis dihitung dari ketertiban input jurnal mengajar harian & presensi siswa.',
                    ],
                ]
            ],
            'comp_3' => [
                'name' => 'Kompetensi Profesional & Pengembangan Diri',
                'weight_key' => 'weight_comp_3',
                'description' => 'Mengukur wawasan keilmuan, karya ilmiah, dan peningkatan kompetensi diri.',
                'indicators' => [
                    '3.1' => [
                        'code' => '3.1',
                        'name' => 'Adaptasi Teknologi & Platform LMS',
                        'type' => 'manual',
                        'desc' => 'Pemanfaatan platform teknologi sekolah secara efektif dan produktif.',
                    ],
                    '3.2' => [
                        'code' => '3.2',
                        'name' => 'Penguasaan Konsep ADLX AEC',
                        'type' => 'manual',
                        'desc' => 'Penerapan konsep Active Deep Learner eXperience dalam KBM.',
                    ],
                    '3.3' => [
                        'code' => '3.3',
                        'name' => 'Pelatihan & Implementasi Asesmen AKM',
                        'type' => 'manual',
                        'desc' => 'Pengembangan butir soal asesmen literasi dan numerasi berkualitas.',
                    ],
                    '3.4' => [
                        'code' => '3.4',
                        'name' => 'PTK / Karya Tulis Reflektif',
                        'type' => 'manual',
                        'desc' => 'Penelitian Tindakan Kelas atau jurnal refleksi perbaikan pembelajaran.',
                    ],
                    '3.5' => [
                        'code' => '3.5',
                        'name' => 'Partisipasi Pengembangan Diri & Prestasi',
                        'type' => 'manual',
                        'desc' => 'Sertifikat pelatihan, workshop, narasumber, atau raihan prestasi sekolah.',
                    ],
                ]
            ],
            'comp_4' => [
                'name' => 'Kompetensi Kepribadian & Nilai Tarbiyah',
                'weight_key' => 'weight_comp_4',
                'description' => 'Mengukur integritas ibadah, keteladanan akhlak, dan partisipasi agenda pembinaan.',
                'indicators' => [
                    '4.1' => [
                        'code' => '4.1',
                        'name' => 'Kedisiplinan Agenda Sekolah & Kajian Pekanan',
                        'type' => 'auto',
                        'desc' => 'Otomatis dari rekap kehadiran Kajian Pekanan Pegawai & agenda yayasan.',
                    ],
                    '4.2.1' => [
                        'code' => '4.2.1',
                        'name' => 'Mutabaah Ibadah Harian Pegawai',
                        'type' => 'auto',
                        'desc' => 'Otomatis dari log mutabaah: Shalat Jamaah, Rawatib, Dhuha, Tahajjud, Tilawah, Dzikir.',
                    ],
                    '4.2.2' => [
                        'code' => '4.2.2',
                        'name' => 'Kemampuan Baca & Tahsin Al-Qur\'an',
                        'type' => 'hybrid',
                        'desc' => 'Kualitas bacaan tartil dan keterlibatan dalam halaqah Al-Qur\'an guru.',
                    ],
                    '4.2.3' => [
                        'code' => '4.2.3',
                        'name' => 'Survei Karakter & Keteladanan Guru',
                        'type' => 'hybrid',
                        'desc' => 'Indeks keteladanan akhlak dari sudut pandang siswa, pimpinan, dan wali murid.',
                    ],
                ]
            ],
            'comp_5' => [
                'name' => 'Kompetensi Sosial & Kolaborasi',
                'weight_key' => 'weight_comp_5',
                'description' => 'Mengukur interaksi sosial, kerjasama tim, dan kontribusi pada kepanitiaan institusi.',
                'indicators' => [
                    '5.1' => [
                        'code' => '5.1',
                        'name' => 'Kemampuan Adaptasi di Sekolah',
                        'type' => 'manual',
                        'desc' => 'Kemampuan beradaptasi dengan budaya sekolah dan dinamika tugas kerja.',
                    ],
                    '5.2' => [
                        'code' => '5.2',
                        'name' => 'Komunikasi & Relasi Positif',
                        'type' => 'manual',
                        'desc' => 'Keramahan, keterbukaan, dan etika komunikasi dengan rekan kerja & orang tua.',
                    ],
                    '5.3' => [
                        'code' => '5.3',
                        'name' => 'Kerjasama Tim & Kepanitiaan Sekolah',
                        'type' => 'manual',
                        'desc' => 'Kontribusi aktif dalam kegiatan, kepanitiaan event, dan tugas tim sekolah.',
                    ],
                ]
            ]
        ];
    }

    /**
     * Compute comprehensive KPI for an employee in a given period
     */
    public function calculateEmployeeKpi(int $userId, int $year, ?int $month = null, string $periodType = 'month'): array
    {
        $settings = $this->getSettings();
        $definitions = $this->getIndicatorDefinitions();

        // 1. Determine date range
        if ($periodType === 'month' && !empty($month)) {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        } else {
            $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();
        }

        // 2. Calculate Effective Working Days (exclude Sundays)
        $totalDays = 0;
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            if ($date->dayOfWeek !== Carbon::SUNDAY) {
                $totalDays++;
            }
        }
        $totalDays = max(1, $totalDays);

        // 3. Excused Days (Approved Sick / Official Duty Leaves)
        $excusedDays = 0;
        try {
            $permits = EmployeePermit::where('user_id', $userId)
                ->where('status', 'approved')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                })->get();

            foreach ($permits as $permit) {
                $pStart = Carbon::parse($permit->start_date)->max($startDate);
                $pEnd = Carbon::parse($permit->end_date)->min($endDate);
                $pPeriod = CarbonPeriod::create($pStart, $pEnd);
                foreach ($pPeriod as $pDate) {
                    if ($pDate->dayOfWeek !== Carbon::SUNDAY) {
                        $excusedDays++;
                    }
                }
            }
        } catch (\Throwable $e) {
            $excusedDays = 0;
        }

        $effectiveWorkdays = max(1, $totalDays - $excusedDays);

        // 4. Retrieve Teacher Attendance records
        $attendances = TeacherAttendance::where('user_id', $userId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $presentCount = 0;
        $morningScoresSum = 0;
        $middayCount = 0;
        $checkoutCount = 0;
        $totalDelayMinutes = 0;

        foreach ($attendances as $att) {
            if (!empty($att->check_in) || in_array($att->status, ['present', 'late', 'very_late'])) {
                $presentCount++;
                $delay = max(0, (int)$att->delay_minutes);
                $totalDelayMinutes += $delay;
                // Penalty: 0.5% per minute late
                $penalty = $delay * $settings['late_penalty_rate'];
                $morningScoresSum += max(0, 100 - $penalty);
            }

            if (!empty($att->midday_at)) {
                $middayCount++;
            }

            if (!empty($att->check_out)) {
                $checkoutCount++;
            }
        }

        // Indicator 1.1: Presensi Masuk Pagi
        $scoreInd11 = round(min(100, $morningScoresSum / $effectiveWorkdays), 2);
        $detailInd11 = "Hadir {$presentCount}/{$effectiveWorkdays} hari aktif, akumulasi terlambat {$totalDelayMinutes} menit";

        // Indicator 1.2: Presensi Siang
        $scoreInd12 = round(min(100, ($middayCount / $effectiveWorkdays) * 100), 2);
        $detailInd12 = "Presensi siang tercatat {$middayCount} dari {$effectiveWorkdays} hari kerja";

        // Indicator 1.3: Presensi Pulang
        $scoreInd13 = round(min(100, ($checkoutCount / $effectiveWorkdays) * 100), 2);
        $detailInd13 = "Presensi kepulangan tepat waktu {$checkoutCount} dari {$effectiveWorkdays} hari kerja";

        // Indicator 1.4: Kehadiran Briefing Pagi
        $totalBriefings = 0;
        $attendedBriefings = 0;
        try {
            $totalBriefings = BriefingSession::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->count();
            if ($totalBriefings > 0) {
                $attendedBriefings = BriefingAttendance::where('user_id', $userId)
                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->count();
                $scoreInd14 = round(min(100, ($attendedBriefings / $totalBriefings) * 100), 2);
            } else {
                $scoreInd14 = 100;
            }
        } catch (\Throwable $e) {
            $scoreInd14 = 85;
        }
        $detailInd14 = $totalBriefings > 0 ? "Hadir {$attendedBriefings} dari {$totalBriefings} sesi briefing pagi" : "Tidak ada jadwal briefing wajib";

        // Attendance Percentage & Gate Prerequisite
        $attendancePercentage = round(min(100, (($presentCount + $excusedDays) / $totalDays) * 100), 2);
        $gatePassed = ($attendancePercentage >= $settings['gate_threshold']);

        // Component 1 Score
        $scoreComp1 = round(($scoreInd11 + $scoreInd12 + $scoreInd13 + $scoreInd14) / 4, 2);

        // Indicator 2.5: Jurnal Mengajar Harian
        $teachingAgendaCount = 0;
        try {
            $teachingAgendaCount = TeachingAgenda::where('teacher_id', $userId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->count();
        } catch (\Throwable $e) {
            $teachingAgendaCount = 0;
        }
        $expectedAgendas = max(4, round($effectiveWorkdays * 0.7)); // Est. 70% of days have teaching load
        $scoreInd25 = $teachingAgendaCount > 0 
            ? round(min(100, ($teachingAgendaCount / $expectedAgendas) * 100), 2)
            : 95.0;
        $detailInd25 = $teachingAgendaCount > 0 
            ? "Terekam {$teachingAgendaCount} jurnal pembelajaran aktif bulan ini"
            : "Baseline jurnal pembelajaran standar (95 pts)";

        // Indicator 4.1: Kajian Pekanan Pegawai (Laporan Individu)
        $attendedStudies = 0;
        try {
            // Count individual studies reported by the employee
            $userKajianCount = EmployeeStudySession::where('created_by', $userId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->count();
            
            // Also check attendance table if exists
            $attendanceCount = EmployeeStudyAttendance::where('user_id', $userId)
                ->where('status', 'hadir')
                ->whereHas('session', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                })->count();

            $attendedStudies = max($userKajianCount, $attendanceCount);
            if ($attendedStudies > 0) {
                $scoreInd41 = round(min(100, max(95, ($attendedStudies / 4) * 100)), 2);
                $detailInd41 = "Telah melaporkan {$attendedStudies} kegiatan kajian pekanan mandiri";
            } else {
                $scoreInd41 = 95.0;
                $detailInd41 = "Baseline keikutsertaan kajian pekanan standar (95 pts)";
            }
        } catch (\Throwable $e) {
            $scoreInd41 = 95.0;
            $detailInd41 = "Baseline keikutsertaan kajian pekanan standar (95 pts)";
        }

        // Indicator 4.2.1: Mutabaah Ibadah Harian Pegawai
        $mutabaahDaysCount = 0;
        $avgMutabaahScore = 95.0;
        try {
            $mutabaahRecords = EmployeeMutabaah::where('user_id', $userId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();
            $mutabaahDaysCount = $mutabaahRecords->count();
            if ($mutabaahDaysCount > 0) {
                $avgMutabaahScore = round($mutabaahRecords->avg('daily_score'), 2);
            } else {
                $avgMutabaahScore = 95.0; // Default baseline 95
            }
        } catch (\Throwable $e) {
            $avgMutabaahScore = 95.0;
        }
        $scoreInd421 = $avgMutabaahScore;
        $detailInd421 = $mutabaahDaysCount > 0 ? "Rerata amalan harian terisi {$mutabaahDaysCount} hari: {$avgMutabaahScore} poin" : "Baseline amalan yaumiyah standar (95 pts)";

        // Load existing saved evaluation if exists
        $existingEval = KpiEvaluation::with('items')->where([
            'user_id' => $userId,
            'period_year' => $year,
            'period_month' => $month,
            'period_type' => $periodType,
        ])->first();

        $savedItems = [];
        if ($existingEval) {
            foreach ($existingEval->items as $item) {
                $savedItems[$item->indicator_code] = $item;
            }
        }

        // Fill indicator items with auto-computed or saved manual values
        $computedItems = [];

        // Comp 1
        $computedItems['1.1'] = [
            'score' => $scoreInd11,
            'source_type' => 'auto',
            'source_detail' => $detailInd11,
            'notes' => $savedItems['1.1']->notes ?? 'Presensi masuk via scan/GPS otomatis',
        ];
        $computedItems['1.2'] = [
            'score' => $scoreInd12,
            'source_type' => 'auto',
            'source_detail' => $detailInd12,
            'notes' => $savedItems['1.2']->notes ?? 'Presensi siang jam istirahat',
        ];
        $computedItems['1.3'] = [
            'score' => $scoreInd13,
            'source_type' => 'auto',
            'source_detail' => $detailInd13,
            'notes' => $savedItems['1.3']->notes ?? 'Presensi kepulangan tepat waktu',
        ];
        $computedItems['1.4'] = [
            'score' => $scoreInd14,
            'source_type' => 'auto',
            'source_detail' => $detailInd14,
            'notes' => $savedItems['1.4']->notes ?? 'Presensi briefing pagi terpadu',
        ];

        // Comp 2 (Pedagogik) - Default: 95
        $comp2Defaults = ['2.1.1' => 95, '2.1.2' => 95, '2.1.3' => 95, '2.2' => 95, '2.3' => 95, '2.4' => 95];
        foreach ($comp2Defaults as $code => $defVal) {
            $computedItems[$code] = [
                'score' => isset($savedItems[$code]) ? (float)$savedItems[$code]->score : $defVal,
                'source_type' => 'manual',
                'source_detail' => 'Supervisi Pengajaran Kepala Sekolah',
                'notes' => $savedItems[$code]->notes ?? '',
            ];
        }
        $computedItems['2.5'] = [
            'score' => $scoreInd25,
            'source_type' => 'auto',
            'source_detail' => $detailInd25,
            'notes' => $savedItems['2.5']->notes ?? 'Ketertiban entri modul agenda mengajar',
        ];

        // Comp 3 (Profesional) - Default: 95
        $comp3Defaults = ['3.1' => 95, '3.2' => 95, '3.3' => 95, '3.4' => 95, '3.5' => 95];
        foreach ($comp3Defaults as $code => $defVal) {
            $computedItems[$code] = [
                'score' => isset($savedItems[$code]) ? (float)$savedItems[$code]->score : $defVal,
                'source_type' => 'manual',
                'source_detail' => 'Observasi & Verifikasi Portofolio',
                'notes' => $savedItems[$code]->notes ?? '',
            ];
        }

        // Comp 4 (Kepribadian & Tarbiyah) - Default: 95
        $computedItems['4.1'] = [
            'score' => $scoreInd41,
            'source_type' => 'auto',
            'source_detail' => $detailInd41,
            'notes' => $savedItems['4.1']->notes ?? 'Laporan kajian pekanan pegawai mandiri',
        ];
        $computedItems['4.2.1'] = [
            'score' => $scoreInd421,
            'source_type' => 'auto',
            'source_detail' => $detailInd421,
            'notes' => $savedItems['4.2.1']->notes ?? 'Jurnal mutabaah amalan yaumiyah',
        ];
        $computedItems['4.2.2'] = [
            'score' => isset($savedItems['4.2.2']) ? (float)$savedItems['4.2.2']->score : 95,
            'source_type' => 'hybrid',
            'source_detail' => 'Penilaian Halaqah Quran & Tahsin Guru',
            'notes' => $savedItems['4.2.2']->notes ?? 'Halaqah Qur\'an guru pekanan',
        ];
        $computedItems['4.2.3'] = [
            'score' => isset($savedItems['4.2.3']) ? (float)$savedItems['4.2.3']->score : 95,
            'source_type' => 'hybrid',
            'source_detail' => 'Survei Karakter Guru dari Siswa/Wali',
            'notes' => $savedItems['4.2.3']->notes ?? 'Keteladanan adab & akhlak terpuji',
        ];

        // Comp 5 (Sosial & Kolaborasi) - Default: 95
        $comp5Defaults = ['5.1' => 95, '5.2' => 95, '5.3' => 95];
        foreach ($comp5Defaults as $code => $defVal) {
            $computedItems[$code] = [
                'score' => isset($savedItems[$code]) ? (float)$savedItems[$code]->score : $defVal,
                'source_type' => 'manual',
                'source_detail' => 'Evaluasi Dinamika Tim & Interaksi Kerja',
                'notes' => $savedItems[$code]->notes ?? '',
            ];
        }

        // Compute Component Averages
        // Comp 1: 1.1, 1.2, 1.3, 1.4
        $comp1Score = $scoreComp1;

        // Comp 2: 2.1.1, 2.1.2, 2.1.3, 2.2, 2.3, 2.4, 2.5
        $comp2Scores = array_map(fn($k) => $computedItems[$k]['score'], ['2.1.1', '2.1.2', '2.1.3', '2.2', '2.3', '2.4', '2.5']);
        $comp2Score = round(array_sum($comp2Scores) / count($comp2Scores), 2);

        // Comp 3: 3.1, 3.2, 3.3, 3.4, 3.5
        $comp3Scores = array_map(fn($k) => $computedItems[$k]['score'], ['3.1', '3.2', '3.3', '3.4', '3.5']);
        $comp3Score = round(array_sum($comp3Scores) / count($comp3Scores), 2);

        // Comp 4: 4.1, 4.2.1, 4.2.2, 4.2.3
        $comp4Scores = array_map(fn($k) => $computedItems[$k]['score'], ['4.1', '4.2.1', '4.2.2', '4.2.3']);
        $comp4Score = round(array_sum($comp4Scores) / count($comp4Scores), 2);

        // Comp 5: 5.1, 5.2, 5.3
        $comp5Scores = array_map(fn($k) => $computedItems[$k]['score'], ['5.1', '5.2', '5.3']);
        $comp5Score = round(array_sum($comp5Scores) / count($comp5Scores), 2);

        // Weighted Final Score
        $w1 = $settings['weight_comp_1'];
        $w2 = $settings['weight_comp_2'];
        $w3 = $settings['weight_comp_3'];
        $w4 = $settings['weight_comp_4'];
        $w5 = $settings['weight_comp_5'];
        $totalWeight = max(1, $w1 + $w2 + $w3 + $w4 + $w5);

        $rawFinalScore = round((
            ($comp1Score * $w1) +
            ($comp2Score * $w2) +
            ($comp3Score * $w3) +
            ($comp4Score * $w4) +
            ($comp5Score * $w5)
        ) / $totalWeight, 2);

        // Determine Predicate
        $predicate = $this->determinePredicate($rawFinalScore);
        $predicateLabel = $this->getPredicateLabel($predicate);
        $isCapped = false;

        // Gate Rule: If attendance < 85%, capped at max 'C'
        if (!$gatePassed) {
            if (in_array($predicate, ['A', 'B'])) {
                $predicate = 'C';
                $predicateLabel = $this->getPredicateLabel('C');
                $isCapped = true;
            }
        }

        return [
            'user_id' => $userId,
            'year' => $year,
            'month' => $month,
            'period_type' => $periodType,
            'total_workdays' => $totalDays,
            'excused_days' => $excusedDays,
            'effective_workdays' => $effectiveWorkdays,
            'attendance_percentage' => $attendancePercentage,
            'gate_passed' => $gatePassed,
            'is_capped' => $isCapped,
            'score_comp_1' => $comp1Score,
            'score_comp_2' => $comp2Score,
            'score_comp_3' => $comp3Score,
            'score_comp_4' => $comp4Score,
            'score_comp_5' => $comp5Score,
            'final_score' => $rawFinalScore,
            'predicate' => $predicate,
            'predicate_label' => $predicateLabel,
            'weights' => [
                'comp_1' => $w1,
                'comp_2' => $w2,
                'comp_3' => $w3,
                'comp_4' => $w4,
                'comp_5' => $w5,
            ],
            'items' => $computedItems,
            'existing_evaluation' => $existingEval,
            'feedback_appreciation' => $existingEval->feedback_appreciation ?? 'Menunjukkan dedikasi mengajar yang tekun, interaksi positif dengan siswa, dan kedisiplinan presensi yang sangat baik.',
            'feedback_improvement' => $existingEval->feedback_improvement ?? 'Pertahankan konsistensi pembuatan media ajar digital serta kelengkapan pengisian modul amalan ibadah harian.',
        ];
    }

    /**
     * Map numerical score to Letter Predicate
     */
    public function determinePredicate(float $score): string
    {
        if ($score >= 91.0) {
            return 'A';
        } elseif ($score >= 76.0) {
            return 'B';
        } elseif ($score >= 61.0) {
            return 'C';
        } else {
            return 'D';
        }
    }

    /**
     * Map Letter Predicate to Human Readable Description
     */
    public function getPredicateLabel(string $predicate): string
    {
        return match ($predicate) {
            'A' => 'Mumtaz / Sangat Baik',
            'B' => 'Jayyid / Baik',
            'C' => 'Maqbul / Cukup',
            'D' => 'Dhaif / Kurang',
            default => 'Cukup',
        };
    }
}
