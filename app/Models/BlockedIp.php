<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlockedIp extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'reason',
        'blocked_by',
    ];

    public function blocker()
    {
        return $this->belongsTo(Admin::class, 'blocked_by');
    }

    public static function isBlocked(?string $ip = null): bool
    {
        $ip = $ip ?: request()->ip();
        if (empty($ip)) return false;

        return self::where('ip_address', $ip)->exists();
    }
}
