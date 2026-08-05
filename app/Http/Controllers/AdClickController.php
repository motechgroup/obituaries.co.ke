<?php

namespace App\Http\Controllers;

use App\Models\AdCampaign;
use App\Services\AdTrackingService;
use Illuminate\Http\Request;

class AdClickController extends Controller
{
    public function redirect(Request $request, AdCampaign $campaign)
    {
        // Record click details
        AdTrackingService::recordClick($campaign, null, $request);

        if (empty($campaign->landing_url)) {
            return back();
        }

        $url = $campaign->landing_url;
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = 'https://' . $url;
        }

        return redirect()->away($url);
    }
}
