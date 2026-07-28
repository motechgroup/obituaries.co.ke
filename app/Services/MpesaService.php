<?php

namespace App\Services;

use App\Models\Obituary;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected string $env;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $shortcode;
    protected string $passkey;
    protected string $callbackUrl;
    protected string $transactionType;
    protected bool $mockMode;

    public function __construct()
    {
        $this->env = \App\Models\Setting::get('mpesa_env', config('mpesa.env', 'sandbox'));
        $this->consumerKey = trim(\App\Models\Setting::get('mpesa_consumer_key', config('mpesa.consumer_key', '')));
        $this->consumerSecret = trim(\App\Models\Setting::get('mpesa_consumer_secret', config('mpesa.consumer_secret', '')));
        $this->shortcode = trim(\App\Models\Setting::get('mpesa_shortcode', config('mpesa.shortcode', '174379')));
        $this->passkey = trim(\App\Models\Setting::get('mpesa_passkey', config('mpesa.passkey', '')));
        $this->callbackUrl = \App\Models\Setting::get('mpesa_callback_url', config('mpesa.callback_url', url('/api/v1/mpesa/callback')));
        $this->transactionType = \App\Models\Setting::get('mpesa_transaction_type', 'CustomerPayBillOnline');

        // Check if mock mode is explicitly turned on/off in Admin Settings
        $dbMock = \App\Models\Setting::get('mpesa_mock_mode', null);
        if ($dbMock !== null) {
            $this->mockMode = (bool) $dbMock;
        } else {
            // Default to mock mode only if credentials are not configured
            $this->mockMode = empty($this->consumerKey) || empty($this->consumerSecret);
        }
    }

    /**
     * Format phone number to standard 2547XXXXXXXX or 2541XXXXXXXX
     */
    public function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            $phone = '254' . $phone;
        }

        return $phone;
    }

    /**
     * Get OAuth Access Token from Safaricom Daraja
     */
    public function getAccessToken(): array
    {
        if ($this->mockMode) {
            return [
                'success' => true,
                'token' => 'mock_access_token_' . time(),
            ];
        }

        $url = config("mpesa.urls.{$this->env}.oauth");

        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'token' => $response->json('access_token'),
                ];
            }

            $errorBody = $response->json();
            $errorMessage = $errorBody['errorMessage'] ?? $errorBody['error_description'] ?? $response->body();

            Log::error('M-Pesa Token Error', ['status' => $response->status(), 'body' => $response->body()]);

            return [
                'success' => false,
                'message' => 'M-Pesa OAuth Failed: ' . $errorMessage . ' (Check Consumer Key, Consumer Secret & Environment)',
            ];
        } catch (\Throwable $e) {
            Log::error('M-Pesa Token Exception', ['message' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'M-Pesa OAuth Exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Initiate STK Push
     */
    public function initiateStkPush(Obituary $obituary, string $phoneNumber, float $amount = 500.00): array
    {
        $formattedPhone = $this->formatPhone($phoneNumber);
        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        // Create initial pending payment record
        $payment = Payment::create([
            'obituary_id' => $obituary->id,
            'phone_number' => $formattedPhone,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        if ($this->mockMode) {
            $merchantRequestId = 'MOCK-MR-' . uniqid();
            $checkoutRequestId = 'MOCK-CR-' . uniqid();

            $payment->update([
                'merchant_request_id' => $merchantRequestId,
                'checkout_request_id' => $checkoutRequestId,
            ]);

            return [
                'success' => true,
                'payment' => $payment,
                'CheckoutRequestID' => $checkoutRequestId,
                'MerchantRequestID' => $merchantRequestId,
                'ResponseDescription' => 'Success. Request accepted for processing (Mock Mode)',
                'is_mock' => true,
            ];
        }

        $tokenResult = $this->getAccessToken();
        if (!$tokenResult['success']) {
            $payment->update(['status' => 'failed']);
            return [
                'success' => false,
                'message' => $tokenResult['message'],
            ];
        }

        $accessToken = $tokenResult['token'];
        $stkUrl = config("mpesa.urls.{$this->env}.stkpush");

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => $this->transactionType,
            'Amount' => (int) $amount,
            'PartyA' => $formattedPhone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $formattedPhone,
            'CallBackURL' => $this->callbackUrl,
            'AccountReference' => "OBIT-{$obituary->id}",
            'TransactionDesc' => "Obituary Publishing - {$obituary->full_name}",
        ];

        try {
            $response = Http::withToken($accessToken)->post($stkUrl, $payload);
            $data = $response->json();

            if ($response->successful() && isset($data['ResponseCode']) && $data['ResponseCode'] == '0') {
                $payment->update([
                    'merchant_request_id' => $data['MerchantRequestID'] ?? null,
                    'checkout_request_id' => $data['CheckoutRequestID'] ?? null,
                ]);

                return [
                    'success' => true,
                    'payment' => $payment,
                    'CheckoutRequestID' => $data['CheckoutRequestID'],
                    'MerchantRequestID' => $data['MerchantRequestID'],
                    'ResponseDescription' => $data['ResponseDescription'] ?? 'STK push sent.',
                    'is_mock' => false,
                ];
            }

            $payment->update(['status' => 'failed']);
            $errorMessage = $data['errorMessage'] ?? $data['CustomerMessage'] ?? $data['ResponseDescription'] ?? 'M-Pesa STK push failed.';
            Log::error('M-Pesa STK Push Failed', ['response' => $data, 'payload' => $payload]);

            return [
                'success' => false,
                'message' => 'STK Push Failed: ' . $errorMessage,
            ];
        } catch (\Throwable $e) {
            Log::error('STK Push Exception', ['message' => $e->getMessage()]);
            $payment->update(['status' => 'failed']);
            return [
                'success' => false,
                'message' => 'Payment request failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process M-Pesa Callback payload
     */
    public function processCallback(array $payload): bool
    {
        Log::info('M-Pesa Callback Received', $payload);

        $stkCallback = $payload['Body']['stkCallback'] ?? null;
        if (!$stkCallback) {
            return false;
        }

        $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
        $resultCode = $stkCallback['ResultCode'] ?? null;
        $resultDesc = $stkCallback['ResultDesc'] ?? null;

        $payment = Payment::where('checkout_request_id', $checkoutRequestId)->first();
        if (!$payment) {
            Log::warning('Payment record not found for CheckoutRequestID', ['id' => $checkoutRequestId]);
            return false;
        }

        if ($resultCode == 0) {
            $mpesaReceiptNumber = null;
            $items = $stkCallback['CallbackMetadata']['Item'] ?? [];

            foreach ($items as $item) {
                if (($item['Name'] ?? '') === 'MpesaReceiptNumber') {
                    $mpesaReceiptNumber = $item['Value'] ?? null;
                }
            }

            $payment->update([
                'status' => 'completed',
                'mpesa_receipt_number' => $mpesaReceiptNumber ?? ('QGH' . rand(1000000, 9999999)),
                'result_code' => (string) $resultCode,
                'result_desc' => $resultDesc,
                'raw_callback_payload' => $payload,
            ]);

            // Update Obituary Status: payment_confirmed -> pending_verification
            $obituary = $payment->obituary;
            if ($obituary) {
                $obituary->update([
                    'status' => 'pending_verification',
                    'verification_status' => 'pending',
                ]);
            }

            return true;
        } else {
            $payment->update([
                'status' => 'failed',
                'result_code' => (string) $resultCode,
                'result_desc' => $resultDesc,
                'raw_callback_payload' => $payload,
            ]);

            return false;
        }
    }

    /**
     * Simulate Mock Payment Completion (For local testing without live callback)
     */
    public function simulateMockCompletion(Payment $payment): bool
    {
        $payment->update([
            'status' => 'completed',
            'mpesa_receipt_number' => 'QGH' . rand(1000000, 9999999),
            'result_code' => '0',
            'result_desc' => 'The service request is processed successfully. (Mock)',
        ]);

        if ($payment->obituary) {
            $payment->obituary->update([
                'status' => 'pending_verification',
                'verification_status' => 'pending',
            ]);
        }

        return true;
    }
}
