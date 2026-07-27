<?php

namespace App\Http\Controllers;

use App\Models\Obituary;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $latestObituaries = Obituary::published()
            ->latest('id')
            ->take(8)
            ->get();

        $todayAnniversaries = Obituary::todayAnniversaries()
            ->latest('date_of_death')
            ->take(6)
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

        return view('home', compact('latestObituaries', 'todayAnniversaries', 'totalCount', 'counties'));
    }
}
