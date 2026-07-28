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
            'reason' => 'inaccurate_info',
            'details' => 'The date of birth listed is incorrect.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('obituary_reports', [
            'obituary_id' => $obituary->id,
            'reporter_name' => 'Peter Ochieng',
            'reporter_email' => 'peter@example.com',
            'reason' => 'inaccurate_info',
        ]);
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
}
