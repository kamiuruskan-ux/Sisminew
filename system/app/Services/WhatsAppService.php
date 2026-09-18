<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Get the active provider name.
     */
    public static function getActiveProvider(): string
    {
        return Setting::get('wa_gateway_provider', 'disabled');
    }

    /**
     * Format phone number to standard international format (e.g. 628123456789).
     */
    public static function formatPhoneNumber(string $phone): string
    {
        // Remove non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($phone)) {
            return '';
        }

        // 08123... -> 628123...
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        // 8123... -> 628123...
        elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Send WhatsApp message using the active provider.
     */
    public static function sendMessage(string $recipientPhone, string $message): array
    {
        $provider = self::getActiveProvider();
        $formattedPhone = self::formatPhoneNumber($recipientPhone);

        if (empty($formattedPhone)) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp penerima tidak valid.',
            ];
        }

        if ($provider === 'fonnte') {
            return self::sendViaFonnte($formattedPhone, $message);
        } elseif ($provider === 'onesender') {
            return self::sendViaOnesender($formattedPhone, $message);
        } else {
            return [
                'success' => false,
                'message' => 'WhatsApp Gateway sedang nonaktif. Silakan aktifkan Fonnte atau Onesender di Konfigurasi System.',
            ];
        }
    }

    /**
     * Send WhatsApp message via Fonnte Gateway.
     */
    private static function sendViaFonnte(string $phone, string $message): array
    {
        $token = Setting::get('wa_fonnte_token');
        $apiUrl = Setting::get('wa_fonnte_url', 'https://api.fonnte.com/send');

        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Token API Fonnte belum dikonfigurasi.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(15)->post($apiUrl, [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                Log::info("WA_FONNTE_SUCCESS: Pesan ke {$phone} terkirim. Resp: " . json_encode($result));
                return [
                    'success' => true,
                    'message' => $result['reason'] ?? 'Pesan berhasil terkirim via Fonnte.',
                    'raw' => $result,
                ];
            }

            $errorMessage = $result['reason'] ?? $result['message'] ?? 'Gagal mengirim pesan via Fonnte (' . $response->status() . ')';
            Log::warning("WA_FONNTE_FAILED: Pesan ke {$phone} gagal: {$errorMessage}");

            return [
                'success' => false,
                'message' => $errorMessage,
                'raw' => $result,
            ];
        } catch (\Exception $e) {
            Log::error("WA_FONNTE_EXCEPTION: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Kesalahan koneksi ke server Fonnte: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send WhatsApp message via Onesender Gateway.
     */
    private static function sendViaOnesender(string $phone, string $message): array
    {
        $apiKey = Setting::get('wa_onesender_api_key');
        $apiUrl = Setting::get('wa_onesender_url', 'https://api.onesender.net/api/v1/messages');

        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'API Key Onesender belum dikonfigurasi.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->timeout(15)->post($apiUrl, [
                'recipient' => $phone,
                'phone' => $phone,
                'to' => $phone,
                'message' => $message,
                'text' => $message,
            ]);

            $result = $response->json();

            if ($response->successful() && (!isset($result['status']) || $result['status'] === 'success' || $result['status'] === true || isset($result['id']))) {
                Log::info("WA_ONESENDER_SUCCESS: Pesan ke {$phone} terkirim. Resp: " . json_encode($result));
                return [
                    'success' => true,
                    'message' => $result['message'] ?? 'Pesan berhasil terkirim via Onesender.',
                    'raw' => $result,
                ];
            }

            $errorMessage = $result['message'] ?? $result['error'] ?? 'Gagal mengirim pesan via Onesender (' . $response->status() . ')';
            Log::warning("WA_ONESENDER_FAILED: Pesan ke {$phone} gagal: {$errorMessage}");

            return [
                'success' => false,
                'message' => $errorMessage,
                'raw' => $result,
            ];
        } catch (\Exception $e) {
            Log::error("WA_ONESENDER_EXCEPTION: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Kesalahan koneksi ke server Onesender: ' . $e->getMessage(),
            ];
        }
    }
}
