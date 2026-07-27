<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'obituary_id',
        'phone_number',
        'amount',
        'merchant_request_id',
        'checkout_request_id',
        'mpesa_receipt_number',
        'status',
        'result_code',
        'result_desc',
        'raw_callback_payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'raw_callback_payload' => 'array',
    ];

    public function obituary()
    {
        return $this->belongsTo(Obituary::class);
    }
}
