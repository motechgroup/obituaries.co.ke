<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdPlacement extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'page_type',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function bannerSizes()
    {
        return $this->belongsToMany(BannerSize::class, 'ad_placement_banner_size');
    }

    public function pricings()
    {
        return $this->hasMany(AdPricing::class);
    }

    public function campaigns()
    {
        return $this->hasMany(AdCampaign::class);
    }
}
