<?php

namespace App\Http\Controllers;

use App\Models\Obituary;
use App\Models\Candle;
use Illuminate\Http\Request;

class CandleController extends Controller
{
    public function store(Request $request, Obituary $obituary)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $obituary->candles()->create([
            'name' => strip_tags($validated['name'] ?: 'Anonymous'),
            'message' => isset($validated['message']) ? strip_tags($validated['message']) : null,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', '🕯️ You have lit a candle of remembrance in honor of ' . $obituary->full_name . '.');
    }
}
