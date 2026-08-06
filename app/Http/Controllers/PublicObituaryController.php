<?php

namespace App\Http\Controllers;

use App\Models\Obituary;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicObituaryController extends Controller
{
    public static function getCountiesList(): array
    {
        return [
            'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo Marakwet', 'Embu', 'Garissa', 'Homa Bay',
            'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga', 'Kisii',
            'Kisumu', 'Kitui', 'Kwale', 'Laikipia', 'Lamu', 'Machakos', 'Makueni', 'Mandera',
            'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Murang\'a', 'Nairobi', 'Nakuru', 'Nandi',
            'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 'Siaya', 'Taita Taveta', 'Tana River',
            'Tharaka Nithi', 'Trans Nzoia', 'Turkana', 'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot'
        ];
    }

    public function show($slug)
    {
        $obituary = Obituary::where('slug', $slug)
            ->where('status', 'published')
            ->with(['candles' => fn($q) => $q->latest()])
            ->firstOrFail();

        // Similar Obituaries in same county for internal SEO linking
        $similarObituaries = Obituary::published()
            ->where('id', '!=', $obituary->id)
            ->where('county', $obituary->county)
            ->latest('date_of_death')
            ->take(4)
            ->get();

        return view('obituaries.show', compact('obituary', 'similarObituaries'));
    }

    public function search(Request $request)
    {
        $query = Obituary::published();

        if ($request->input('filter') === 'anniversaries') {
            $query->todayAnniversaries();
        }

        if ($category = trim(strip_tags((string)$request->input('category')))) {
            $query->where('category', $category);
        }

        if ($name = trim(strip_tags((string)$request->input('name')))) {
            $query->where('full_name', 'like', "%{$name}%");
        }

        if ($county = trim(strip_tags((string)$request->input('county')))) {
            $query->where('county', $county);
        }

        if ($year = trim(strip_tags((string)$request->input('year')))) {
            $query->whereYear('date_of_death', $year);
        }

        $obituaries = $query->latest('date_of_death')->paginate(50)->withQueryString();
        $counties = static::getCountiesList();
        $categories = Obituary::CATEGORIES;

        return view('obituaries.search', compact('obituaries', 'counties', 'categories'));
    }

    public function countyIndex($countySlug)
    {
        $cleanSlug = str_replace('-obituaries', '', Str::slug($countySlug));
        $counties = static::getCountiesList();

        $matchedCounty = null;
        foreach ($counties as $c) {
            if (Str::slug($c) === $cleanSlug) {
                $matchedCounty = $c;
                break;
            }
        }

        if (!$matchedCounty) {
            abort(404);
        }

        $obituaries = Obituary::published()
            ->where('county', $matchedCounty)
            ->latest('date_of_death')
            ->paginate(12);

        $totalCount = Obituary::published()->where('county', $matchedCounty)->count();

        return view('obituaries.county', [
            'county' => $matchedCounty,
            'countySlug' => Str::slug($matchedCounty),
            'obituaries' => $obituaries,
            'totalCount' => $totalCount,
            'allCounties' => $counties,
        ]);
    }

    public function sitemap()
    {
        $obituaries = Obituary::published()->select('slug', 'updated_at')->get();
        $counties = static::getCountiesList();

        $content = view('obituaries.sitemap', compact('obituaries', 'counties'));

        return response($content, 200)->header('Content-Type', 'text/xml; charset=utf-8');
    }
}
