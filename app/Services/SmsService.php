<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public static function send(string $phone, string $message): bool
    {
        $provider = Setting::get('sms_provider', 'textsms');
        $apiKey = Setting::get('sms_api_key', '');
        $partnerId = Setting::get('sms_partner_id', '');
        $senderId = Setting::get('sms_sender_id', Setting::get('sms_shortcode', 'OBITUARIES'));

        // Format phone number to 254...
        $formattedPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($formattedPhone, '0')) {
            $formattedPhone = '254' . substr($formattedPhone, 1);
        } elseif (str_starts_with($formattedPhone, '7') || str_starts_with($formattedPhone, '1')) {
            $formattedPhone = '254' . $formattedPhone;
        }

        if (empty($apiKey) && $provider !== 'generic') {
            Log::warning("SMS Gateway ({$provider}) not sent: Missing API Key.");
            return false;
        }

        try {
            if ($provider === 'textsms') {
                // TextSMS Kenya (https://textsms.co.ke/api/services/sendsms/)
                $response = Http::acceptJson()->post('https://textsms.co.ke/api/services/sendsms/', [
                    'apikey' => $apiKey,
                    'partnerID' => $partnerId,
                    'message' => $message,
                    'shortcode' => $senderId,
                    'mobile' => $formattedPhone,
                ]);

                Log::info("TextSMS response for {$formattedPhone}: " . $response->body());
                return $response->successful();

            } elseif ($provider === 'africastalking') {
                // Africa's Talking API
                $username = Setting::get('sms_shortcode', 'sandbox');
                $response = Http::withHeaders([
                    'apiKey' => $apiKey,
                    'Accept' => 'application/json',
                ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                    'username' => $username,
                    'to' => '+' . $formattedPhone,
                    'message' => $message,
                    'from' => $senderId,
                ]);

                return $response->successful();

            } elseif ($provider === 'mobitech') {
                // Mobitech SMS API
                $response = Http::post('https://api.mobitechtechnologies.com/sms/sendsms', [
                    'api_key' => $apiKey,
                    'username' => $partnerId,
                    'sender_id' => $senderId,
                    'message' => $message,
                    'phone' => $formattedPhone,
                ]);

                return $response->successful();
            } else {
                Log::info("Generic SMS dispatch for {$formattedPhone}: {$message}");
                return true;
            }
        } catch (\Throwable $e) {
            Log::error("SMS dispatch error ({$provider}) for {$formattedPhone}: " . $e->getMessage());
            return false;
        }
    }
}
