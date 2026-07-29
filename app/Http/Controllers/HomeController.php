<?php

namespace App\Http\Controllers;

use App\Models\Obituary;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Obituaries Directory (Dark Top Section): Show latest obituaries posted TODAY
        $todayDirectoryObituaries = Obituary::published()
            ->where('created_at', '>=', now()->startOfDay())
            ->latest('id')
            ->take(8)
            ->get();

        // Fallback: If no obituaries posted today yet, show latest published obituaries
        if ($todayDirectoryObituaries->isEmpty()) {
            $todayDirectoryObituaries = Obituary::published()
                ->latest('id')
                ->take(8)
                ->get();
        }

        // Recent Tributes Section: Show obituaries posted on PREVIOUS days (created_at < start of today)
        $latestObituaries = Obituary::published()
            ->where('created_at', '<', now()->startOfDay())
            ->latest('id')
            ->take(8)
            ->get();

        // Fallback: If no previous day posts exist, show latest published obituaries
        if ($latestObituaries->isEmpty()) {
            $latestObituaries = Obituary::published()
                ->latest('id')
                ->take(8)
                ->get();
        }

        // Today's Anniversaries: Strictly notices with date_of_death matching today's month & day, and year < current year
        $todayAnniversaries = Obituary::todayAnniversaries()
            ->latest('date_of_death')
            ->take(6)
            ->get();

        $todayCandlesObituaries = Obituary::published()
            ->withCount('candles')
            ->orderByDesc('candles_count')
            ->latest('id')
            ->take(4)
            ->get();

        $totalCount = Obituary::published()->count();

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

        return view('home', compact('todayDirectoryObituaries', 'latestObituaries', 'todayAnniversaries', 'todayCandlesObituaries', 'totalCount', 'counties', 'dailyQuote'));
    }
}
