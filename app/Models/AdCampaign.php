<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    protected $fillable = [
        'advertiser_id',
        'business_profile_id',
        'ad_placement_id',
        'banner_size_id',
        'business_category_id',
        'name',
        'banner_path',
        'banner_webp_path',
        'thumbnail_path',
        'landing_url',
        'start_date',
        'end_date',
        'total_days',
        'is_national',
        'is_featured',
        'calculated_price',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_national' => 'boolean',
        'is_featured' => 'boolean',
        'approved_at' => 'datetime',
        'calculated_price' => 'decimal:2',
    ];

    public function advertiser()
    {
        return $this->belongsTo(Advertiser::class);
    }

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function placement()
    {
        return $this->belongsTo(AdPlacement::class, 'ad_placement_id');
    }

    public function bannerSize()
    {
        return $this->belongsTo(BannerSize::class, 'banner_size_id');
    }

    public function category()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    public function counties()
    {
        return $this->hasMany(AdCampaignCounty::class);
    }

    public function payments()
    {
        return $this->hasMany(AdCampaignPayment::class);
    }

    public function latestCompletedPayment()
    {
        return $this->hasOne(AdCampaignPayment::class)->where('status', 'completed')->latestOfMany();
    }

    public function impressions()
    {
        return $this->hasMany(AdImpression::class);
    }

    public function clicks()
    {
        return $this->hasMany(AdClick::class);
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function scopeRunning($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('status', 'running')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    public function getCtrAttribute(): float
    {
        $impressions = $this->impressions_count ?? $this->impressions()->count();
        $clicks = $this->clicks_count ?? $this->clicks()->count();

        if ($impressions === 0) {
            return 0.00;
        }

        return round(($clicks / $impressions) * 100, 2);
    }

    public function getBannerUrlAttribute(): string
    {
        $path = $this->banner_webp_path ?: $this->banner_path;
        if ($path) {
            \App\Helpers\StorageHelper::ensurePublicCopy($path);
        }
        if ($this->banner_path) {
            \App\Helpers\StorageHelper::ensurePublicCopy($this->banner_path);
        }
        return asset('storage/' . ($path ?: $this->banner_path));
    }
}
