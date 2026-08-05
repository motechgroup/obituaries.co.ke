<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdClick extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ad_campaign_id',
        'ad_placement_id',
        'ip_address',
        'county',
        'country',
        'browser',
        'os',
        'device_type',
        'referer',
        'page_url',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'ad_campaign_id');
    }

    public function placement()
    {
        return $this->belongsTo(AdPlacement::class, 'ad_placement_id');
    }
}
