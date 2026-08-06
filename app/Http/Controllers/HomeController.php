<?php

namespace App\Http\Controllers;

use App\Models\Obituary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $idPayload = function () {
            // 1. Today's Obituaries IDs
            $todayIds = Obituary::published()
                ->where('created_at', '>=', now()->startOfDay())
                ->latest('id')
                ->take(8)
                ->pluck('id')
                ->toArray();

            if (empty($todayIds)) {
                $todayIds = Obituary::published()
                    ->latest('id')
                    ->take(8)
                    ->pluck('id')
                    ->toArray();
            }

            // 2. Directory Obituaries IDs
            $directoryIds = Obituary::published()
                ->whereBetween('created_at', [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()])
                ->latest('id')
                ->take(8)
                ->pluck('id')
                ->toArray();

            if (empty($directoryIds)) {
                $directoryIds = Obituary::published()
                    ->where('created_at', '<', now()->startOfDay())
                    ->latest('id')
                    ->take(8)
                    ->pluck('id')
                    ->toArray();
            }

            if (empty($directoryIds)) {
                $directoryIds = Obituary::published()
                    ->latest('id')
                    ->take(8)
                    ->pluck('id')
                    ->toArray();
            }

            // 3. Latest Obituaries IDs
            $latestIds = Obituary::published()
                ->where('created_at', '>=', now()->subDays(7)->startOfDay())
                ->inRandomOrder()
                ->take(8)
                ->pluck('id')
                ->toArray();

            if (count($latestIds) < 8) {
                $latestIds = Obituary::published()
                    ->inRandomOrder()
                    ->take(8)
                    ->pluck('id')
                    ->toArray();
            }

            // 4. Today's Anniversaries IDs
            $anniversaryIds = Obituary::todayAnniversaries()
                ->latest('date_of_death')
                ->take(6)
                ->pluck('id')
                ->toArray();

            // 5. Candle Obituaries IDs
            $candleIds = Obituary::published()
                ->withCount('candles')
                ->orderByDesc('candles_count')
                ->latest('id')
                ->take(4)
                ->pluck('id')
                ->toArray();

            // 6. Total Published Count
            $totalCount = Obituary::published()->count();

            // 7. Category IDs
            $noticeCategories = [
                'Death Announcement' => [
                    'title' => 'Death Announcements',
                    'subtitle' => 'Official death announcements and passing notices across Kenya.',
                    'icon' => 'campaign',
                ],
                'Anniversary' => [
                    'title' => 'Anniversaries & Remembrances',
                    'subtitle' => 'Honoring loved ones whose anniversaries of passing are remembered.',
                    'icon' => 'event_repeat',
                ],
                'Memorial' => [
                    'title' => 'Memorial Tributes',
                    'subtitle' => 'Preserving everlasting memories and tributes for departed loved ones.',
                    'icon' => 'auto_stories',
                ],
                'Life Celebration' => [
                    'title' => 'Life Celebrations',
                    'subtitle' => 'Celebrating rich lives, joyful memories, and timeless legacies.',
                    'icon' => 'celebration',
                ],
            ];

            $hasCategoryColumn = Schema::hasColumn('obituaries', 'category');
            $categoryObituaryIds = [];
            foreach (array_keys($noticeCategories) as $catKey) {
                if ($hasCategoryColumn) {
                    $categoryObituaryIds[$catKey] = Obituary::published()
                        ->where(function($q) use ($catKey) {
                            $q->where('category', $catKey);
                            if ($catKey === 'Death Announcement') {
                                $q->orWhereNull('category');
                            }
                        })
                        ->latest('id')
                        ->take(4)
                        ->pluck('id')
                        ->toArray();
                } else {
                    $categoryObituaryIds[$catKey] = ($catKey === 'Death Announcement')
                        ? Obituary::published()->latest('id')->take(4)->pluck('id')->toArray()
                        : [];
                }
            }

            return compact(
                'todayIds',
                'directoryIds',
                'latestIds',
                'anniversaryIds',
                'candleIds',
                'noticeCategories',
                'categoryObituaryIds',
                'totalCount'
            );
        };

        if (app()->environment('testing')) {
            $cachedIds = $idPayload();
        } else {
            $cachedIds = Cache::remember('homepage_id_payload_v5', 600, $idPayload);
        }

        // Hydrate Eloquent collections safely preserving model instance types
        $todayObituaries = !empty($cachedIds['todayIds'])
            ? Obituary::whereIn('id', $cachedIds['todayIds'])->get()->sortBy(fn($m) => array_search($m->id, $cachedIds['todayIds']))->values()
            : new \Illuminate\Database\Eloquent\Collection();

        $todayDirectoryObituaries = !empty($cachedIds['directoryIds'])
            ? Obituary::whereIn('id', $cachedIds['directoryIds'])->get()->sortBy(fn($m) => array_search($m->id, $cachedIds['directoryIds']))->values()
            : new \Illuminate\Database\Eloquent\Collection();

        $latestObituaries = !empty($cachedIds['latestIds'])
            ? Obituary::whereIn('id', $cachedIds['latestIds'])->get()->sortBy(fn($m) => array_search($m->id, $cachedIds['latestIds']))->values()
            : new \Illuminate\Database\Eloquent\Collection();

        $todayAnniversaries = !empty($cachedIds['anniversaryIds'])
            ? Obituary::whereIn('id', $cachedIds['anniversaryIds'])->get()->sortBy(fn($m) => array_search($m->id, $cachedIds['anniversaryIds']))->values()
            : new \Illuminate\Database\Eloquent\Collection();

        $todayCandlesObituaries = !empty($cachedIds['candleIds'])
            ? Obituary::whereIn('id', $cachedIds['candleIds'])->withCount('candles')->get()->sortBy(fn($m) => array_search($m->id, $cachedIds['candleIds']))->values()
            : new \Illuminate\Database\Eloquent\Collection();

        $categoryObituaries = [];
        foreach ($cachedIds['categoryObituaryIds'] ?? [] as $catKey => $catIds) {
            $categoryObituaries[$catKey] = !empty($catIds)
                ? Obituary::whereIn('id', $catIds)->get()->sortBy(fn($m) => array_search($m->id, $catIds))->values()
                : new \Illuminate\Database\Eloquent\Collection();
        }

        $noticeCategories = $cachedIds['noticeCategories'];
        $totalCount = $cachedIds['totalCount'];

        $counties = [
            'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo Marakwet', 'Embu', 'Garissa', 'Homa Bay',
            'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga', 'Kisii',
            'Kisumu', 'Kitui', 'Kwale', 'Laikipia', 'Lamu', 'Machakos', 'Makueni', 'Mandera',
            'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Murang\'a', 'Nairobi', 'Nakuru', 'Nandi',
            'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 'Siaya', 'Taita Taveta', 'Tana River',
            'Tharaka Nithi', 'Trans Nzoia', 'Turkana', 'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot'
        ];

        $quotes = [
            ['text' => 'The song is ended, but the melody lingers on.', 'author' => 'Irving Berlin'],
            ['text' => 'To live in hearts we leave behind is not to die.', 'author' => 'Thomas Campbell'],
            ['text' => 'There are no farewells to us. Wherever you are, you will always be in our hearts.', 'author' => 'Rumi'],
            ['text' => 'What we once enjoyed and deeply loved we can never lose, for all that we love deeply becomes a part of us.', 'author' => 'Helen Keller'],
            ['text' => 'Death is not extinguishing the light; it is only putting out the lamp because the dawn has come.', 'author' => 'Rabindranath Tagore'],
            ['text' => 'Those we love don\'t go away, they walk beside us every day. Unseen, unheard, but always near.', 'author' => 'Traditional Sympathy'],
            ['text' => 'A great soul serves everyone all the time. A great soul never dies. It brings us together again and again.', 'author' => 'Maya Angelou'],
            ['text' => 'Grief is the price we pay for love.', 'author' => 'Queen Elizabeth II'],
            ['text' => 'May the stars carry your sadness away, may the flowers fill your heart with beauty.', 'author' => 'Chief Dan George'],
            ['text' => 'While we are mourning the loss of our loved one, others are rejoicing to meet them behind the veil.', 'author' => 'John Taylor'],
            ['text' => 'Peace is seeing the sunrise or a sunset and knowing who to thank.', 'author' => 'Kenyan Proverb'],
            ['text' => 'The loss is unmeasurable, but so is the love left behind.', 'author' => 'Memorial Reflection'],
            ['text' => 'Don\'t cry because it\'s over, smile because it happened.', 'author' => 'Dr. Seuss'],
            ['text' => 'Those we hold most dear never truly leave us. They live on in the kindnesses they showed, the comfort they shared, and the love they brought.', 'author' => 'Norton Juster'],
            ['text' => 'God saw you getting tired and a cure was not to be, so He put His arms around you and whispered, "Come to Me."', 'author' => 'Anonymous'],
            ['text' => 'Like a fallen oak tree, their legacy leaves an empty space in the forest, yet their memory feeds the roots of generations to come.', 'author' => 'African Proverb'],
            ['text' => 'Unable are the loved to die, for love is immortality.', 'author' => 'Emily Dickinson'],
            ['text' => 'Say not in grief "he is no more" but in thankfulness that he was.', 'author' => 'Hebrew Proverb'],
            ['text' => 'A life that touches others goes on forever.', 'author' => 'Traditional Tribute'],
            ['text' => 'For death is no more than a turning of us over from time to eternity.', 'author' => 'William Penn'],
            ['text' => 'Love leaves a memory no one can steal.', 'author' => 'Irish Blessing'],
            ['text' => 'Precious in the sight of the LORD is the death of His faithful servants.', 'author' => 'Psalm 116:15'],
            ['text' => 'He who has gone, so we but cherish his memory, abides with us, more present, nay, more powerful than the living man.', 'author' => 'Antoine de Saint-Exupéry'],
            ['text' => 'When someone you love becomes a memory, the memory becomes a treasure.', 'author' => 'Traditional Proverb'],
            ['text' => 'Memory is a way of holding on to the things you love, the things you are, the things you never want to lose.', 'author' => 'Memorial Reflection'],
            ['text' => 'The heart that has truly loved never forgets.', 'author' => 'Thomas Moore'],
            ['text' => 'Peace I leave with you; my peace I give you. Do not let your hearts be troubled and do not be afraid.', 'author' => 'John 14:27'],
            ['text' => 'Sunset in one horizon is sunrise in another.', 'author' => 'African Wisdom'],
            ['text' => 'Though their voice is stilled, their wisdom echoes forever.', 'author' => 'Kenyan Blessing'],
            ['text' => 'In the quiet earth, their memory blooms as eternal spring.', 'author' => 'Poetic Reflection'],
        ];

        $dayIndex = (date('z') + date('Y')) % count($quotes);
        $dailyQuote = $quotes[$dayIndex];

        return view('home', compact(
            'todayObituaries',
            'todayDirectoryObituaries',
            'latestObituaries',
            'todayAnniversaries',
            'todayCandlesObituaries',
            'noticeCategories',
            'categoryObituaries',
            'totalCount',
            'counties',
            'dailyQuote'
        ));
    }

    public static function clearCache(): void
    {
        Cache::forget('homepage_payload_v4');
        Cache::forget('homepage_id_payload_v5');
    }
}
