<?php

namespace Tests\Feature;

use App\Models\Obituary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecentTributesTest extends TestCase
{
    use RefreshDatabase;

    public function test_notices_older_than_one_day_do_not_appear_as_recent_when_newer_notices_exist()
    {
        // 1. Notice posted 2 days ago (1+ day old)
        $oldNotice = Obituary::create([
            'slug' => 'old-notice-test',
            'full_name' => 'Old Notice Deceased',
            'date_of_death' => '2026-06-01',
            'county' => 'Nairobi',
            'town' => 'Westlands',
            'biography' => 'Biography for old notice test.',
            'submitter_name' => 'John Submitter',
            'submitter_phone' => '0722000000',
            'relationship' => 'Son',
            'status' => 'published',
        ]);
        $oldNotice->created_at = now()->subDays(2);
        $oldNotice->save(['timestamps' => false]);

        // 2. Notice posted 2 hours ago (< 1 day old)
        $recentNotice = Obituary::create([
            'slug' => 'recent-notice-test',
            'full_name' => 'Recent Notice Deceased',
            'date_of_death' => '2026-07-28',
            'county' => 'Nairobi',
            'town' => 'Kilimani',
            'biography' => 'Biography for recent notice test.',
            'submitter_name' => 'Jane Submitter',
            'submitter_phone' => '0733000000',
            'relationship' => 'Daughter',
            'status' => 'published',
            'created_at' => now()->subHours(2),
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        $latestObituaries = $response->viewData('latestObituaries');
        $this->assertTrue($latestObituaries->contains($recentNotice));
        $this->assertFalse($latestObituaries->contains($oldNotice));
    }
}
