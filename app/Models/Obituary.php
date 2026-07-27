<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Obituary extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'full_name',
        'photo',
        'gallery_images',
        'date_of_birth',
        'date_of_death',
        'county',
        'town',
        'biography',
        'funeral_date',
        'burial_location',
        'church_service_location',
        'programme_file',
        'submitter_name',
        'submitter_phone',
        'submitter_email',
        'relationship',
        'family_permission_confirmed',
        'status',
        'verification_status',
        'verification_notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_death' => 'date',
        'funeral_date' => 'date',
        'verified_at' => 'datetime',
        'family_permission_confirmed' => 'boolean',
        'gallery_images' => 'array',
    ];

    public static function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-" . Str::lower(Str::random(4));
            $count++;
            if ($count > 10) {
                $slug = "{$baseSlug}-" . time();
                break;
            }
        }

        return $slug;
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function verifier()
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }

    public function candles()
    {
        return $this->hasMany(Candle::class);
    }

    public function reports()
    {
        return $this->hasMany(ObituaryReport::class);
    }

    public function getAgeAttribute(): ?int
    {
        if ($this->date_of_birth && $this->date_of_death) {
            return $this->date_of_birth->diffInYears($this->date_of_death);
        }
        return null;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePendingVerification($query)
    {
        return $query->where('status', 'pending_verification');
    }
}
