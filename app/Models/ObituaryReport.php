<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObituaryReport extends Model
{
    protected $fillable = [
        'obituary_id',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'reason',
        'details',
        'status',
        'resolved_by',
        'resolution_notes',
        'ip_address',
        'user_agent',
    ];

    public function obituary()
    {
        return $this->belongsTo(Obituary::class);
    }

    public function resolver()
    {
        return $this->belongsTo(Admin::class, 'resolved_by');
    }
}
