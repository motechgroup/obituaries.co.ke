<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdPricing extends Model
{
    protected $fillable = [
        'ad_placement_id',
        'banner_size_id',
        'daily_rate',
        'national_daily_rate',
        'featured_sur_charge',
        'status',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'national_daily_rate' => 'decimal:2',
        'featured_sur_charge' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function placement()
    {
        return $this->belongsTo(AdPlacement::class, 'ad_placement_id');
    }

    public function bannerSize()
    {
        return $this->belongsTo(BannerSize::class, 'banner_size_id');
    }
}
