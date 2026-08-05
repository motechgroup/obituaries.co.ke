<?php

namespace App\Services;

use App\Models\AdPlacement;
use App\Models\AdPricing;
use App\Models\BannerSize;
use Carbon\Carbon;

class AdPricingEngine
{
    /**
     * Calculate dynamic campaign total price based on placement, size, dates, national coverage, and featured flags.
     */
    public static function calculatePrice(
        int $adPlacementId,
        int $bannerSizeId,
        string $startDate,
        string $endDate,
        array $selectedCounties = [],
        bool $isNational = false,
        bool $isFeatured = false
    ): array {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $totalDays = max(1, $start->diffInDays($end) + 1);

        $pricing = AdPricing::where('ad_placement_id', $adPlacementId)
            ->where('banner_size_id', $bannerSizeId)
            ->where('status', true)
            ->first();

        // Fallback default pricing if specific rule not defined
        $dailyRate = $pricing ? (float) $pricing->daily_rate : 500.00;
        $nationalDailyRate = $pricing ? (float) $pricing->national_daily_rate : 1200.00;
        $featuredSurcharge = $pricing ? (float) $pricing->featured_sur_charge : 300.00;

        if ($isNational) {
            $baseDailyRate = $nationalDailyRate;
        } else {
            $countyCount = max(1, count($selectedCounties));
            // Multi-county pricing: 1st county at full daily rate, additional counties at 50% discount
            if ($countyCount === 1) {
                $baseDailyRate = $dailyRate;
            } else {
                $baseDailyRate = $dailyRate + (($countyCount - 1) * ($dailyRate * 0.5));
            }
        }

        $subtotal = $baseDailyRate * $totalDays;
        $featuredFee = $isFeatured ? ($featuredSurcharge * $totalDays) : 0.00;
        $totalPrice = round($subtotal + $featuredFee, 2);

        return [
            'total_days' => $totalDays,
            'daily_rate' => round($baseDailyRate, 2),
            'subtotal' => round($subtotal, 2),
            'featured_fee' => round($featuredFee, 2),
            'total_price' => $totalPrice,
            'currency' => 'KES',
        ];
    }
}
