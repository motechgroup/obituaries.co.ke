<?php

namespace App\Services;

use App\Models\AdCampaign;
use App\Models\AdClick;
use App\Models\AdImpression;
use App\Models\AdPlacement;
use Illuminate\Http\Request;

class AdTrackingService
{
    /**
     * Record genuine impression with deduplication window (5 minutes per IP per campaign).
     */
    public static function recordImpression(
        AdCampaign $campaign,
        ?AdPlacement $placement = null,
        ?Request $request = null
    ): bool {
        $request = $request ?? request();
        $ip = $request->ip();

        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('ad_impressions')) {
                return false;
            }

            // 1. Impression Deduplication Window Check (5 mins)
            $recentImpressionExists = AdImpression::where('ad_campaign_id', $campaign->id)
                ->where('ip_address', $ip)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->exists();

            if ($recentImpressionExists) {
                return false;
            }

            // Detect user agent & device info
            $agent = $request->userAgent() ?? '';
            $deviceType = static::detectDeviceType($agent);
            $browser = static::detectBrowser($agent);
            $os = static::detectOs($agent);
            $county = $request->get('county') ?? $campaign->counties->first()?->county ?? 'Nairobi';

            AdImpression::create([
                'ad_campaign_id' => $campaign->id,
                'ad_placement_id' => $placement?->id ?? $campaign->ad_placement_id,
                'ip_address' => $ip,
                'county' => $county,
                'country' => 'Kenya',
                'browser' => $browser,
                'os' => $os,
                'device_type' => $deviceType,
                'referer' => substr($request->header('referer', ''), 0, 500),
                'page_url' => substr($request->fullUrl(), 0, 500),
                'created_at' => now(),
            ]);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Record genuine click with rate-limiting & anti-click-farming protection.
     */
    public static function recordClick(
        AdCampaign $campaign,
        ?AdPlacement $placement = null,
        ?Request $request = null
    ): bool {
        $request = $request ?? request();
        $ip = $request->ip();

        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('ad_clicks')) {
                return false;
            }

            // 1. Rate-limiting anti-spam check (10 seconds window)
            $recentClickExists = AdClick::where('ad_campaign_id', $campaign->id)
                ->where('ip_address', $ip)
                ->where('created_at', '>=', now()->subSeconds(10))
                ->exists();

            if ($recentClickExists) {
                return false;
            }

            $agent = $request->userAgent() ?? '';
            $deviceType = static::detectDeviceType($agent);
            $browser = static::detectBrowser($agent);
            $os = static::detectOs($agent);
            $county = $request->get('county') ?? $campaign->counties->first()?->county ?? 'Nairobi';

            AdClick::create([
                'ad_campaign_id' => $campaign->id,
                'ad_placement_id' => $placement?->id ?? $campaign->ad_placement_id,
                'ip_address' => $ip,
                'county' => $county,
                'country' => 'Kenya',
                'browser' => $browser,
                'os' => $os,
                'device_type' => $deviceType,
                'referer' => substr($request->header('referer', ''), 0, 500),
                'page_url' => substr($request->fullUrl(), 0, 500),
                'created_at' => now(),
            ]);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected static function detectDeviceType(string $agent): string
    {
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $agent)) {
            return 'Tablet';
        }
        if (preg_match('/(mobile|iphone|ipod|blackberry|android|opera mini|windows phone)/i', $agent)) {
            return 'Mobile';
        }
        return 'Desktop';
    }

    protected static function detectBrowser(string $agent): string
    {
        if (str_contains($agent, 'Chrome')) return 'Chrome';
        if (str_contains($agent, 'Safari')) return 'Safari';
        if (str_contains($agent, 'Firefox')) return 'Firefox';
        if (str_contains($agent, 'Edge')) return 'Edge';
        if (str_contains($agent, 'MSIE') || str_contains($agent, 'Trident')) return 'Internet Explorer';
        return 'Other';
    }

    protected static function detectOs(string $agent): string
    {
        if (str_contains($agent, 'Android')) return 'Android';
        if (str_contains($agent, 'iPhone') || str_contains($agent, 'iPad')) return 'iOS';
        if (str_contains($agent, 'Windows')) return 'Windows';
        if (str_contains($agent, 'Macintosh') || str_contains($agent, 'Mac OS')) return 'macOS';
        if (str_contains($agent, 'Linux')) return 'Linux';
        return 'Unknown OS';
    }
}
