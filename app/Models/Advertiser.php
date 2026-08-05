<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Advertiser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'business_name',
        'contact_person',
        'phone_number',
        'email',
        'password',
        'email_verified_at',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profiles()
    {
        return $this->hasMany(BusinessProfile::class);
    }

    public function primaryProfile()
    {
        return $this->hasOne(BusinessProfile::class)->oldestOfMany();
    }

    public function businessProfile()
    {
        return $this->hasOne(BusinessProfile::class);
    }

    public function campaigns()
    {
        return $this->hasMany(AdCampaign::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
