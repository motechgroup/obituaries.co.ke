<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    protected $fillable = [
        'advertiser_id',
        'business_category_id',
        'business_name',
        'logo',
        'phone',
        'whatsapp',
        'email',
        'website',
        'google_maps_link',
        'county',
        'address',
        'description',
        'status',
    ];

    public function advertiser()
    {
        return $this->belongsTo(Advertiser::class);
    }

    public function category()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    public function campaigns()
    {
        return $this->hasMany(AdCampaign::class);
    }
}
