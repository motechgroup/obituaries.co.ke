<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Obituary;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObituaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Remembering Lives');
    }

    public function test_can_submit_obituary()
    {
        $payload = [
            'full_name' => 'Mzee Samuel Mwangi',
            'date_of_birth' => '1955-04-10',
            'date_of_death' => '2026-07-20',
            'county' => 'Nyeri',
            'town' => 'Karatina',
            'biography' => 'Mzee Samuel Mwangi lived a full and courageous life devoted to his community and family.',
            'funeral_date' => '2026-08-02',
            'church_service_location' => 'PCEA Karatina',
            'burial_location' => 'Family Farm Karatina',
            'submitter_name' => 'John Mwangi',
            'submitter_phone' => '0712345678',
            'submitter_email' => 'john@example.com',
            'relationship' => 'Child',
            'family_permission_confirmed' => '1',
        ];

        $response = $this->post('/submit', $payload);

        $this->assertDatabaseHas('obituaries', [
            'full_name' => 'Mzee Samuel Mwangi',
            'status' => 'pending_payment',
            'verification_status' => 'unverified',
        ]);

        $obituary = Obituary::where('full_name', 'Mzee Samuel Mwangi')->first();
        $response->assertRedirect(route('payments.checkout', $obituary->id));
    }

    public function test_admin_can_verify_and_publish_obituary()
    {
        $admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@obituaries.co.ke',
            'password' => bcrypt('password123'),
        ]);

        $obituary = Obituary::create([
            'slug' => 'mzee-samuel-mwangi',
            'full_name' => 'Mzee Samuel Mwangi',
            'date_of_birth' => '1955-04-10',
            'date_of_death' => '2026-07-20',
            'county' => 'Nyeri',
            'town' => 'Karatina',
            'biography' => 'Test biography content.',
            'submitter_name' => 'John Mwangi',
            'submitter_phone' => '0712345678',
            'relationship' => 'Child',
            'status' => 'pending_verification',
            'verification_status' => 'pending',
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.obituaries.verify', $obituary->id), [
                'action' => 'approve',
                'verification_notes' => 'Verified via phone call.',
            ]);

        $obituary->refresh();
        $this->assertEquals('published', $obituary->status);
        $this->assertEquals('verified', $obituary->verification_status);
        $this->assertEquals('Verified via phone call.', $obituary->verification_notes);

        // Verify published obituary shows up on public page
        $publicResponse = $this->get('/obituary/' . $obituary->slug);
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee('Mzee Samuel Mwangi');
    }

    public function test_search_returns_published_obituaries()
    {
        Obituary::create([
            'slug' => 'published-john-doe',
            'full_name' => 'Published John Doe',
            'date_of_birth' => '1950-01-01',
            'date_of_death' => '2026-05-01',
            'county' => 'Nairobi',
            'town' => 'Westlands',
            'biography' => 'Bio for John Doe.',
            'submitter_name' => 'Jane Doe',
            'submitter_phone' => '0700000000',
            'relationship' => 'Spouse',
            'status' => 'published',
            'verification_status' => 'verified',
        ]);

        $response = $this->get('/search?name=John&county=Nairobi');
        $response->assertStatus(200);
        $response->assertSee('Published John Doe');
    }

    public function test_user_can_submit_obituary_report()
    {
        $obituary = Obituary::create([
            'slug' => 'published-john-doe-report-test',
            'full_name' => 'Published John Doe Report Test',
            'date_of_birth' => '1950-01-01',
            'date_of_death' => '2026-05-01',
            'county' => 'Nairobi',
            'town' => 'Westlands',
            'biography' => 'Bio for John Doe.',
            'submitter_name' => 'Jane Doe',
            'submitter_phone' => '0700000000',
            'relationship' => 'Spouse',
            'status' => 'published',
            'verification_status' => 'verified',
        ]);

        $response = $this->post(route('obituaries.report', $obituary->id), [
            'reporter_name' => 'Peter Ochieng',
            'reporter_email' => 'peter@example.com',
            'reporter_phone' => '0712345678',
            'reason' => 'inaccurate_info',
            'details' => 'The date of birth listed is incorrect.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('obituary_reports', [
            'obituary_id' => $obituary->id,
            'reporter_name' => 'Peter Ochieng',
            'reporter_email' => 'peter@example.com',
            'reporter_phone' => '0712345678',
            'reason' => 'inaccurate_info',
        ]);
    }

    public function test_report_requires_reporter_email_and_phone()
    {
        $obituary = Obituary::create([
            'slug' => 'report-req-test',
            'full_name' => 'Report Req Test',
            'date_of_birth' => '1950-01-01',
            'date_of_death' => '2026-05-01',
            'county' => 'Nairobi',
            'town' => 'Westlands',
            'biography' => 'Bio for Report Req Test.',
            'submitter_name' => 'Jane Doe',
            'submitter_phone' => '0700000000',
            'relationship' => 'Spouse',
            'status' => 'published',
            'verification_status' => 'verified',
        ]);

        $response = $this->post(route('obituaries.report', $obituary->id), [
            'reporter_name' => 'Peter Ochieng',
            // Missing email and phone
            'reason' => 'inaccurate_info',
            'details' => 'The date of birth listed is incorrect.',
        ]);

        $response->assertSessionHasErrors(['reporter_email', 'reporter_phone']);
    }

    public function test_auto_publish_and_hide_poster_details_settings()
    {
        // 1. Verify poster details are hidden when show_poster_details = 0
        \App\Models\Setting::set('show_poster_details', '0');

        $obituary = Obituary::create([
            'slug' => 'poster-test-mzee',
            'full_name' => 'Poster Test Mzee',
            'date_of_birth' => '1950-01-01',
            'date_of_death' => '2026-05-01',
            'county' => 'Nairobi',
            'town' => 'Westlands',
            'biography' => 'Bio for Poster Test Mzee.',
            'submitter_name' => 'Secret Submitter Name',
            'submitter_phone' => '0700000000',
            'relationship' => 'Spouse',
            'status' => 'published',
            'verification_status' => 'verified',
        ]);

        $response = $this->get('/obituary/' . $obituary->slug);
        $response->assertStatus(200);
        $response->assertDontSee('Submitted with love by Secret Submitter Name');

        // 2. Enable show_poster_details = 1
        \App\Models\Setting::set('show_poster_details', '1');
        $response2 = $this->get('/obituary/' . $obituary->slug);
        $response2->assertSee('Submitted with love by Secret Submitter Name');
    }

    public function test_admin_can_view_finance_reports_and_export_csv()
    {
        $admin = Admin::create([
            'name' => 'Finance Admin',
            'email' => 'finance@obituaries.co.ke',
            'password' => bcrypt('password123'),
        ]);

        $obituary = Obituary::create([
            'slug' => 'finance-test-mzee',
            'full_name' => 'Finance Test Mzee',
            'date_of_birth' => '1950-01-01',
            'date_of_death' => '2026-05-01',
            'county' => 'Nairobi',
            'town' => 'Westlands',
            'biography' => 'Bio for Finance Test Mzee.',
            'submitter_name' => 'Jane Submitter',
            'submitter_phone' => '0700000000',
            'relationship' => 'Spouse',
            'status' => 'published',
            'verification_status' => 'verified',
        ]);

        Payment::create([
            'obituary_id' => $obituary->id,
            'phone_number' => '254712345678',
            'amount' => 500.00,
            'mpesa_receipt_number' => 'QGH1234567',
            'checkout_request_id' => 'ws_CO_12345',
            'status' => 'completed',
        ]);

        // Access Finance Reports Dashboard
        $response = $this->actingAs($admin, 'admin')->get('/admin/payments');
        $response->assertStatus(200);
        $response->assertSee('Finance Reports, Analytics & Audit Log', false);
        $response->assertSee('KES 500.00');

        // Export CSV
        $exportResponse = $this->actingAs($admin, 'admin')->get('/admin/payments/export');
        $exportResponse->assertStatus(200);
        $exportResponse->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        ob_start();
        $exportResponse->sendContent();
        $csvContent = ob_get_clean();
        $this->assertStringContainsString('QGH1234567', $csvContent);
    }

    public function test_admin_can_purge_database_mock_data()
    {
        $admin = Admin::create([
            'name' => 'Purge Admin',
            'email' => 'purge@obituaries.co.ke',
            'password' => bcrypt('password123'),
        ]);

        $obituary = Obituary::create([
            'slug' => 'mock-to-be-purged',
            'full_name' => 'Mock To Be Purged',
            'date_of_birth' => '1950-01-01',
            'date_of_death' => '2026-05-01',
            'county' => 'Nairobi',
            'town' => 'Westlands',
            'biography' => 'Bio content.',
            'submitter_name' => 'Jane Submitter',
            'submitter_phone' => '0700000000',
            'relationship' => 'Spouse',
            'status' => 'published',
            'verification_status' => 'verified',
        ]);

        Payment::create([
            'obituary_id' => $obituary->id,
            'phone_number' => '254712345678',
            'amount' => 500.00,
            'mpesa_receipt_number' => 'QGH9999999',
            'checkout_request_id' => 'ws_CO_99999',
            'status' => 'completed',
        ]);

        // Rejection when confirmation text is incorrect
        $failResponse = $this->actingAs($admin, 'admin')->post(route('admin.database.purge'), [
            'target' => 'all',
            'confirm_text' => 'WRONG_TEXT',
        ]);
        $failResponse->assertSessionHas('error');
        $this->assertDatabaseHas('obituaries', ['slug' => 'mock-to-be-purged']);

        // Successful Purge when confirmation text is PURGE
        $successResponse = $this->actingAs($admin, 'admin')->post(route('admin.database.purge'), [
            'target' => 'all',
            'confirm_text' => 'PURGE',
        ]);
        $successResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('obituaries', ['slug' => 'mock-to-be-purged']);
        $this->assertDatabaseMissing('payments', ['mpesa_receipt_number' => 'QGH9999999']);
        // Ensure Admin user remains intact!
        $this->assertDatabaseHas('admins', ['email' => 'purge@obituaries.co.ke']);
    }

    public function test_admin_can_create_and_publish_obituary_without_payment()
    {
        $admin = Admin::create([
            'name' => 'Creator Admin',
            'email' => 'creator@obituaries.co.ke',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.obituaries.create'));
        $response->assertStatus(200);
        $response->assertSee('Create New Obituary Notice');

        $storeResponse = $this->actingAs($admin, 'admin')->post(route('admin.obituaries.store'), [
            'full_name' => 'Mzee Peter Karanja',
            'date_of_birth' => '1945-05-10',
            'date_of_death' => '2026-06-20',
            'county' => 'Kiambu',
            'town' => 'Ruiru',
            'biography' => 'Beloved father and grandfather.',
            'submitter_name' => 'Admin Editorial',
            'submitter_phone' => '0711000000',
            'relationship' => 'Editorial Team',
            'status' => 'published',
        ]);

        $this->assertDatabaseHas('obituaries', [
            'full_name' => 'Mzee Peter Karanja',
            'county' => 'Kiambu',
            'status' => 'published',
            'verification_status' => 'verified',
            'verified_by' => $admin->id,
        ]);

        $obituary = Obituary::where('full_name', 'Mzee Peter Karanja')->first();
        $storeResponse->assertRedirect(route('admin.obituaries.show', $obituary->id));
    }

    public function test_admin_can_view_and_update_profile_and_password()
    {
        $admin = Admin::create([
            'name' => 'Original Profile Name',
            'email' => 'profile@obituaries.co.ke',
            'password' => bcrypt('oldpassword123'),
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('Manage Admin Profile');
        $response->assertSee('Original Profile Name');

        $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.profile.update'), [
            'name' => 'Updated Profile Name',
            'email' => 'updated_profile@obituaries.co.ke',
            'phone' => '0799887766',
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('admins', [
            'id' => $admin->id,
            'name' => 'Updated Profile Name',
            'email' => 'updated_profile@obituaries.co.ke',
            'phone' => '0799887766',
        ]);

        $admin->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $admin->password));
    }

    public function test_editor_cannot_access_super_admin_routes()
    {
        $editor = Admin::create([
            'name' => 'Editor User',
            'email' => 'editor@obituaries.co.ke',
            'password' => bcrypt('password123'),
            'role' => 'editor',
        ]);

        // Editor attempts to access Super Admin routes -> 403 Forbidden
        $this->actingAs($editor, 'admin')->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($editor, 'admin')->get(route('admin.payments.index'))->assertStatus(403);
        $this->actingAs($editor, 'admin')->get(route('admin.settings.index'))->assertStatus(403);
        $this->actingAs($editor, 'admin')->post(route('admin.database.purge'), ['confirm_text' => 'PURGE'])->assertStatus(403);

        // Editor CAN access Obituaries, Reports, Profile, and Dashboard
        $this->actingAs($editor, 'admin')->get(route('admin.obituaries.index'))->assertStatus(200);
        $this->actingAs($editor, 'admin')->get(route('admin.reports.index'))->assertStatus(200);
        $this->actingAs($editor, 'admin')->get(route('admin.profile.edit'))->assertStatus(200);
        
        $dashboardResponse = $this->actingAs($editor, 'admin')->get(route('admin.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertDontSee('Total Revenue');
    }

    public function test_admin_and_editor_can_unpublish_obituary()
    {
        $editor = Admin::create([
            'name' => 'Moderator Editor',
            'email' => 'moderator@obituaries.co.ke',
            'password' => bcrypt('password123'),
            'role' => 'editor',
        ]);

        $obituary = Obituary::create([
            'slug' => 'to-be-unpublished',
            'full_name' => 'Unpublish Test Mzee',
            'date_of_birth' => '1950-01-01',
            'date_of_death' => '2026-05-01',
            'county' => 'Nairobi',
            'town' => 'Westlands',
            'biography' => 'Bio to be unpublished.',
            'submitter_name' => 'Submitter Name',
            'submitter_phone' => '0700000000',
            'relationship' => 'Friend',
            'status' => 'published',
            'verification_status' => 'verified',
        ]);

        $unpublishResponse = $this->actingAs($editor, 'admin')->post(route('admin.obituaries.unpublish', $obituary->id), [
            'reason' => 'Content flagged for review.',
        ]);

        $unpublishResponse->assertSessionHas('success');
        $this->assertDatabaseHas('obituaries', [
            'id' => $obituary->id,
            'status' => 'draft',
            'verification_status' => 'pending',
        ]);
    }

    public function test_obituary_can_be_created_without_date_of_birth_and_generates_seo_keywords()
    {
        $obituary = Obituary::create([
            'slug' => 'optional-dob-test',
            'full_name' => 'Mzee Daniel Kipruto',
            'date_of_birth' => null,
            'date_of_death' => '2026-06-15',
            'county' => 'Uasin Gishu',
            'town' => 'Eldoret',
            'biography' => 'Elder Mzee Daniel Kipruto was a beloved father and grandfather in Eldoret.',
            'submitter_name' => 'Kipruto Family',
            'submitter_phone' => '0711223344',
            'relationship' => 'Child',
            'status' => 'published',
        ]);

        $this->assertNull($obituary->date_of_birth);
        $this->assertNotEmpty($obituary->seo_keywords);
        $this->assertStringContainsString('Mzee Daniel Kipruto obituary', $obituary->seo_keywords);
        $this->assertStringContainsString('Uasin Gishu', $obituary->seo_keywords);
        $this->assertStringContainsString('Eldoret', $obituary->seo_keywords);
        $this->assertStringContainsString('father', $obituary->seo_keywords);
        $this->assertStringContainsString('Mzee Daniel Kipruto Obituary & Death Notice | Obituaries.co.ke', $obituary->meta_title);
    }
}
