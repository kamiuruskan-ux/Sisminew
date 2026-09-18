<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DatabaseMaintenanceController extends Controller
{
    /**
     * Display database status, pending migrations, and backup files.
     */
    public function index()
    {
        // 1. Check pending migrations
        $migrationFiles = File::exists(database_path('migrations')) ? File::files(database_path('migrations')) : [];
        $executedMigrations = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('migrations')) {
                $executedMigrations = DB::table('migrations')->pluck('migration')->toArray();
            }
        } catch (\Throwable $e) {
            $executedMigrations = [];
        }

        $pendingMigrations = [];
        foreach ($migrationFiles as $file) {
            $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            if (!in_array($filename, $executedMigrations)) {
                $pendingMigrations[] = [
                    'file' => $file->getFilename(),
                    'name' => $filename,
                ];
            }
        }

        // 2. Check Database Info
        $dbName = config('database.connections.mysql.database', 'Unknown');
        $dbHost = config('database.connections.mysql.host', '127.0.0.1');
        $tableCount = 0;
        $dbSize = 'Unknown';

        try {
            $tables = DB::select('SHOW TABLES');
            $tableCount = count($tables);

            $sizeResult = DB::select("
                SELECT SUM(data_length + index_length) AS size 
                FROM information_schema.TABLES 
                WHERE table_schema = ?
            ", [$dbName]);

            if (!empty($sizeResult) && isset($sizeResult[0]->size)) {
                $bytes = $sizeResult[0]->size;
                $dbSize = number_format($bytes / (1024 * 1024), 2) . ' MB';
            }
        } catch (\Throwable $e) {
            // connection or permission issue fallback
        }

        // 3. List Backup SQL Files from public_path('doc/backups') according to upload rules
        $backupDir = public_path('doc/backups');
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true, true);
        }

        $backups = [];
        $files = File::files($backupDir);
        foreach ($files as $file) {
            $ext = strtolower($file->getExtension());
            if ($ext === 'sql' || $ext === 'zip') {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => number_format($file->getSize() / 1024, 2) . ' KB',
                    'created_at' => \Carbon\Carbon::createFromTimestamp($file->getMTime())->format('d M Y, H:i:s'),
                    'mtime' => $file->getMTime(),
                ];
            }
        }

        usort($backups, function ($a, $b) {
            return $b['mtime'] <=> $a['mtime'];
        });

        return view('admin.database-maintenance.index', compact(
            'pendingMigrations',
            'executedMigrations',
            'migrationFiles',
            'dbName',
            'dbHost',
            'tableCount',
            'dbSize',
            'backups'
        ));
    }

    /**
     * Run pending database migrations via Artisan.
     */
    public function migrate(Request $request)
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            return redirect()->route('admin.database-maintenance.index')
                ->with('success', 'Update database / migrasi berhasil dijalankan!')
                ->with('migration_output', trim($output) ?: 'Tidak ada migrasi baru yang diproses.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.database-maintenance.index')
                ->with('error', 'Gagal memperbarui database: ' . $e->getMessage());
        }
    }

    /**
     * Create database SQL backup stored in public_path('doc/backups/').
     */
    public function backup(Request $request)
    {
        try {
            $backupDir = public_path('doc/backups');
            if (!File::isDirectory($backupDir)) {
                File::makeDirectory($backupDir, 0755, true, true);
            }

            $dbName = config('database.connections.mysql.database', 'sekolah');
            $filename = 'backup_' . $dbName . '_' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = $backupDir . '/' . $filename;

            $tables = DB::select('SHOW TABLES');

            $sqlContent = "-- SQL Dump Sekolah LRV\n";
            $sqlContent .= "-- Generated at: " . date('Y-m-d H:i:s') . "\n";
            $sqlContent .= "-- Database: `" . $dbName . "`\n\n";
            $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $tableObj) {
                $tableName = current((array)$tableObj);

                $sqlContent .= "-- Table structure for `" . $tableName . "`\n";
                $sqlContent .= "DROP TABLE IF EXISTS `" . $tableName . "`;\n";

                $createTableRes = DB::select("SHOW CREATE TABLE `" . $tableName . "`");
                if (!empty($createTableRes)) {
                    $createSql = ((array)$createTableRes[0])['Create Table'] ?? '';
                    $sqlContent .= $createSql . ";\n\n";
                }

                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sqlContent .= "-- Dumping data for table `" . $tableName . "`\n";
                    foreach ($rows->chunk(100) as $chunk) {
                        $sqlContent .= "INSERT INTO `" . $tableName . "` VALUES \n";
                        $values = [];
                        foreach ($chunk as $row) {
                            $rowValues = [];
                            foreach ((array)$row as $val) {
                                if (is_null($val)) {
                                    $rowValues[] = 'NULL';
                                } else {
                                    $valStr = str_replace(["\\", "'"], ["\\\\", "\\'"], $val);
                                    $valStr = str_replace("\r\n", "\\r\\n", $valStr);
                                    $valStr = str_replace("\n", "\\n", $valStr);
                                    $rowValues[] = "'" . $valStr . "'";
                                }
                            }
                            $values[] = "(" . implode(", ", $rowValues) . ")";
                        }
                        $sqlContent .= implode(",\n", $values) . ";\n\n";
                    }
                }
            }

            $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";

            File::put($filePath, $sqlContent);

            return redirect()->route('admin.database-maintenance.index')
                ->with('success', 'Backup database berhasil dibuat: ' . $filename);
        } catch (\Throwable $e) {
            return redirect()->route('admin.database-maintenance.index')
                ->with('error', 'Gagal membuat backup database: ' . $e->getMessage());
        }
    }

    /**
     * Download backup file.
     */
    public function download($filename)
    {
        $filename = basename($filename);
        $filePath = public_path('doc/backups/' . $filename);

        if (!File::exists($filePath)) {
            return redirect()->route('admin.database-maintenance.index')
                ->with('error', 'File backup tidak ditemukan.');
        }

        return response()->download($filePath);
    }

    /**
     * Delete backup file.
     */
    public function destroy($filename)
    {
        $filename = basename($filename);
        $filePath = public_path('doc/backups/' . $filename);

        if (File::exists($filePath)) {
            File::delete($filePath);
            return redirect()->route('admin.database-maintenance.index')
                ->with('success', 'File backup berhasil dihapus.');
        }

        return redirect()->route('admin.database-maintenance.index')
            ->with('error', 'File backup tidak ditemukan.');
    }
}
