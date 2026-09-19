<?php

namespace App\Services;

use App\Models\AttendanceAuditLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class FingerprintDeviceService
{
    /**
     * Map hardware error codes to user-friendly messages
     */
    public function resolveErrorMessage(string $errorCode, ?string $rawMessage = null): string
    {
        return match (strtolower(trim($errorCode))) {
            'device_disconnected', 'scanner_disconnected', 'unplugged' =>
                'Scanner USB terputus. Silakan hubungkan kembali kabel scanner ke port USB laptop.',
            'driver_not_installed', 'service_offline', 'ws_error' =>
                'Layanan HID DigitalPersona WebSDK belum aktif pada port 52181. Jalankan DigitalPersona Desktop Service.',
            'permission_denied' =>
                'Izin akses perangkat USB ditolak. Periksa pengaturan izin perangkat pada browser/sistem.',
            'capture_timeout', 'timeout' =>
                'Batas waktu pemindaian habis. Tempelkan jari guru sebelum waktu habis.',
            'poor_quality', 'low_quality', 'quality_fail' =>
                'Kualitas sidik jari kurang jelas. Bersihkan kaca prisma scanner dan tempelkan jari lebih mantap.',
            'duplicate_enrollment' =>
                'Sidik jari ini telah terdaftar untuk guru/pegawai lain dalam database.',
            'template_failed' =>
                'Gagal membentuk template biometrik. Pastikan jari tidak bergeser saat pemindaian.',
            'device_busy', 'busy' =>
                'Scanner sedang sibuk memproses sampel sebelumnya. Tunggu sejenak.',
            default => $rawMessage ?: 'Terjadi kendala pada komunikasi scanner USB HID DigitalPersona.',
        };
    }

    /**
     * Log device telemetry event to audit log and system logs
     */
    public function logDeviceEvent(string $event, array $context = []): void
    {
        $payload = array_merge([
            'event' => $event,
            'device' => $context['device_name'] ?? 'HID DigitalPersona 4500 USB',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ], $context);

        Log::info("FINGERPRINT_HARDWARE_EVENT: [{$event}]", $payload);

        // Store into database audit trail if table exists
        try {
            if (Schema::hasTable('attendance_audit_logs')) {
                AttendanceAuditLog::create([
                    'user_id' => $context['user_id'] ?? auth()->id(),
                    'action' => 'device_' . $event,
                    'method' => 'fingerprint',
                    'status' => in_array($event, ['capture_failed', 'device_removed', 'error']) ? 'error' : 'success',
                    'session_type' => $context['session'] ?? null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'device_info' => $context['device_name'] ?? 'HID DigitalPersona 4500',
                    'details' => $payload,
                ]);
            }
        } catch (\Throwable $e) {
            // Non-blocking fallback
        }
    }
}
