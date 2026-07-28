<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Obituary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorRoleRestrictionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_cannot_access_advanced_admin_menu_items()
    {
        $editor = Admin::create([
            'name' => 'Editor User',
            'email' => 'editor@obituaries.co.ke',
            'password' => bcrypt('password123'),
            'role' => 'editor',
        ]);

        $restrictedRoutes = [
            'admin.contributors.index',
            'admin.security-logs.index',
            'admin.fraud.index',
            'admin.analytics.index',
            'admin.payments.index',
            'admin.users.index',
            'admin.settings.index',
        ];

        foreach ($restrictedRoutes as $routeName) {
            $response = $this->actingAs($editor, 'admin')->get(route($routeName));
            $response->assertStatus(403);
        }
    }

    public function test_editor_cannot_post_free_notice_without_mpesa_code()
    {
        $editor = Admin::create([
            'name' => 'Editor User',
            'email' => 'editor2@obituaries.co.ke',
            'password' => bcrypt('password123'),
            'role' => 'editor',
        ]);

        $response = $this->actingAs($editor, 'admin')->post(route('admin.obituaries.store'), [
            'full_name' => 'Editor Free Test Notice',
            'date_of_death' => '2026-06-01',
            'county' => 'Kiambu',
            'town' => 'Thika',
            'biography' => 'Biography content for editor test.',
            'submitter_name' => 'Submitter Name',
            'submitter_phone' => '0711223344',
            'relationship' => 'Friend',
            'status' => 'published',
            'mpesa_transaction_code' => '', // Empty code!
        ]);

        $response->assertSessionHasErrors(['mpesa_transaction_code']);
        $this->assertDatabaseMissing('obituaries', ['full_name' => 'Editor Free Test Notice']);
    }

    public function test_super_admin_can_post_free_notice_and_access_all_routes()
    {
        $superAdmin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'super@obituaries.co.ke',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($superAdmin, 'admin')->get(route('admin.contributors.index'));
        $response->assertStatus(200);

        $createResponse = $this->actingAs($superAdmin, 'admin')->post(route('admin.obituaries.store'), [
            'full_name' => 'Super Admin Free Notice',
            'date_of_death' => '2026-06-01',
            'county' => 'Kiambu',
            'town' => 'Thika',
            'biography' => 'Biography content for super admin test.',
            'submitter_name' => 'Submitter Name',
            'submitter_phone' => '0711223344',
            'relationship' => 'Friend',
            'status' => 'published',
        ]);

        $this->assertDatabaseHas('obituaries', ['full_name' => 'Super Admin Free Notice']);
    }
}
