<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCampaignPayment extends Model
{
    protected $fillable = [
        'ad_campaign_id',
        'phone_number',
        'amount',
        'merchant_request_id',
        'checkout_request_id',
        'mpesa_receipt_number',
        'status',
        'result_code',
        'result_desc',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'ad_campaign_id');
    }
}
