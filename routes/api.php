<?php

use App\Http\Controllers\PaymentController;
use App\Models\Obituary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// M-Pesa Callback Endpoint
Route::post('/v1/mpesa/callback', [PaymentController::class, 'handleCallback'])->name('api.mpesa.callback');

// Rate-Limited Public REST API Endpoints (60 requests / minute)
Route::middleware('throttle:60,1')->prefix('v1')->group(function () {

    // Get Published Obituaries List
    Route::get('/obituaries', function (Request $request) {
        $county = $request->query('county');
        $search = $request->query('q');

        $query = Obituary::published();

        if ($county) {
            $query->where('county', $county);
        }

        if ($search) {
            $query->where('full_name', 'like', "%{$search}%");
        }

        $obituaries = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $obituaries,
        ]);
    });

    // Get Single Obituary Detail
    Route::get('/obituaries/{slug}', function ($slug) {
        $obituary = Obituary::published()->where('slug', $slug)->first();

        if (!$obituary) {
            return response()->json([
                'status' => 'error',
                'message' => 'Obituary notice not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $obituary,
        ]);
    });

    // Public Stats Endpoint
    Route::get('/stats', function () {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_obituaries' => Obituary::published()->count(),
                'today_anniversaries' => Obituary::todayAnniversaries()->count(),
                'counties_covered' => Obituary::published()->distinct('county')->count('county'),
            ]
        ]);
    });
});
