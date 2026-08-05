<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function profiles()
    {
        return $this->hasMany(BusinessProfile::class);
    }

    public function campaigns()
    {
        return $this->hasMany(AdCampaign::class);
    }
}
