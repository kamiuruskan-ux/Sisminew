<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class CbtCapacityController extends Controller
{
    /**
     * Display CBT Server Capacity Diagnostics Dashboard.
     */
    public function index()
    {
        // Clear old cached routes if file exists to prevent 405 Method Not Allowed issues
        $routeCachePath = base_path('bootstrap/cache/routes-v7.php');
        if (file_exists($routeCachePath)) {
            @unlink($routeCachePath);
        }

        $serverInfo = $this->getServerDiagnostics();
        $dbInfo = $this->getDatabaseDiagnostics();
        $laravelInfo = $this->getLaravelDiagnostics();
        $capacityAnalysis = $this->calculateCapacity($serverInfo, $dbInfo, $laravelInfo);

        $totalActiveStudents = \App\Models\Student::whereIn('student_status', ['active', 'Aktif'])
            ->orWhereNull('student_status')
            ->count();
        if ($totalActiveStudents === 0) {
            $totalActiveStudents = \App\Models\Student::count();
        }

        $activeExamClassIds = \App\Models\Exam::where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('start_time')->orWhere('start_time', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_time')->orWhere('end_time', '>=', now());
            })
            ->pluck('class_id')
            ->filter()
            ->unique();

        $ongoingExamStudentsCount = 0;
        if ($activeExamClassIds->isNotEmpty()) {
            $ongoingExamStudentsCount = \App\Models\Student::whereIn('class_id', $activeExamClassIds)
                ->where(function ($q) {
                    $q->whereIn('student_status', ['active', 'Aktif'])->orWhereNull('student_status');
                })
                ->count();
        }

        return view('admin.cbt-capacity.index', compact(
            'serverInfo', 
            'dbInfo', 
            'laravelInfo', 
            'capacityAnalysis', 
            'totalActiveStudents',
            'ongoingExamStudentsCount'
        ));
    }

    /**
     * AJAX endpoint to execute Artisan cache optimization commands directly from UI.
     */
    public function optimize(Request $request)
    {
        $target = $request->input('target', 'all');

        try {
            $messages = [];
            if ($target === 'config' || $target === 'all') {
                Artisan::call('config:cache');
                $messages[] = 'Optimasi Config (php artisan config:cache) Berhasil';
            }
            if ($target === 'route' || $target === 'all') {
                Artisan::call('route:clear');
                Artisan::call('route:cache');
                $messages[] = 'Optimasi Route (php artisan route:cache) Berhasil';
            }
            if ($target === 'view' || $target === 'all') {
                Artisan::call('view:cache');
                $messages[] = 'Optimasi View (php artisan view:cache) Berhasil';
            }
            if ($target === 'disable_debug') {
                $envPath = app()->environmentFilePath();
                if (!file_exists($envPath)) {
                    $envPath = base_path('.env');
                }

                if (file_exists($envPath) && is_writable($envPath)) {
                    $envContent = file_get_contents($envPath);
                    if (preg_match('/^APP_DEBUG\s*=\s*true/im', $envContent)) {
                        $envContent = preg_replace('/^APP_DEBUG\s*=\s*true/im', 'APP_DEBUG=false', $envContent);
                    } elseif (!preg_match('/^APP_DEBUG\s*=/im', $envContent)) {
                        $envContent .= "\nAPP_DEBUG=false\n";
                    }
                    file_put_contents($envPath, $envContent);
                    Artisan::call('config:cache');
                    $messages[] = 'APP_DEBUG Berhasil Dimatikan (false)';
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'File .env tidak dapat diubah (Permission Denied). Silakan ubah APP_DEBUG=false pada .env secara manual.',
                    ], 422);
                }
            }
            if ($target === 'set_max_connections') {
                try {
                    DB::statement("SET GLOBAL max_connections = 500");
                    $messages[] = 'MySQL max_connections Berhasil Dinaikkan ke 500';
                } catch (\Throwable $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User MySQL tidak memiliki hak akses (SUPER Privilege) untuk mengubah max_connections secara query. Silakan edit file my.ini/my.cnf: max_connections = 500.',
                    ], 422);
                }
            }

            return response()->json([
                'success' => true,
                'message' => implode(' & ', $messages) . '.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menjalankan optimasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX endpoint for live capacity simulation calculator.
     */
    public function simulate(Request $request)
    {
        $studentsCount = max(1, (int) $request->input('students', 300));
        $timeWindowSeconds = max(1, (int) $request->input('window', 5));

        $serverInfo = $this->getServerDiagnostics();
        $dbInfo = $this->getDatabaseDiagnostics();
        $laravelInfo = $this->getLaravelDiagnostics();

        $simulation = $this->runSimulation($studentsCount, $timeWindowSeconds, $serverInfo, $dbInfo, $laravelInfo);

        return response()->json($simulation);
    }

    /**
     * Fetch PHP and OS Server Hardware Specs.
     */
    private function getServerDiagnostics()
    {
        // CPU Cores Detection
        $cpuCores = 2; // Default fallback
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $processors = getenv('NUMBER_OF_PROCESSORS');
            if ($processors && is_numeric($processors)) {
                $cpuCores = (int) $processors;
            } else {
                @exec('wmic cpu get NumberOfLogicalProcessors', $output);
                if (isset($output[1]) && is_numeric(trim($output[1]))) {
                    $cpuCores = (int) trim($output[1]);
                }
            }
        } else {
            if (is_file('/proc/cpuinfo')) {
                $cpuinfo = file_get_contents('/proc/cpuinfo');
                preg_match_all('/^processor/m', $cpuinfo, $matches);
                $cpuCores = count($matches[0]) ?: 2;
            } else {
                $nproc = @shell_exec('nproc');
                if ($nproc && is_numeric(trim($nproc))) {
                    $cpuCores = (int) trim($nproc);
                }
            }
        }

        // Memory Limit in MB
        $memoryLimitRaw = ini_get('memory_limit');
        $memoryLimitMB = $this->parseSizeToMB($memoryLimitRaw);

        // System Total RAM estimation (MB)
        $totalSystemRamMB = 4096; // Default fallback 4GB
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            @exec('wmic OS get TotalVisibleMemorySize', $ramOutput);
            if (isset($ramOutput[1]) && is_numeric(trim($ramOutput[1]))) {
                $totalSystemRamMB = round((float) trim($ramOutput[1]) / 1024);
            }
        } else {
            if (is_file('/proc/meminfo')) {
                $meminfo = file_get_contents('/proc/meminfo');
                if (preg_match('/MemTotal:\s+(\d+)\s+kB/i', $meminfo, $matches)) {
                    $totalSystemRamMB = round((float) $matches[1] / 1024);
                }
            }
        }

        // OPcache Status
        $opcacheEnabled = false;
        $opcacheMemoryMB = 0;
        if (extension_loaded('Zend OPcache')) {
            $status = @opcache_get_status(false);
            if (is_array($status) && !empty($status['opcache_enabled'])) {
                $opcacheEnabled = true;
                $opcacheMemoryMB = round(($status['memory_usage']['used_memory'] ?? 0) / 1024 / 1024, 1);
            }
        }

        return [
            'os' => PHP_OS_FAMILY . ' (' . php_uname('s') . ' ' . php_uname('r') . ')',
            'php_version' => PHP_VERSION,
            'sapi' => php_sapi_name(),
            'cpu_cores' => $cpuCores,
            'total_ram_mb' => $totalSystemRamMB,
            'memory_limit' => $memoryLimitRaw,
            'memory_limit_mb' => $memoryLimitMB,
            'max_execution_time' => (int) ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'opcache_enabled' => $opcacheEnabled,
            'opcache_memory_mb' => $opcacheMemoryMB,
        ];
    }

    /**
     * Fetch MySQL Database Server Parameters.
     */
    private function getDatabaseDiagnostics()
    {
        $version = 'Unknown';
        $maxConnections = 151; // MySQL Default
        $threadsConnected = 1;
        $innodbBufferPoolMB = 128;
        $dbSizeBytes = 0;

        try {
            // Version
            $verResult = DB::select("SELECT VERSION() as ver");
            if (!empty($verResult)) {
                $version = $verResult[0]->ver;
            }

            // Max Connections
            $maxConnRes = DB::select("SHOW VARIABLES LIKE 'max_connections'");
            if (!empty($maxConnRes)) {
                $maxConnections = (int) $maxConnRes[0]->Value;
            }

            // Threads Connected
            $threadsRes = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            if (!empty($threadsRes)) {
                $threadsConnected = (int) $threadsRes[0]->Value;
            }

            // InnoDB Buffer Pool Size
            $bufferRes = DB::select("SHOW VARIABLES LIKE 'innodb_buffer_pool_size'");
            if (!empty($bufferRes)) {
                $innodbBufferPoolMB = round(((float) $bufferRes[0]->Value) / 1024 / 1024, 1);
            }

            // Database Size
            $dbName = config('database.connections.mysql.database');
            $sizeRes = DB::select("SELECT SUM(data_length + index_length) AS size FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
            if (!empty($sizeRes) && isset($sizeRes[0]->size)) {
                $dbSizeBytes = (float) $sizeRes[0]->size;
            }
        } catch (\Throwable $e) {
            // Soft fail if DB query blocked
        }

        return [
            'version' => $version,
            'max_connections' => $maxConnections,
            'threads_connected' => $threadsConnected,
            'innodb_buffer_pool_mb' => $innodbBufferPoolMB,
            'db_size_mb' => round($dbSizeBytes / 1024 / 1024, 2),
        ];
    }

    /**
     * Fetch Laravel Application Optimizations & Configs.
     */
    private function getLaravelDiagnostics()
    {
        return [
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'config_cached' => app()->configurationIsCached(),
            'routes_cached' => app()->routesAreCached(),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
        ];
    }

    /**
     * Compute CBT Concurrency Safety Analysis.
     */
    private function calculateCapacity(array $server, array $db, array $laravel)
    {
        // 1. Estimate average PHP RAM per request
        $ramPerRequestMB = 28; // ~28MB per Laravel worker

        // 2. RAM available for PHP Workers (assume 60% of total RAM dedicated to PHP-FPM/Apache)
        $usableRamForPhpMB = $server['total_ram_mb'] * 0.60;
        $maxPhpWorkersRam = max(5, floor($usableRamForPhpMB / $ramPerRequestMB));

        // 3. CPU Core worker multiplier (~25 workers per core for IO bound web requests)
        $maxPhpWorkersCpu = $server['cpu_cores'] * 25;

        // 4. Concurrency Limit (Workers) bounded by RAM, CPU, and DB max_connections
        $estimatedMaxWorkers = min($maxPhpWorkersRam, $maxPhpWorkersCpu, max(10, $db['max_connections'] - 15));

        // 5. Average response time per CBT request (seconds)
        $baseLatency = 0.035; // 35ms base response time for cached CBT exam show page

        if (!$server['opcache_enabled']) {
            $baseLatency += 0.040; // +40ms if OPcache disabled
        }
        if ($laravel['app_debug']) {
            $baseLatency += 0.035; // +35ms if Debug mode ON
        }
        if (!$laravel['config_cached']) {
            $baseLatency += 0.015; // +15ms if config uncached
        }
        if ($laravel['cache_driver'] === 'file') {
            $baseLatency += 0.010; // +10ms for disk I/O session/cache
        }

        // Throughput: Requests Per Second (RPS)
        $requestsPerSecond = round($estimatedMaxWorkers / $baseLatency, 1);

        // Capacity Calculations:
        // Burst Capacity: 5-second window (Siswa mengerjakan ujian & simpan jawaban serentak dalam 5 detik)
        $burstCapacity = (int) round($requestsPerSecond * 5);

        // Safe Staggered Capacity: 60-second window (Siswa berpindah soal & menyimpan jawaban dalam 1-2 menit)
        $safeStaggeredCapacity = (int) round($requestsPerSecond * 45);

        // Primary Bottleneck Identification
        $bottlenecks = [];
        if ($db['max_connections'] < 200) {
            $bottlenecks[] = [
                'type' => 'warning',
                'title' => 'MySQL Max Connections Rendah (' . $db['max_connections'] . ')',
                'desc' => 'Default MySQL max_connections (' . $db['max_connections'] . ') berpotensi habis jika >' . ($db['max_connections'] - 20) . ' siswa menyimpan jawaban bersamaan saat ujian berlangsung. Rekomendasi: ubah `max_connections = 500` di my.ini/my.cnf atau klik tombol di bawah.',
                'action_target' => 'set_max_connections',
                'action_label' => 'Naikkan max_connections ke 500 Sekarang',
            ];
        }
        if (!$server['opcache_enabled']) {
            $bottlenecks[] = [
                'type' => 'warning',
                'title' => 'Zend OPcache Belum Aktif',
                'desc' => 'OPcache dapat melipatgandakan kecepatan PHP hingga 2x-3x lipat tanpa membutuhkan RAM tambahan saat ujian berlangsung.',
            ];
        }
        if ($laravel['app_debug']) {
            $bottlenecks[] = [
                'type' => 'danger',
                'title' => 'APP_DEBUG Masih TRUE',
                'desc' => 'Mode Debug mengonsumsi memori besar untuk setiap request log dan memperlambat response time saat siswa mengerjakan ujian.',
                'action_target' => 'disable_debug',
                'action_label' => 'Matikan APP_DEBUG Sekarang',
            ];
        }
        if (!$laravel['config_cached'] || !$laravel['routes_cached']) {
            $bottlenecks[] = [
                'type' => 'info',
                'title' => 'Cache Laravel Belum Dioptimasi',
                'desc' => 'Jalankan `php artisan config:cache` dan `php artisan route:cache` sebelum ujian dilaksanakan.',
                'action_target' => 'all',
                'action_label' => 'Jalankan Optimasi Cache Sekarang',
            ];
        }

        // Dynamic Concurrency Capacity Grade & Status Badge (Indonesian Number Format)
        if ($burstCapacity >= 100) {
            $statusGrade = 'success';
            $statusBadge = 'SANGAT AMAN (Sanggup ' . number_format($burstCapacity, 0, ',', '.') . ' Siswa Mengerjakan Ujian Serentak)';
        } elseif ($burstCapacity >= 30) {
            $statusGrade = 'warning';
            $statusBadge = 'OPTIMAL BERTAHAP (Sanggup ' . number_format($burstCapacity, 0, ',', '.') . ' Siswa Mengerjakan Ujian Serentak)';
        } else {
            $statusGrade = 'danger';
            $statusBadge = 'KAPASITAS TERBATAS (Sanggup ' . number_format($burstCapacity, 0, ',', '.') . ' Siswa Mengerjakan Ujian Serentak)';
        }

        return [
            'estimated_workers' => $estimatedMaxWorkers,
            'avg_response_time_ms' => round($baseLatency * 1000),
            'rps' => $requestsPerSecond,
            'burst_capacity' => $burstCapacity,
            'safe_staggered_capacity' => $safeStaggeredCapacity,
            'status_grade' => $statusGrade,
            'status_badge' => $statusBadge,
            'bottlenecks' => $bottlenecks,
        ];
    }

    /**
     * Run simulation math for interactive calculator.
     */
    private function runSimulation(int $students, int $windowSec, array $server, array $db, array $laravel)
    {
        $analysis = $this->calculateCapacity($server, $db, $laravel);
        $rpsNeeded = round($students / max(1, $windowSec), 1);

        $serverMaxRps = $analysis['rps'];
        $capacityRatio = round(($serverMaxRps / max(0.1, $rpsNeeded)) * 100);
        $loadPercentage = min(999, round(($rpsNeeded / max(0.1, $serverMaxRps)) * 100));

        $dbConnNeeded = min($students, $analysis['estimated_workers']);
        $isDbSufficient = $db['max_connections'] >= $dbConnNeeded;

        $ramNeededMB = round($dbConnNeeded * 28 + 512); // PHP + MySQL overhead
        $isRamSufficient = $server['total_ram_mb'] >= $ramNeededMB;

        $passed = ($serverMaxRps >= $rpsNeeded) && $isDbSufficient;

        // Recommended time window if staggered entry is needed
        $recommendedSec = max(5, (int) ceil($students / max(0.5, $serverMaxRps)));
        $recommendedMinutes = round($recommendedSec / 60, 1);

        if ($passed) {
            $statusLevel = 'safe';
            $statusBadge = 'SIAP & SANGAT AMAN';
            $message = "Server Anda DIPREDIKSI SIAP & AMAN menangani " . number_format($students, 0, ',', '.') . " siswa yang aktif mengerjakan ujian CBT (navigasi soal & simpan jawaban) secara bersamaan dalam rentang {$windowSec} detik.";
        } elseif ($students <= $analysis['safe_staggered_capacity']) {
            $statusLevel = 'warning';
            $statusBadge = 'REKOMENDASI BERTAHAP';
            $message = "Server sanggup menampung " . number_format($students, 0, ',', '.') . " siswa mengerjakan ujian, tetapi disarankan membagi gelombang/sesi ujian agar lonjakan lalulintas simpan jawaban (jeda total ~{$recommendedSec} detik atau " . ($recommendedMinutes > 1 ? "{$recommendedMinutes} menit" : "1-2 menit") . ") tetap lancar dan stabil.";
        } else {
            $statusLevel = 'danger';
            $statusBadge = 'BERPOTENSI OVERLOAD';
            $message = "Jumlah " . number_format($students, 0, ',', '.') . " siswa mengerjakan ujian bersamaan melebihi batas aman lalulintas server (" . number_format($analysis['burst_capacity'], 0, ',', '.') . " siswa/5 dtk & " . number_format($analysis['safe_staggered_capacity'], 0, ',', '.') . " siswa bertahap). Disarankan membagi ujian menjadi beberapa sesi atau menaikkan spesifikasi server.";
        }

        // Dynamic Recommended Specs Calculation based on target students count
        $recCpu = 2;
        $recRamGB = 4;
        $recMaxConn = 200;

        if ($students > 1200) {
            $recCpu = 32;
            $recRamGB = 64;
            $recMaxConn = 1500;
        } elseif ($students > 600) {
            $recCpu = 16;
            $recRamGB = 32;
            $recMaxConn = 1000;
        } elseif ($students > 300) {
            $recCpu = 8;
            $recRamGB = 16;
            $recMaxConn = 600;
        } elseif ($students > 100) {
            $recCpu = 4;
            $recRamGB = 8;
            $recMaxConn = 400;
        }

        $currentRamGB = round($server['total_ram_mb'] / 1024, 1);

        $specRecommendation = [
            'cpu' => [
                'label' => 'Prosesor (CPU Cores)',
                'current' => $server['cpu_cores'] . ' Core',
                'recommended' => $recCpu . ' Core',
                'is_sufficient' => $server['cpu_cores'] >= $recCpu,
            ],
            'ram' => [
                'label' => 'Memori Utama (RAM)',
                'current' => $currentRamGB . ' GB',
                'recommended' => $recRamGB . ' GB',
                'is_sufficient' => $currentRamGB >= $recRamGB,
            ],
            'max_connections' => [
                'label' => 'MySQL max_connections',
                'current' => $db['max_connections'] . ' Conn',
                'recommended' => $recMaxConn . ' Conn',
                'is_sufficient' => $db['max_connections'] >= $recMaxConn,
            ],
            'opcache' => [
                'label' => 'Zend OPcache',
                'current' => $server['opcache_enabled'] ? 'Aktif' : 'Nonaktif',
                'recommended' => 'WAJIB Aktif',
                'is_sufficient' => $server['opcache_enabled'],
            ],
            'app_debug' => [
                'label' => 'Mode APP_DEBUG',
                'current' => $laravel['app_debug'] ? 'TRUE (Mode Debug)' : 'FALSE (Production)',
                'recommended' => 'FALSE (Production)',
                'is_sufficient' => !$laravel['app_debug'],
            ],
        ];

        return [
            'students' => $students,
            'window_sec' => $windowSec,
            'rps_needed' => $rpsNeeded,
            'server_max_rps' => $serverMaxRps,
            'capacity_ratio' => $capacityRatio,
            'load_percentage' => $loadPercentage,
            'db_conn_needed' => $dbConnNeeded,
            'db_max_conn' => $db['max_connections'],
            'is_db_sufficient' => $isDbSufficient,
            'ram_needed_mb' => $ramNeededMB,
            'ram_available_mb' => $server['total_ram_mb'],
            'is_ram_sufficient' => $isRamSufficient,
            'passed' => $passed,
            'status_level' => $statusLevel,
            'status_badge' => $statusBadge,
            'recommended_window_sec' => $recommendedSec,
            'recommended_minutes' => $recommendedMinutes,
            'burst_limit' => $analysis['burst_capacity'],
            'staggered_limit' => $analysis['safe_staggered_capacity'],
            'message' => $message,
            'spec_recommendation' => $specRecommendation,
        ];
    }

    /**
     * Convert memory string (e.g. 128M, 2G, -1) to integer MB.
     */
    private function parseSizeToMB($size)
    {
        if ($size === '-1' || $size === -1) {
            return 2048; // Assume 2GB if unlimited
        }

        $unit = strtoupper(substr($size, -1));
        $value = (int) $size;

        switch ($unit) {
            case 'G':
                return $value * 1024;
            case 'M':
                return $value;
            case 'K':
                return round($value / 1024);
            default:
                return $value;
        }
    }
}
