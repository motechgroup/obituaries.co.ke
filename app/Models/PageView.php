<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageView extends Model
{
    use HasFactory;

    public $timestamps = false; // Uses created_at only

    protected $fillable = [
        'url',
        'route_name',
        'obituary_id',
        'ip_address',
        'user_agent',
        'device_type',
        'referer',
        'referer_host',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function obituary()
    {
        return $this->belongsTo(Obituary::class);
    }
}
