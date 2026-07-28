<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FraudAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'obituary_id',
        'risk_score',
        'risk_level',
        'threat_type',
        'description',
        'status',
    ];

    public function obituary()
    {
        return $this->belongsTo(Obituary::class);
    }
}
