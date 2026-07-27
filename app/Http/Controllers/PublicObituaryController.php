<?php

namespace App\Http\Controllers;

use App\Models\Obituary;
use Illuminate\Http\Request;

class PublicObituaryController extends Controller
{
    public function show($slug)
    {
        $obituary = Obituary::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('obituaries.show', compact('obituary'));
    }

    public function search(Request $request)
    {
        $query = Obituary::published();

        if ($name = $request->input('name')) {
            $query->where('full_name', 'like', "%{$name}%");
        }

        if ($county = $request->input('county')) {
            $query->where('county', $county);
        }

        if ($year = $request->input('year')) {
            $query->whereYear('date_of_death', $year);
        }

        $obituaries = $query->latest('date_of_death')->paginate(12)->withQueryString();

        $counties = [
            'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo Marakwet', 'Embu', 'Garissa', 'Homa Bay',
            'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga', 'Kisii',
            'Kisumu', 'Kitui', 'Kwale', 'Laikipia', 'Lamu', 'Machakos', 'Makueni', 'Mandera',
            'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Murang\'a', 'Nairobi', 'Nakuru', 'Nandi',
            'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 'Siaya', 'Taita Taveta', 'Tana River',
            'Tharaka Nithi', 'Trans Nzoia', 'Turkana', 'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot'
        ];

        return view('obituaries.search', compact('obituaries', 'counties'));
    }

    public function sitemap()
    {
        $obituaries = Obituary::published()->select('slug', 'updated_at')->get();

        $content = view('obituaries.sitemap', compact('obituaries'));

        return response($content, 200)->header('Content-Type', 'text/xml');
    }
}
