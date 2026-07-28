<?php

namespace Tests\Feature;

use App\Models\Obituary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecentTributesTest extends TestCase
{
    use RefreshDatabase;

    public function test_today_notices_shows_notices_posted_today_and_recent_notices_shows_notices_at_least_two_days_old()
    {
        // 1. Notice posted 3 days ago (at least 2 days old)
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
        $oldNotice->created_at = now()->subDays(3);
        $oldNotice->save(['timestamps' => false]);

        // 2. Notice posted 2 hours ago (posted today)
        $todayNotice = Obituary::create([
            'slug' => 'today-notice-test',
            'full_name' => 'Today Notice Deceased',
            'date_of_death' => '2026-07-28',
            'county' => 'Nairobi',
            'town' => 'Kilimani',
            'biography' => 'Biography for today notice test.',
            'submitter_name' => 'Jane Submitter',
            'submitter_phone' => '0733000000',
            'relationship' => 'Daughter',
            'status' => 'published',
            'created_at' => now()->subHours(2),
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        $todayNotices = $response->viewData('todayNotices');
        $recentNotices = $response->viewData('recentNotices');

        $this->assertTrue($todayNotices->contains($todayNotice));
        $this->assertTrue($recentNotices->contains($oldNotice));
    }
}
