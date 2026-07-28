<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\BlockedIp;
use App\Models\FraudAlert;
use App\Models\Obituary;
use App\Models\SecurityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndFraudTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocked_ip_is_denied_access()
    {
        BlockedIp::create([
            'ip_address' => '197.232.0.100',
            'reason' => 'Automated Bot Attack',
        ]);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '197.232.0.100'])
            ->get(route('home'));

        $response->assertStatus(403);
        $response->assertSee('Access Denied');
    }

    public function test_fraud_detection_flags_suspicious_submission_patterns()
    {
        $admin = Admin::create([
            'name' => 'Security Admin',
            'email' => 'sec_admin@obituaries.co.ke',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
        ]);

        // Submit obituary containing spam trigger keyword "bitcoin loan offer"
        $response = $this->post(route('obituaries.store'), [
            'full_name' => 'Spam Trigger Test',
            'date_of_death' => '2026-06-01',
            'county' => 'Nairobi',
            'town' => 'Kilimani',
            'biography' => 'This obituary contains bitcoin loan offer scam trigger words.',
            'submitter_name' => 'Spammer Identity',
            'submitter_phone' => '0799887766',
            'relationship' => 'Friend',
            'family_permission_confirmed' => '1',
        ]);

        $obituary = Obituary::where('full_name', 'Spam Trigger Test')->first();
        $this->assertNotNull($obituary);
        $this->assertTrue((bool)$obituary->is_flagged_fraud);

        $this->assertDatabaseHas('fraud_alerts', [
            'obituary_id' => $obituary->id,
        ]);

        // Admin views Fraud Center
        $fraudView = $this->actingAs($admin, 'admin')->get(route('admin.fraud.index'));
        $fraudView->assertStatus(200);
        $fraudView->assertSee('Spam Trigger Test');
    }

    public function test_contributors_module_lists_submitters_and_exports_csv()
    {
        $admin = Admin::create([
            'name' => 'Report Admin',
            'email' => 'report_admin@obituaries.co.ke',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
        ]);

        Obituary::create([
            'slug' => 'john-submitter-test',
            'full_name' => 'Mzee Contributor Test',
            'date_of_death' => '2026-05-01',
            'county' => 'Nyeri',
            'town' => 'Karatina',
            'biography' => 'Biography for contributor test.',
            'submitter_name' => 'John Submitter',
            'submitter_phone' => '0722112233',
            'submitter_email' => 'john@submitter.co.ke',
            'relationship' => 'Child',
            'status' => 'published',
        ]);

        // View Contributors Module
        $response = $this->actingAs($admin, 'admin')->get(route('admin.contributors.index'));
        $response->assertStatus(200);
        $response->assertSee('John Submitter');
        $response->assertSee('0722112233');

        // Export Contributors CSV
        $export = $this->actingAs($admin, 'admin')->get(route('admin.contributors.export'));
        $export->assertStatus(200);
        $export->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_security_logs_module_records_events()
    {
        $admin = Admin::create([
            'name' => 'Log Admin',
            'email' => 'log_admin@obituaries.co.ke',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
        ]);

        SecurityLog::log('test_event', 'info', null, 'Test log entry details');

        $response = $this->actingAs($admin, 'admin')->get(route('admin.security-logs.index'));
        $response->assertStatus(200);
        $response->assertSee('Test log entry details');
    }
}
