<?php

namespace App\Console\Commands;

use App\Models\Obituary;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAnniversaryRemindersCommand extends Command
{
    protected $signature = 'app:send-anniversary-reminders';
    protected $description = 'Send automatic annual anniversary SMS and Email reminders to family submitters';

    public function handle()
    {
        $this->info('Checking today\'s obituary anniversaries...');

        $anniversaries = Obituary::todayAnniversaries()->get();

        if ($anniversaries->isEmpty()) {
            $this->info('No obituary anniversaries found for today.');
            return Command::SUCCESS;
        }

        $emailCount = 0;
        $smsCount = 0;

        foreach ($anniversaries as $obituary) {
            $years = $obituary->anniversary_years;
            if (!$years) {
                continue;
            }

            $badgeText = $obituary->anniversary_badge_text;
            $link = route('obituaries.show', $obituary->slug);

            // Send Anniversary Email
            if ($obituary->submitter_email) {
                try {
                    $tmpl = Setting::get('mail_template_anniversary', "Dear {NAME},\n\nToday marks the {YEARS} Anniversary of the passing of {DECEASED_NAME}.\n\nIn honoring their cherished legacy, friends and family are remembering them today on Obituaries.co.ke.\n\nView Memorial: {LINK}\n\nWarm regards,\nObituaries.co.ke Team");
                    $body = str_replace(
                        ['{NAME}', '{DECEASED_NAME}', '{YEARS}', '{LINK}'],
                        [$obituary->submitter_name, $obituary->full_name, "{$years}" . $this->getOrdinalSuffix($years), $link],
                        $tmpl
                    );

                    Mail::raw($body, function ($msg) use ($obituary, $years) {
                        $msg->to($obituary->submitter_email)
                            ->subject("In Loving Memory: {$years}" . $this->getOrdinalSuffix($years) . " Anniversary of {$obituary->full_name}");
                    });

                    $emailCount++;
                } catch (\Throwable $e) {
                    Log::error("Failed sending anniversary email for Obituary #{$obituary->id}: " . $e->getMessage());
                }
            }

            // Send Anniversary SMS
            if ($obituary->submitter_phone) {
                try {
                    $tmpl = Setting::get('sms_template_anniversary', "Dear {NAME}, today marks the {YEARS} Anniversary of {DECEASED_NAME}'s passing. We join you in memory: {LINK}");
                    $smsBody = str_replace(
                        ['{NAME}', '{DECEASED_NAME}', '{YEARS}', '{LINK}'],
                        [$obituary->submitter_name, $obituary->full_name, "{$years}" . $this->getOrdinalSuffix($years), $link],
                        $tmpl
                    );

                    Log::info("Anniversary SMS sent to {$obituary->submitter_phone}: {$smsBody}");
                    $smsCount++;
                } catch (\Throwable $e) {
                    Log::error("Failed sending anniversary SMS for Obituary #{$obituary->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("Completed! Dispatched {$emailCount} anniversary emails and {$smsCount} SMS notifications.");
        return Command::SUCCESS;
    }

    private function getOrdinalSuffix(int $number): string
    {
        $ends = ['th','st','nd','rd','th','th','th','th','th','th'];
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return 'th';
        }
        return $ends[$number % 10];
    }
}
