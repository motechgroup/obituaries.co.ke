<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public static function configure(): void
    {
        // Fix Homebrew / macOS / cPanel OpenSSL missing cert.pem stream issue
        $caFile = ini_get('openssl.cafile');
        if ($caFile && !file_exists($caFile)) {
            @ini_set('openssl.cafile', '');
        }

        $host = Setting::get('mail_host', config('mail.mailers.smtp.host', 'mail.obituaries.co.ke'));
        $port = (int) Setting::get('mail_port', config('mail.mailers.smtp.port', 465));
        $username = Setting::get('mail_username', config('mail.mailers.smtp.username'));
        $password = Setting::get('mail_password', config('mail.mailers.smtp.password'));
        $encryption = Setting::get('mail_encryption', 'ssl');
        $fromAddress = Setting::get('mail_from_address', config('mail.from.address', 'hello@obituaries.co.ke'));
        $fromName = Setting::get('mail_from_name', config('mail.from.name', 'Obituaries.co.ke'));

        $isSsl = ($port === 465 || strtolower((string)$encryption) === 'ssl');

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', $port);
        Config::set('mail.mailers.smtp.username', $username);
        Config::set('mail.mailers.smtp.password', $password);
        Config::set('mail.mailers.smtp.scheme', $isSsl ? 'smtps' : null);
        Config::set('mail.mailers.smtp.encryption', $isSsl ? 'ssl' : 'tls');
        Config::set('mail.mailers.smtp.stream', [
            'ssl' => [
                'allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
                'cafile' => null,
                'capath' => null,
            ],
        ]);
        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', $fromName);

        // Purge cached mailer instance to force rebuild of transport
        Mail::purge('smtp');
    }

    public static function sendHtmlEmail(string $toEmail, string $subject, string $bodyContent, ?string $actionUrl = null, ?string $actionText = null): void
    {
        self::configure();

        Mail::send('emails.branded', [
            'subject' => $subject,
            'bodyContent' => $bodyContent,
            'actionUrl' => $actionUrl,
            'actionText' => $actionText,
        ], function ($msg) use ($toEmail, $subject) {
            $msg->to($toEmail)->subject($subject);
        });
    }
}
