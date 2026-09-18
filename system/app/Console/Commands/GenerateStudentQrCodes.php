<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;

class GenerateStudentQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:generate-qr-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique QR codes for all students without one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting QR code generation for students...');

        $studentsWithoutQr = Student::whereNull('qr_code')->orWhere('qr_code', '')->get();

        if ($studentsWithoutQr->isEmpty()) {
            $this->info('All students already have QR codes!');
            return 0;
        }

        $this->info("Found {$studentsWithoutQr->count()} students without QR codes.");

        $bar = $this->output->createProgressBar($studentsWithoutQr->count());
        $bar->start();

        $successCount = 0;
        $failCount = 0;

        foreach ($studentsWithoutQr as $student) {
            try {
                $qrCode = $this->generateUniqueQrCode();
                $student->update(['qr_code' => $qrCode]);
                $successCount++;
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nFailed to generate QR code for student {$student->id}: " . $e->getMessage());
                $failCount++;
            }
        }

        $bar->finish();
        $this->newLine();

        $this->info("✓ QR code generation completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Success', $successCount],
                ['Failed', $failCount],
                ['Total', $studentsWithoutQr->count()],
            ]
        );

        return 0;
    }

    private function generateUniqueQrCode(): string
    {
        do {
            $qrCode = 'STD-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
        } while (Student::where('qr_code', $qrCode)->exists());

        return $qrCode;
    }
}
