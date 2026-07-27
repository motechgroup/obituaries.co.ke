<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSettings = [
            'site_title' => 'Obituaries.co.ke',
            'site_tagline' => 'A dignified space for remembrance, honouring loved ones across Kenya.',
            'footer_address' => 'Nairobi, Kenya',
            'footer_phone' => '+254 700 000 000',
            'footer_email' => 'support@obituaries.co.ke',
            'copyright_text' => '© ' . date('Y') . ' Obituaries.co.ke. All rights reserved.',
            'obituary_publishing_cost' => '2000',

            // SMTP Mail Credentials for obituaries.co.ke
            'mail_host' => 'mail.obituaries.co.ke',
            'mail_port' => '465',
            'mail_username' => 'hello@obituaries.co.ke',
            'mail_password' => 'QdXyQe@h!C8SrGEW',
            'mail_encryption' => 'ssl',
            'mail_from_address' => 'hello@obituaries.co.ke',
            'mail_from_name' => 'Obituaries.co.ke',

            // Mail Templates
            'mail_template_verification' => "Dear {NAME},\n\nYour obituary notice for {DECEASED_NAME} has been verified and published live on Obituaries.co.ke.\n\nView Live: {LINK}\n\nWarm regards,\nObituaries.co.ke Team",
            'mail_template_rejection' => "Dear {NAME},\n\nRegrettably, your obituary submission for {DECEASED_NAME} could not be approved due to the following reason:\n\nReason: {REASON}\n\nPlease contact our editorial team if you have questions.\n\nWarm regards,\nObituaries.co.ke Editorial Team",
            'mail_template_anniversary' => "Dear {NAME},\n\nToday marks the {YEARS} Anniversary of the passing of {DECEASED_NAME}.\n\nIn honoring their cherished legacy, friends and family are remembering them today on Obituaries.co.ke.\n\nView Memorial: {LINK}\n\nWarm regards,\nObituaries.co.ke Team",

            // SMS Configuration
            'sms_provider' => 'textsms',
            'sms_shortcode' => 'OBITUARIES',
            'sms_sender_id' => 'OBITUARIES',
            'sms_template_submission' => "Dear {NAME}, your obituary submission for {DECEASED_NAME} has been received. Complete payment to publish.",
            'sms_template_approval' => "Dear {NAME}, the obituary for {DECEASED_NAME} is now published live: {LINK}",
            'sms_template_rejection' => "Dear {NAME}, your obituary submission for {DECEASED_NAME} was not approved. Reason: {REASON}",
            'sms_template_anniversary' => "Dear {NAME}, today marks the {YEARS} Anniversary of {DECEASED_NAME}'s passing. We join you in memory: {LINK}",
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
