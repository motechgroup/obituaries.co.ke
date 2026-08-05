<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCampaignCounty extends Model
{
    protected $fillable = [
        'ad_campaign_id',
        'county',
    ];

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'ad_campaign_id');
    }
}
