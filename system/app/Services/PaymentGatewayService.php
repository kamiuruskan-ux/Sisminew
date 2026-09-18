<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    /**
     * Create a Midtrans Snap Token for transaction checkout.
     */
    public static function createMidtransTransaction($orderId, $amount, $customer, $itemName = 'Pembayaran Sekolah')
    {
        $serverKey = Setting::get('payment_midtrans_server_key');
        $mode = Setting::get('payment_midtrans_mode', 'sandbox');
        
        $url = $mode === 'production' 
            ? 'https://app.midtrans.com/snap/v1/transactions' 
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => [
                'first_name' => $customer['name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'] ?? '',
            ],
            'item_details' => [
                [
                    'id' => 'ITEM-01',
                    'price' => (int) $amount,
                    'quantity' => 1,
                    'name' => substr($itemName, 0, 50),
                ]
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->withBasicAuth($serverKey, '')
            ->post($url, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'snap_token' => $response->json('token'),
                    'redirect_url' => $response->json('redirect_url'),
                ];
            }

            Log::error('Midtrans API Error: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Midtrans Error: ' . ($response->json('error_messages')[0] ?? $response->body()),
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Koneksi ke Midtrans gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create a Tripay Transaction.
     */
    public static function createTripayTransaction($orderId, $amount, $paymentMethod, $customer, $itemName = 'Pembayaran Sekolah')
    {
        $apiKey = Setting::get('payment_tripay_api_key');
        $privateKey = Setting::get('payment_tripay_private_key');
        $merchantCode = Setting::get('payment_tripay_merchant_code');
        $mode = Setting::get('payment_tripay_mode', 'sandbox');

        $url = $mode === 'production' 
            ? 'https://tripay.co.id/api/transaction/create' 
            : 'https://tripay.co.id/api-sandbox/transaction/create';

        // Calculate Tripay signature: merchantCode + merchantRef + amount
        $signature = hash_hmac('sha256', $merchantCode . $orderId . $amount, $privateKey);

        $payload = [
            'method' => $paymentMethod,
            'merchant_ref' => $orderId,
            'amount' => (int) $amount,
            'customer_name' => $customer['name'],
            'customer_email' => $customer['email'],
            'customer_phone' => $customer['phone'] ?? '',
            'order_items' => [
                [
                    'sku' => 'ITEM-01',
                    'name' => substr($itemName, 0, 50),
                    'price' => (int) $amount,
                    'quantity' => 1,
                ]
            ],
            'expired_time' => time() + (24 * 60 * 60), // 24 hours expiry
            'signature' => $signature,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post($url, $payload);

            if ($response->successful() && $response->json('success') === true) {
                return [
                    'success' => true,
                    'payment_url' => $response->json('data.checkout_url'),
                    'payment_method' => $response->json('data.payment_name'),
                    'instructions' => $response->json('data.instructions'),
                    'qr_url' => $response->json('data.qr_url'),
                    'pay_code' => $response->json('data.pay_code'),
                ];
            }

            Log::error('Tripay API Error: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Tripay Error: ' . ($response->json('message') ?? $response->body()),
            ];
        } catch (\Exception $e) {
            Log::error('Tripay Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Koneksi ke Tripay gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get active payment channels from Tripay API.
     */
    public static function getTripayChannels()
    {
        $apiKey = Setting::get('payment_tripay_api_key');
        $mode = Setting::get('payment_tripay_mode', 'sandbox');

        $url = $mode === 'production' 
            ? 'https://tripay.co.id/api/merchant/payment-channel' 
            : 'https://tripay.co.id/api-sandbox/merchant/payment-channel';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->get($url);

            if ($response->successful() && $response->json('success') === true) {
                return $response->json('data');
            }
        } catch (\Exception $e) {
            Log::error('Tripay channels exception: ' . $e->getMessage());
        }

        // Fallback standard payment channels
        return [
            ['code' => 'BCAVA', 'name' => 'BCA Virtual Account'],
            ['code' => 'MANDIRIVA', 'name' => 'Mandiri Virtual Account'],
            ['code' => 'BNIVA', 'name' => 'BNI Virtual Account'],
            ['code' => 'BRIVA', 'name' => 'BRI Virtual Account'],
            ['code' => 'QRIS', 'name' => 'QRIS (Gopay/OVO/Dana/LinkAja)'],
        ];
    }

    /**
     * Create a Duitku.com Transaction.
     */
    public static function createDuitkuTransaction($orderId, $amount, $customer, $itemName = 'Pembayaran Sekolah')
    {
        $merchantCode = Setting::get('payment_duitku_merchant_code');
        $apiKey = Setting::get('payment_duitku_api_key');
        $mode = Setting::get('payment_duitku_mode', 'sandbox');

        $url = $mode === 'production'
            ? 'https://api-prod.duitku.com/api/merchant/createInvoice'
            : 'https://api-sandbox.duitku.com/api/merchant/createInvoice';

        $timestamp = round(microtime(true) * 1000);
        $signature = hash('sha256', $merchantCode . $timestamp . $apiKey);

        $callbackUrl = route('api.payment.duitku.callback');
        $returnUrl = url('/');

        $payload = [
            'merchantCode' => $merchantCode,
            'paymentAmount' => (int) $amount,
            'merchantOrderId' => $orderId,
            'productDetails' => substr($itemName, 0, 50),
            'email' => $customer['email'],
            'phoneNumber' => $customer['phone'] ?? '',
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl,
            'expiryPeriod' => 60,
            'customerDetail' => [
                'firstName' => $customer['name'],
                'email' => $customer['email'],
                'phoneNumber' => $customer['phone'] ?? '',
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-duitku-signature' => $signature,
                'x-duitku-timestamp' => $timestamp,
                'x-duitku-merchantcode' => $merchantCode,
            ])->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['statusCode']) && $data['statusCode'] === '00') {
                    return [
                        'success' => true,
                        'payment_url' => $data['paymentUrl'],
                        'reference' => $data['reference'] ?? null,
                    ];
                }

                Log::error('Duitku API Error Response: ' . $response->body());
                return [
                    'success' => false,
                    'message' => 'Duitku Error: ' . ($data['statusMessage'] ?? 'Unknown status error'),
                ];
            }

            Log::error('Duitku API HTTP Error: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Duitku HTTP Error: ' . $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Duitku Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Koneksi ke Duitku gagal: ' . $e->getMessage(),
            ];
        }
    }
}

