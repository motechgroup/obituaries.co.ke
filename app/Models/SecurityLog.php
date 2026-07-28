<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SecurityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'user_agent',
        'device_type',
        'action',
        'severity',
        'obituary_id',
        'admin_id',
        'details',
    ];

    public function obituary()
    {
        return $this->belongsTo(Obituary::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public static function log(string $action, string $severity = 'info', ?int $obituaryId = null, ?string $details = null): ?self
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('security_logs')) {
                return null;
            }

            $request = request();
            $agent = $request->header('User-Agent', '');

            $deviceType = 'Desktop';
            if (preg_match('/(android|bb\d+|meego).+mobile|avail|blackberry|emulator|iphone|ipod|sm-t|gt-p|opera m(obi|ini)|palmsource|pocket|tablet|windows phone/i', $agent)) {
                $deviceType = preg_match('/tablet|ipad|playbook|silk/i', $agent) ? 'Tablet' : 'Mobile';
            }

            return self::create([
                'ip_address' => $request->ip() ?: '127.0.0.1',
                'user_agent' => substr($agent, 0, 500),
                'device_type' => $deviceType,
                'action' => $action,
                'severity' => $severity,
                'obituary_id' => $obituaryId,
                'admin_id' => auth('admin')->id(),
                'details' => $details,
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
