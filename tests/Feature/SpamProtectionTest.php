<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\BlockedIp;
use App\Models\Obituary;
use App\Models\ObituaryReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpamProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    private function createObituary(array $overrides = []): Obituary
    {
        return Obituary::create(array_merge([
            'slug' => 'test-obituary-' . uniqid(),
            'full_name' => 'Mzee John Kiarie',
            'date_of_birth' => '1950-01-01',
            'date_of_death' => '2026-07-20',
            'county' => 'Nairobi',
            'town' => 'Kilimani',
            'biography' => 'Test biography content for obituary notice.',
            'submitter_name' => 'Jane Submitter',
            'submitter_phone' => '0712345678',
            'relationship' => 'Child',
            'status' => 'published',
            'verification_status' => 'verified',
        ], $overrides));
    }

    public function test_valid_report_submission_succeeds_and_logs_ip_and_user_agent()
    {
        $obituary = $this->createObituary();

        $response = $this->withHeaders(['User-Agent' => 'Mozilla/5.0 TestBrowser'])
            ->post(route('obituaries.report', $obituary->id), [
                'reporter_name' => 'David Ochieng',
                'reporter_email' => 'david@gmail.com',
                'reporter_phone' => '0712345678',
                'reason' => 'inaccurate_info',
                'details' => 'The date of birth listed on the notice has a typo.',
                'website_hp' => '',
                '_form_time' => (string)(time() - 10),
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('obituary_reports', [
            'obituary_id' => $obituary->id,
            'reporter_name' => 'David Ochieng',
            'reporter_email' => 'david@gmail.com',
            'reporter_phone' => '0712345678',
            'reason' => 'inaccurate_info',
            'user_agent' => 'Mozilla/5.0 TestBrowser',
        ]);
    }

    public function test_honeypot_field_filled_silently_ignores_submission()
    {
        $obituary = $this->createObituary();

        $response = $this->post(route('obituaries.report', $obituary->id), [
            'reporter_name' => 'Spam Bot',
            'reporter_email' => 'spambot@example.com',
            'reporter_phone' => '0712345678',
            'reason' => 'inaccurate_info',
            'details' => 'Buy cheap products now',
            'website_hp' => 'http://spam-link.com',
            '_form_time' => (string)(time() - 10),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('obituary_reports', [
            'reporter_name' => 'Spam Bot',
        ]);
    }

    public function test_submission_under_three_seconds_is_rejected()
    {
        $obituary = $this->createObituary();

        $response = $this->post(route('obituaries.report', $obituary->id), [
            'reporter_name' => 'Fast Bot',
            'reporter_email' => 'fast@gmail.com',
            'reporter_phone' => '0712345678',
            'reason' => 'inaccurate_info',
            'details' => 'Fast speed submission',
            'website_hp' => '',
            '_form_time' => (string)time(), // 0 seconds elapsed
        ]);

        $response->assertSessionHasErrors(['details']);
        $this->assertDatabaseMissing('obituary_reports', [
            'reporter_name' => 'Fast Bot',
        ]);
    }

    public function test_disposable_email_is_rejected()
    {
        $obituary = $this->createObituary();

        $response = $this->post(route('obituaries.report', $obituary->id), [
            'reporter_name' => 'Jane Wanjiku',
            'reporter_email' => 'spammer@mailinator.com',
            'reporter_phone' => '0712345678',
            'reason' => 'inaccurate_info',
            'details' => 'Legitimate message text here',
            'website_hp' => '',
            '_form_time' => (string)(time() - 5),
        ]);

        $response->assertSessionHasErrors(['reporter_email']);
    }

    public function test_non_kenyan_phone_is_rejected()
    {
        $obituary = $this->createObituary();

        $response = $this->post(route('obituaries.report', $obituary->id), [
            'reporter_name' => 'John Smith',
            'reporter_email' => 'john@gmail.com',
            'reporter_phone' => '+15551234567', // US phone
            'reason' => 'inaccurate_info',
            'details' => 'Test details description',
            'website_hp' => '',
            '_form_time' => (string)(time() - 5),
        ]);

        $response->assertSessionHasErrors(['reporter_phone']);
    }

    public function test_gibberish_random_character_details_are_rejected()
    {
        $obituary = $this->createObituary();

        $response = $this->post(route('obituaries.report', $obituary->id), [
            'reporter_name' => 'Test User',
            'reporter_email' => 'test@gmail.com',
            'reporter_phone' => '0712345678',
            'reason' => 'inaccurate_info',
            'details' => 'asdfghjklqwertyuiopzxcvbnmkkkkkkk',
            'website_hp' => '',
            '_form_time' => (string)(time() - 5),
        ]);

        $response->assertSessionHasErrors(['details']);
    }

    public function test_admin_marking_report_as_spam_automatically_blocks_ip()
    {
        $admin = Admin::create([
            'name' => 'Spam Admin',
            'email' => 'spam_admin@obituaries.co.ke',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
        ]);

        $report = ObituaryReport::create([
            'obituary_id' => $this->createObituary()->id,
            'reporter_name' => 'Spammer',
            'reporter_email' => 'spamm@tempmail.com',
            'reporter_phone' => '0712345678',
            'reason' => 'other',
            'details' => 'Spam content',
            'status' => 'pending',
            'ip_address' => '197.232.40.50',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.reports.resolve', $report->id), [
                'status' => 'spam',
                'resolution_notes' => 'Spam report flagged',
            ]);

        $response->assertSessionHas('success');
        $this->assertEquals('spam', $report->fresh()->status);
        $this->assertTrue(BlockedIp::isBlocked('197.232.40.50'));
    }

    public function test_blocked_ip_cannot_submit_reports()
    {
        $obituary = $this->createObituary();
        BlockedIp::create([
            'ip_address' => '197.232.99.99',
            'reason' => 'Offender IP blocked',
        ]);

        $response = $this->post(route('obituaries.report', $obituary->id), [
            'reporter_name' => 'Blocked Spammer',
            'reporter_email' => 'user@gmail.com',
            'reporter_phone' => '0712345678',
            'reason' => 'inaccurate_info',
            'details' => 'Valid text details here',
            'website_hp' => '',
            '_form_time' => (string)(time() - 10),
        ], ['REMOTE_ADDR' => '197.232.99.99']);

        $response->assertStatus(403);
    }
}
