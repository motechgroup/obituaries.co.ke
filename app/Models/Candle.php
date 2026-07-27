<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candle extends Model
{
    protected $fillable = [
        'obituary_id',
        'name',
        'message',
        'ip_address',
    ];

    public function obituary()
    {
        return $this->belongsTo(Obituary::class);
    }
}
