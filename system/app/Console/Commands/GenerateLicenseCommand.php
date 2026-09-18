<?php

namespace App\Console\Commands;

use App\Services\LicenseManager;
use Illuminate\Console\Command;

class GenerateLicenseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:generate 
                            {--domain= : Target domain (e.g. sekolah-lrv.lapakcode.com or localhost)}
                            {--client= : Client / School name}
                            {--install : Automatically install generated key to local application database/file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Cryptographic Domain-Bound Lifetime License Key';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=====================================================');
        $this->info('    CRYPTOGRAPHIC LIFETIME LICENSE GENERATOR SYSTEM  ');
        $this->info('=====================================================');

        $domain = $this->option('domain');
        if (!$domain) {
            $domain = $this->ask('Masukkan Nama Domain Target (contoh: sekolah-lrv.lapakcode.com atau localhost)', request() ? request()->getHost() : 'localhost');
        }

        $client = $this->option('client');
        if (!$client) {
            $client = $this->ask('Masukkan Nama Sekolah / Klien', 'SMA Negeri 1 Jakarta');
        }

        $licenseKey = LicenseManager::generateLicense($domain, $client);

        $this->newLine();
        $this->info('✅ LISENSI KRIPTOGRAFIK LIFETIME BERHASIL DIBUAT:');
        $this->line('-----------------------------------------------------');
        $this->line('<comment>Domain Target:</comment> ' . $domain);
        $this->line('<comment>Nama Klien:</comment>   ' . $client);
        $this->line('<comment>Tipe Lisensi:</comment> LIFETIME (Permanen)');
        $this->line('-----------------------------------------------------');
        $this->newLine();

        $this->warn('KUNCI LISENSI APLIKASI (SALIN KUNCI DI BAWAH INI):');
        $this->line($licenseKey);
        $this->newLine();

        // Verification test
        $verification = LicenseManager::verifyLicense($licenseKey, $domain);
        if ($verification['valid']) {
            $this->info('✔️ Status Uji Validasi Kriptografik: PASSED (VALID)');
        } else {
            $this->error('❌ Status Uji Validasi Kriptografik: FAILED (' . $verification['message'] . ')');
        }

        if ($this->option('install') || ($this->interactive && $this->confirm('Apakah Anda ingin langsung memasang lisensi ini pada server lokal?', false))) {
            LicenseManager::saveLicense($licenseKey);
            $this->info('🚀 Lisensi berhasil dipasang ke sistem lokal ini!');
        }

        return 0;
    }
}
