<?php

namespace App\Console\Commands;

use App\Services\AttendanceReminderService;
use Illuminate\Console\Command;

class AttendanceReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-reminders {--session=auto : morning, afternoon, or auto} {--force : bypass weekend/holiday checks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi pengingat presensi otomatis ke ponsel dan PC guru/pegawai';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $session = $this->option('session') ?: 'auto';
        $this->info("Menjalankan pengingat presensi sesi [{$session}]...");

        $result = AttendanceReminderService::runReminders($session);

        if ($result['success']) {
            $this->info("Berhasil: " . $result['message']);
            $this->table(['Sesi', 'Terkirim', 'Pesan'], [
                [$result['session'] ?? $session, $result['sent_count'] ?? 0, $result['message']]
            ]);
            return Command::SUCCESS;
        }

        $this->error("Gagal: " . ($result['message'] ?? 'Terjadi kesalahan.'));
        return Command::FAILURE;
    }
}
