<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class SpamProtectionService
{
    /**
     * Verify Cloudflare Turnstile token via API.
     */
    public static function verifyTurnstile(?string $token, ?string $ip = null): bool
    {
        $secretKey = config('services.turnstile.secret_key') ?: env('TURNSTILE_SECRET_KEY');

        // Skip verification if Turnstile is unconfigured or in automated test environment
        if (empty($secretKey) || app()->environment('testing')) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $ip ?: request()->ip(),
            ]);

            return (bool) $response->json('success');
        } catch (\Throwable $e) {
            Log::warning("Turnstile API verification failed: " . $e->getMessage());
            return true; // Graceful fallback on network glitch
        }
    }

    /**
     * Check if email domain belongs to a known disposable email provider.
     */
    public static function isDisposableEmail(string $email): bool
    {
        $domain = strtolower(substr(strrchr($email, "@"), 1));
        if (empty($domain)) {
            return false;
        }

        $disposableDomains = [
            'tempmail.com', 'temp-mail.org', 'tempmail.net', 'mailinator.com', 'guerrillamail.com',
            'guerrillamail.info', 'guerrillamail.biz', 'guerrillamail.de', 'guerrillamail.net',
            'guerrillamail.org', 'grr.la', 'guerrillamailblock.com', '10minutemail.com',
            '10minutemail.net', 'trashmail.com', 'trashmail.me', 'trashmail.net', 'yopmail.com',
            'yopmail.fr', 'yopmail.net', 'dispostable.com', 'sharklasers.com', 'getnada.com',
            'maildrop.cc', 'boun.cr', 'crazymailing.com', 'fakeinbox.com', 'throwawaymail.com',
            'inboxkitten.com', 'temp-mail.ru', 'disposablemail.com', 'burnermail.io',
            'pokemail.net', 'spam4.me', 'nada.ltd', 'getairmail.com', 'mohmal.com',
            'emailondeck.com', 'minuteinbox.com', 'mytemp.email', 'tempinbox.com',
            'mailcatch.com', 'generator.email', 'disposable.com', 'tempmailaddress.com'
        ];

        return in_array($domain, $disposableDomains, true);
    }

    /**
     * Validate whether a phone number matches Kenyan format.
     */
    public static function isKenyanPhone(string $phone): bool
    {
        $clean = preg_replace('/[\s\-\(\)]+/', '', trim($phone));
        return (bool) preg_match('/^(?:\+?254|0)?([71]\d{8})$/', $clean);
    }

    /**
     * Detect gibberish, keyboard mash, or random character spam strings.
     */
    public static function isGibberish(string $text): bool
    {
        $text = trim($text);
        if (strlen($text) < 3) {
            return false;
        }

        // 1. Single word exceeding 35 continuous characters without space
        $words = explode(' ', $text);
        foreach ($words as $w) {
            if (mb_strlen($w) > 35) {
                return true;
            }
        }

        // 2. Character repetition (5+ consecutive identical characters)
        if (preg_match('/(.)\1{4,}/i', $text)) {
            return true;
        }

        // 3. Keyboard walking sequences (6+ consecutive keys)
        $keyboardPatterns = [
            'qwertyuiop', 'asdfghjkl', 'zxcvbnm',
            'poiuytrewq', 'lkjhgfdsa', 'mnbvcxz',
            '123456789', '987654321'
        ];
        $cleanText = strtolower(preg_replace('/[^a-z0-9]/', '', $text));
        if (strlen($cleanText) >= 6) {
            foreach ($keyboardPatterns as $pattern) {
                for ($i = 0; $i <= strlen($pattern) - 6; $i++) {
                    $sub = substr($pattern, $i, 6);
                    if (str_contains($cleanText, $sub)) {
                        return true;
                    }
                }
            }
        }

        // 4. Consonant-to-vowel ratio & 6+ consecutive consonants
        $lettersOnly = preg_replace('/[^a-zA-Z]/', '', $text);
        if (strlen($lettersOnly) >= 12) {
            preg_match_all('/[aeiouAEIOU]/', $lettersOnly, $vowels);
            $vowelCount = count($vowels[0]);
            $vowelRatio = $vowelCount / strlen($lettersOnly);

            if ($vowelRatio < 0.10 || $vowelRatio > 0.85) {
                return true;
            }

            if (preg_match('/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]{6,}/', $lettersOnly)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify that form was not submitted faster than minimum required seconds.
     */
    public static function verifyTimeLock(?string $encryptedTime, int $minSeconds = 3): bool
    {
        if (empty($encryptedTime)) {
            return app()->environment('testing');
        }

        try {
            $timestamp = null;
            if (is_numeric($encryptedTime)) {
                $timestamp = (int) $encryptedTime;
            } else {
                try {
                    $timestamp = (int) Crypt::decryptString($encryptedTime);
                } catch (\Throwable $e) {
                    return app()->environment('testing');
                }
            }

            $elapsed = time() - $timestamp;

            return $elapsed >= $minSeconds && $elapsed <= 14400;
        } catch (\Throwable $e) {
            return app()->environment('testing');
        }
    }

    /**
     * Check if honeypot hidden field was filled.
     */
    public static function checkHoneypot($honeypotValue): bool
    {
        return empty($honeypotValue);
    }
}
