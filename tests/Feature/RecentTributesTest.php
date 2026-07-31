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
        // 1. Notice posted yesterday (1 day ago)
        $yesterdayNotice = Obituary::create([
            'slug' => 'yesterday-notice-test',
            'full_name' => 'Yesterday Notice Deceased',
            'date_of_death' => '2026-06-01',
            'county' => 'Nairobi',
            'town' => 'Westlands',
            'biography' => 'Biography for yesterday notice test.',
            'submitter_name' => 'John Submitter',
            'submitter_phone' => '0722000000',
            'relationship' => 'Son',
            'status' => 'published',
        ]);
        $yesterdayNotice->created_at = now()->subDay()->startOfDay()->addHours(2);
        $yesterdayNotice->save(['timestamps' => false]);

        // 2. Notice posted 2 hours ago (< 1 day old / today)
        $todayNotice = Obituary::create([
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

        $todayDirectoryObituaries = $response->viewData('todayDirectoryObituaries');
        $todayObituaries = $response->viewData('todayObituaries');

        $this->assertTrue($todayDirectoryObituaries->contains($yesterdayNotice));
        $this->assertTrue($todayObituaries->contains($todayNotice));
    }
}
