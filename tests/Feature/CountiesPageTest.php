<?php

namespace Tests\Feature;

use App\Models\Obituary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountiesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_counties_index_page_displays_all_counties()
    {
        Obituary::create([
            'slug' => 'kiambu-deceased-test',
            'full_name' => 'Kiambu Deceased Person',
            'date_of_death' => '2026-06-01',
            'county' => 'Kiambu',
            'town' => 'Ruiru',
            'biography' => 'Biography content for test.',
            'submitter_name' => 'John Submitter',
            'submitter_phone' => '0722000000',
            'relationship' => 'Son',
            'status' => 'published',
        ]);

        $response = $this->get(route('obituaries.counties'));

        $response->assertStatus(200);
        $response->assertSee('Kenyan Counties Obituary Registry');
        $response->assertSee('Kiambu');
        $response->assertSee('Nairobi');
        $response->assertSee('Mombasa');
    }
}
