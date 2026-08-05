<?php

namespace App\Services;

use App\Models\AdCampaign;
use App\Models\AdPlacement;

class AdServingService
{
    /**
     * Retrieve an active running ad campaign for a specific placement slot and location context.
     * Guaranteed no duplicate campaigns rendered on the same page via $excludeCampaignIds.
     */
    public static function getAdForPlacement(
        string $placementSlug,
        ?string $targetCounty = null,
        array $excludeCampaignIds = []
    ): ?AdCampaign {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('ad_placements') || !\Illuminate\Support\Facades\Schema::hasTable('ad_campaigns')) {
                return null;
            }
        } catch (\Throwable $e) {
            return null;
        }

        $placement = AdPlacement::where('slug', $placementSlug)
            ->where('status', true)
            ->first();

        if (!$placement) {
            return null;
        }

        $today = now()->format('Y-m-d');

        // Query active running campaigns for this placement
        $baseQuery = AdCampaign::with(['businessProfile', 'bannerSize', 'placement'])
            ->where('ad_placement_id', $placement->id)
            ->where('status', 'running')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today);

        if (!empty($excludeCampaignIds)) {
            $baseQuery->whereNotIn('id', $excludeCampaignIds);
        }

        $allEligible = $baseQuery->get();

        if ($allEligible->isEmpty()) {
            return null;
        }

        // Priority 1: Match Target County (e.g. Obituary County or Landing Page County)
        if (!empty($targetCounty)) {
            $countyMatches = $allEligible->filter(function (AdCampaign $campaign) use ($targetCounty) {
                if ($campaign->is_national) {
                    return false;
                }
                return $campaign->counties->pluck('county')->contains(function ($c) use ($targetCounty) {
                    return strcasecmp($c, $targetCounty) === 0;
                });
            });

            if ($countyMatches->isNotEmpty()) {
                return static::selectWeightedCampaign($countyMatches);
            }
        }

        // Priority 2: Match National Campaigns
        $nationalMatches = $allEligible->filter(fn (AdCampaign $c) => $c->is_national);
        if ($nationalMatches->isNotEmpty()) {
            return static::selectWeightedCampaign($nationalMatches);
        }

        // Priority 3: Fallback to any remaining active campaign for this placement
        return static::selectWeightedCampaign($allEligible);
    }

    /**
     * Weighted selection algorithm (Featured campaigns get 3x higher probability).
     */
    protected static function selectWeightedCampaign($campaigns): ?AdCampaign
    {
        if ($campaigns->isEmpty()) {
            return null;
        }

        if ($campaigns->count() === 1) {
            return $campaigns->first();
        }

        $weightedPool = [];
        foreach ($campaigns as $campaign) {
            // Featured campaigns get weight 3, normal weight 1
            $weight = $campaign->is_featured ? 3 : 1;
            for ($i = 0; $i < $weight; $i++) {
                $weightedPool[] = $campaign;
            }
        }

        return $weightedPool[array_rand($weightedPool)];
    }
}
