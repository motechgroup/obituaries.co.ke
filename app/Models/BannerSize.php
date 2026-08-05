<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerSize extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'width',
        'height',
        'type',
        'max_size_kb',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function getDimensionsAttribute(): string
    {
        return "{$this->width} × {$this->height} px";
    }

    public function placements()
    {
        return $this->belongsToMany(AdPlacement::class, 'ad_placement_banner_size');
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
