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
        'meta_title',
        'meta_description',
        'seo_keywords',
        'canonical_url',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_death' => 'date',
        'funeral_date' => 'date',
        'verified_at' => 'datetime',
        'family_permission_confirmed' => 'boolean',
        'gallery_images' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (Obituary $obituary) {
            if (empty($obituary->seo_keywords)) {
                $obituary->seo_keywords = $obituary->generateSeoKeywords();
            }
            if (empty($obituary->meta_title)) {
                $cleanName = strip_tags($obituary->full_name);
                $obituary->meta_title = Str::limit("{$cleanName} Obituary & Death Notice | Obituaries.co.ke", 250, '');
            }
            if (empty($obituary->meta_description)) {
                $cleanName = strip_tags($obituary->full_name);
                $cleanTown = strip_tags($obituary->town);
                $cleanCounty = strip_tags($obituary->county);
                $deathDate = $obituary->date_of_death ? $obituary->date_of_death->format('M j, Y') : '';
                $obituary->meta_description = Str::limit("In loving memory of {$cleanName} from {$cleanTown}, {$cleanCounty} Kenya. Passed away on {$deathDate}. Read biography, funeral service details, and leave tribute messages.", 480, '');
            }
        });
    }

    public function generateSeoKeywords(): string
    {
        $keywords = [];

        $name = trim(strip_tags($this->full_name));
        $keywords[] = "{$name} obituary";
        $keywords[] = "{$name} death notice";
        $keywords[] = "{$name} funeral";
        $keywords[] = "{$name} Kenya";

        if (!empty($this->county)) {
            $cleanCounty = strip_tags($this->county);
            $keywords[] = "{$name} {$cleanCounty}";
            $keywords[] = "obituary {$cleanCounty} county";
        }

        if (!empty($this->town)) {
            $cleanTown = strip_tags($this->town);
            $keywords[] = "{$name} {$cleanTown}";
        }

        if (!empty($this->burial_location)) {
            $cleanBurial = strip_tags($this->burial_location);
            $keywords[] = "{$cleanBurial} funeral";
        }

        if (!empty($this->church_service_location)) {
            $cleanChurch = strip_tags($this->church_service_location);
            $keywords[] = "{$cleanChurch}";
        }

        $keywords[] = "Kenya obituaries";
        $keywords[] = "Kenyan death announcements";
        $keywords[] = "online memorial Kenya";

        if (!empty($this->biography)) {
            $bioText = strtolower(strip_tags($this->biography));
            $familyTerms = ['father', 'mother', 'husband', 'wife', 'son', 'daughter', 'brother', 'sister', 'grandfather', 'grandmother', 'mzee', 'mama', 'elder', 'dr', 'prof', 'hon', 'engineer', 'eng'];
            foreach ($familyTerms as $term) {
                if (str_contains($bioText, $term)) {
                    $keywords[] = "{$name} {$term}";
                }
            }
        }

        return implode(', ', array_unique($keywords));
    }

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

    public function getAnniversaryYearsAttribute(): ?int
    {
        if (!$this->date_of_death) {
            return null;
        }
        $today = now();
        if ($this->date_of_death->month === $today->month && $this->date_of_death->day === $today->day && $this->date_of_death->year < $today->year) {
            return $this->date_of_death->diffInYears($today);
        }
        return null;
    }

    public function getIsAnniversaryTodayAttribute(): bool
    {
        return $this->anniversary_years !== null && $this->anniversary_years > 0;
    }

    public function getAnniversaryBadgeTextAttribute(): ?string
    {
        if (!$this->is_anniversary_today || !$this->anniversary_years) {
            return null;
        }

        $years = $this->anniversary_years;
        $ends = ['th','st','nd','rd','th','th','th','th','th','th'];
        if ((($years % 100) >= 11) && (($years % 100) <= 13)) {
            $abbreviation = $years . 'th';
        } else {
            $abbreviation = $years . $ends[$years % 10];
        }

        return "🌹 {$abbreviation} Anniversary";
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeTodayAnniversaries($query)
    {
        $today = now();
        return $query->where('status', 'published')
            ->whereMonth('date_of_death', $today->month)
            ->whereDay('date_of_death', $today->day)
            ->whereYear('date_of_death', '<', $today->year);
    }

    public function scopePendingVerification($query)
    {
        return $query->where('status', 'pending_verification');
    }
}
