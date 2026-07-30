<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }

    public static function getPricingDetails(): array
    {
        $basePrice = (float) static::get('obituary_publishing_cost', '2000');
        $discountPercent = (float) static::get('obituary_discount_percentage', '0');
        $offerEnabled = static::get('obituary_offer_enabled', '1') === '1' && $discountPercent > 0;

        if ($offerEnabled) {
            $savings = round(($basePrice * $discountPercent) / 100, 2);
            $finalPrice = max(0, round($basePrice - $savings, 2));
        } else {
            $savings = 0.0;
            $discountPercent = 0.0;
            $finalPrice = $basePrice;
        }

        return [
            'base_price' => $basePrice,
            'discount_percent' => $discountPercent,
            'savings' => $savings,
            'final_price' => $finalPrice,
            'has_offer' => $offerEnabled && $discountPercent > 0,
        ];
    }
}
