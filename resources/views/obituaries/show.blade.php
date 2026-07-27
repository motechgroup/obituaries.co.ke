@extends('layouts.app')

@section('title', $obituary->meta_title ?: $obituary->full_name . ' Obituary | Death Notice & Funeral Details | Obituaries.co.ke')
@section('meta_description', $obituary->meta_description ?: 'Read the obituary, life story, funeral service details, and memories of ' . $obituary->full_name . ' from ' . $obituary->town . ', ' . $obituary->county . ' County. Share condolences.')
@section('seo_keywords', $obituary->seo_keywords ?: $obituary->full_name . ' obituary, ' . $obituary->county . ' obituaries, ' . $obituary->full_name . ' funeral details, death notice Kenya')
@section('canonical_url', $obituary->canonical_url ?: route('obituaries.show', $obituary->slug))

@section('og_title', $obituary->meta_title ?: $obituary->full_name . ' Obituary | Obituaries.co.ke')
@section('og_description', $obituary->meta_description ?: 'Read the obituary, life story, funeral service details, and memories of ' . $obituary->full_name . '.')
@section('og_image', $obituary->photo ? asset('storage/' . $obituary->photo) : asset('images/og-default.jpg'))
@section('og_type', 'article')

@section('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Person",
      "@id": "{{ route('obituaries.show', $obituary->slug) }}#person",
      "name": "{{ $obituary->full_name }}",
      "birthDate": "{{ $obituary->date_of_birth->format('Y-m-d') }}",
      "deathDate": "{{ $obituary->date_of_death->format('Y-m-d') }}",
      "image": "{{ $obituary->photo ? asset('storage/' . $obituary->photo) : asset('images/og-default.jpg') }}",
      "description": "{{ Str::limit(strip_tags($obituary->biography), 200) }}",
      "homeLocation": {
        "@type": "Place",
        "name": "{{ $obituary->town }}, {{ $obituary->county }} County, Kenya"
      }
    },
    {
      "@type": "BreadcrumbList",
      "@id": "{{ route('obituaries.show', $obituary->slug) }}#breadcrumb",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Home",
          "item": "{{ url('/') }}"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "Obituaries Directory",
          "item": "{{ url('/search') }}"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "{{ $obituary->county }} Obituaries",
          "item": "{{ url('/county/' . \Illuminate\Support\Str::slug($obituary->county) . '-obituaries') }}"
        },
        {
          "@type": "ListItem",
          "position": 4,
          "name": "{{ $obituary->full_name }}"
        }
      ]
    }
  ]
}
</script>
@endsection

@section('content')

<!-- Immersive Hero Section matching Stitch Design & Screenshot -->
<section class="relative w-full min-h-[380px] sm:min-h-[460px] lg:h-[500px] flex items-center overflow-hidden border-b border-surface-container-high py-10 sm:py-16">
    <!-- Cover Image with Gradient Scrim -->
    <div class="absolute inset-0 z-0">
        <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBMukGU84gf4tb_RHChMQDd_B23JPy5Bu7V7T7TGKz5opRwHBvTpsGdK-UHzBunI2Fpmqfz80T4SmsEkU-ZlmHQMTw32EbDPwfuznH0PjAgOu1GJy548fELHnHza2bIvWbvZVoS--L_nXm_DHosxPzTXn44Zhu6PvDMZz8R5vITf88A6lz0tzxb12ZVmauEXaWiRtxNdpFUcTAR5uAWvPbTkc1GhRXAcfJ54MPWU0bwSPX95qqv_Pj3NI8Jd-wUPoiks-aTR4zOGNk-')"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-surface via-surface/90 to-surface/30 sm:to-transparent"></div>
    </div>

    <!-- Profile & Vital Info Card Overlap -->
    <div class="relative z-10 w-full max-w-[1200px] mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center sm:items-center gap-6 sm:gap-10">
        <!-- Square Portrait Photo with Rounded Corners & Shadow -->
        <div class="relative flex-shrink-0">
            <div class="w-40 h-40 sm:w-48 sm:h-48 lg:w-56 lg:h-56 rounded-2xl border-4 border-white shadow-xl overflow-hidden bg-surface-container flex items-center justify-center">
                @if($obituary->photo)
                    <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-b from-primary to-primary-container flex flex-col items-center justify-center p-4 text-center text-on-primary">
                        <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-secondary-fixed mb-2">
                            <span class="material-symbols-outlined text-[28px]">person</span>
                        </div>
                        <span class="font-serif text-xs italic">In Loving Memory</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Vital Info Text -->
        <div class="flex flex-col items-center sm:items-start text-center sm:text-left">
            <span class="text-[11px] sm:text-xs font-bold text-secondary tracking-[0.2em] uppercase mb-1.5 sm:mb-2">In Loving Memory</span>
            <h1 class="font-serif text-3xl sm:text-5xl lg:text-6xl font-bold text-primary mb-2 sm:mb-3 leading-tight">{{ $obituary->full_name }}</h1>
            
            <div class="flex items-center gap-3 text-on-surface-variant mb-3 font-semibold text-sm sm:text-base">
                <span>{{ $obituary->date_of_birth->format('Y') }}</span>
                <span class="w-6 h-[1px] bg-outline-variant"></span>
                <span>{{ $obituary->date_of_death->format('Y') }}</span>
                @if($obituary->age)
                    <span class="text-xs text-on-surface-variant/70 font-normal">({{ $obituary->age }} Years)</span>
                @endif
            </div>

            <p class="font-serif italic text-on-surface-variant opacity-80 text-sm sm:text-base">"Forever in our hearts"</p>
        </div>
    </div>
</section>

<!-- Content Grid matching Screenshot -->
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 sm:gap-12 mt-12 sm:mt-16 pb-16">
    
    <!-- Left Column: Biography, Gallery & Candles (8 cols) -->
    <div class="lg:col-span-8 space-y-12 sm:space-y-16">
        
        <!-- Life & Journey Section -->
        <section class="relative">
            @php
                $nameParts = explode(' ', $obituary->full_name);
                $firstName = $nameParts[0] ?? $obituary->full_name;
            @endphp

            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-primary mb-6 sm:mb-8 flex items-center gap-4">
                <span>The Life of {{ $firstName }}</span>
                <span class="flex-1 h-[1px] bg-surface-container-high"></span>
            </h2>

            <div class="prose max-w-none text-on-surface text-base sm:text-lg leading-relaxed font-serif break-words break-all overflow-hidden">
                {!! nl2br(e($obituary->biography)) !!}
            </div>

            <!-- Featured Pull Quote Box -->
            <div class="my-8 sm:my-10 p-6 sm:p-8 bg-surface-container-low rounded-xl border-l-4 border-secondary italic shadow-xs">
                <p class="font-serif text-base sm:text-xl text-on-surface-variant">
                    "A tree is known by its fruit, and a man by his deeds. A forest of kindness."
                </p>
            </div>
        </section>

        @php
            $uploadedGallery = is_array($obituary->gallery_images) ? array_filter($obituary->gallery_images) : [];
        @endphp

        <!-- Moments in Time Photo Gallery Section (Only rendered if gallery images uploaded) -->
        @if(count($uploadedGallery) > 0)
            <section id="gallery" class="pt-4 border-t border-surface-container-high">
                <div class="flex items-end justify-between mb-8">
                    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-primary">Moments in Time</h2>
                    <span class="text-xs font-semibold text-on-surface-variant">{{ count($uploadedGallery) }} {{ Str::plural('Photograph', count($uploadedGallery)) }}</span>
                </div>

                <!-- Gallery Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($uploadedGallery as $img)
                        <div class="aspect-square overflow-hidden rounded-xl shadow-xs group bg-surface-container">
                            <img src="{{ asset('storage/' . $img) }}" alt="Moments in Time" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Light a Virtual Candle Section -->
        <section id="candles" class="pt-8 border-t border-surface-container-high space-y-6" x-data="{ candleModal: false }">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-primary flex items-center gap-2">
                        <span>🕯️ Candles of Remembrance</span>
                    </h2>
                    <p class="text-xs text-on-surface-variant mt-1">Light a virtual candle to honor {{ $firstName }} and offer your condolences.</p>
                </div>

                <button type="button" @click="candleModal = true" class="px-5 py-3 bg-secondary text-on-secondary rounded-xl text-xs font-bold shadow-md hover:bg-secondary/90 transition-all flex items-center justify-center space-x-2">
                    <span>🕯️ Light a Candle</span>
                </button>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Candle Count Banner -->
            <div class="p-5 bg-gradient-to-r from-primary to-primary-container text-on-primary rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-amber-400 text-2xl animate-pulse">
                        🕯️
                    </div>
                    <div>
                        <span class="font-serif text-xl font-bold block">{{ $obituary->candles->count() }} {{ Str::plural('Candle', $obituary->candles->count()) }} Lit</span>
                        <span class="text-xs text-primary-fixed/80">In loving memory and eternal peace</span>
                    </div>
                </div>
            </div>

            <!-- Lit Candles Grid / List -->
            @if($obituary->candles->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($obituary->candles as $candle)
                        <div class="p-4 bg-surface-container-lowest rounded-xl border border-surface-container shadow-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-primary text-xs">🕯️ {{ $candle->name }}</span>
                                <span class="text-[10px] text-on-surface-variant/60">{{ $candle->created_at->diffForHumans() }}</span>
                            </div>
                            @if($candle->message)
                                <p class="text-xs text-on-surface-variant italic font-serif break-words break-all overflow-hidden">"{{ $candle->message }}"</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center bg-surface-container-low rounded-2xl border border-dashed border-outline-variant text-on-surface-variant">
                    <span class="text-3xl block mb-2">🕯️</span>
                    <p class="font-serif text-sm font-bold text-primary mb-1">Be the first to light a candle</p>
                    <p class="text-xs text-on-surface-variant/70 mb-4">Share your love, warmth, and heartfelt tribute for {{ $firstName }}.</p>
                    <button type="button" @click="candleModal = true" class="px-5 py-2.5 bg-primary text-on-primary rounded-xl text-xs font-semibold">
                        Light a Candle Now
                    </button>
                </div>
            @endif

            <!-- Light Candle Modal -->
            <div x-show="candleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-transition>
                <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-surface-container space-y-6" @click.away="candleModal = false">
                    <div class="flex items-center justify-between border-b border-surface-container pb-4">
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl">🕯️</span>
                            <h3 class="font-serif text-lg font-bold text-primary">Light a Candle</h3>
                        </div>
                        <button type="button" @click="candleModal = false" class="text-on-surface-variant text-sm font-bold hover:text-primary">&times;</button>
                    </div>

                    <form action="{{ route('obituaries.candle', $obituary->id) }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold uppercase text-on-surface-variant mb-1.5">Your Name (Optional)</label>
                            <input type="text" name="name" placeholder="e.g. Grace Wanjiku (Leave blank for Anonymous)" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl text-xs text-on-surface">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-on-surface-variant mb-1.5">Short Tribute Message (Optional)</label>
                            <textarea name="message" rows="3" placeholder="e.g. Rest in eternal peace. You will forever be remembered." class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl text-xs text-on-surface leading-relaxed"></textarea>
                        </div>

                        <div class="pt-2 flex justify-end space-x-3">
                            <button type="button" @click="candleModal = false" class="px-4 py-2 bg-surface-container text-on-surface rounded-xl text-xs font-semibold">Cancel</button>
                            <button type="submit" class="px-6 py-2.5 bg-secondary text-on-secondary rounded-xl text-xs font-bold shadow-md">
                                🕯️ Light Candle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Family Submitter Note -->
        <div class="p-4 sm:p-6 bg-surface-container-low rounded-xl border-l-4 border-secondary text-xs text-on-surface-variant flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <span class="font-bold text-primary block text-xs sm:text-sm">Submitted with love by {{ $obituary->submitter_name }}</span>
                <span>Relationship: {{ $obituary->relationship }}</span>
            </div>
            <span class="font-serif italic text-secondary font-bold text-xs">Obituaries.co.ke</span>
        </div>

        <div class="pt-4 flex items-center justify-between" x-data="{ reportModal: false }">
            <a href="{{ route('obituaries.search') }}" class="text-xs font-bold text-primary hover:text-secondary inline-flex items-center space-x-1">
                <span>&larr; Return to Obituary Directory</span>
            </a>

            <button type="button" @click="reportModal = true" class="text-xs text-rose-600 hover:text-rose-800 font-semibold flex items-center space-x-1">
                <span class="material-symbols-outlined text-[16px]">flag</span>
                <span>Report Obituary Issue</span>
            </button>

            <!-- Report Modal -->
            <div x-show="reportModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-surface-container space-y-6" @click.away="reportModal = false">
                    <div class="flex items-center justify-between border-b border-surface-container pb-4">
                        <div class="flex items-center space-x-2 text-rose-600">
                            <span class="material-symbols-outlined text-[24px]">flag</span>
                            <h3 class="font-serif text-lg font-bold text-primary">Report Obituary Notice</h3>
                        </div>
                        <button type="button" @click="reportModal = false" class="text-on-surface-variant text-sm font-bold hover:text-primary">&times;</button>
                    </div>

                    <form action="{{ route('obituaries.report', $obituary->id) }}" method="POST" class="space-y-4 text-xs">
                        @csrf

                        <div>
                            <label class="block font-semibold uppercase text-on-surface-variant mb-1">Your Full Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="reporter_name" required placeholder="e.g. David Ochieng" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface">
                        </div>

                        <div>
                            <label class="block font-semibold uppercase text-on-surface-variant mb-1">Your Email Address <span class="text-rose-500">*</span></label>
                            <input type="email" name="reporter_email" required placeholder="e.g. david@example.com" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface">
                        </div>

                        <div>
                            <label class="block font-semibold uppercase text-on-surface-variant mb-1">Reason for Reporting <span class="text-rose-500">*</span></label>
                            <select name="reason" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface font-semibold">
                                <option value="inaccurate_info">Inaccurate Dates or Details</option>
                                <option value="impersonation">Fake / Impersonation Submission</option>
                                <option value="unauthorized_post">Unauthorized Family Post</option>
                                <option value="copyright_violation">Copyrighted Photo or Text</option>
                                <option value="offensive_content">Inappropriate or Offensive Content</option>
                                <option value="other">Other Concern</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold uppercase text-on-surface-variant mb-1">Description / Details <span class="text-rose-500">*</span></label>
                            <textarea name="details" rows="3" required placeholder="Please describe the issue in detail so our editorial team can investigate." class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface leading-relaxed"></textarea>
                        </div>

                        <div class="pt-2 flex justify-end space-x-3">
                            <button type="button" @click="reportModal = false" class="px-4 py-2 bg-surface-container text-on-surface rounded-xl font-semibold">Cancel</button>
                            <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold shadow-md">
                                Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Service Details Card & Share Widgets (4 cols) -->
    <div class="lg:col-span-4 space-y-6 sm:space-y-8" x-data="{ copied: false }">
        
        <!-- Funeral Service Info Card matching Screenshot -->
        <div class="bg-surface-container-lowest rounded-2xl shadow-md p-6 sm:p-8 border border-surface-container space-y-6">
            <h3 class="font-serif text-xl font-bold text-primary border-b border-surface-container pb-4">Service Details</h3>
            
            <div class="space-y-5 text-xs">
                <!-- Date & Time -->
                <div class="flex gap-4 items-start">
                    <div class="w-10 h-10 rounded-full bg-primary-container/10 flex items-center justify-center flex-shrink-0 text-primary">
                        <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                    </div>
                    <div>
                        <p class="font-bold uppercase tracking-wider text-on-surface-variant text-[10px]">Date & Time</p>
                        <p class="font-semibold text-on-surface text-sm">
                            {{ $obituary->funeral_date ? $obituary->funeral_date->format('l, F jS, Y') : 'Saturday, July 12th, 2026' }}
                        </p>
                        <p class="text-on-surface-variant text-xs">10:00 AM EAT</p>
                    </div>
                </div>

                <!-- Venue / Location -->
                <div class="flex gap-4 items-start">
                    <div class="w-10 h-10 rounded-full bg-primary-container/10 flex items-center justify-center flex-shrink-0 text-primary">
                        <span class="material-symbols-outlined text-[20px]">location_on</span>
                    </div>
                    <div>
                        <p class="font-bold uppercase tracking-wider text-on-surface-variant text-[10px]">Venue</p>
                        <p class="font-semibold text-on-surface text-sm">
                            {{ $obituary->church_service_location ?? $obituary->burial_location ?? 'St. Peter\'s Cathedral' }}
                        </p>
                        <p class="text-on-surface-variant text-xs">{{ $obituary->town }}, {{ $obituary->county }}</p>
                    </div>
                </div>
            </div>

            <!-- Map Placeholder Box -->
            <div class="w-full h-32 rounded-xl bg-surface-container border border-outline-variant/30 flex items-center justify-center text-on-surface-variant/40 text-xs font-semibold">
                <div class="flex items-center space-x-1.5">
                    <span class="material-symbols-outlined text-[18px]">map</span>
                    <span>Service Location Map</span>
                </div>
            </div>

            <!-- PDF Programme Download Button matching Screenshot -->
            @if($obituary->programme_file)
                <a href="{{ asset('storage/' . $obituary->programme_file) }}" target="_blank" class="w-full flex items-center justify-center gap-2 bg-primary text-on-primary py-3.5 rounded-xl text-xs font-semibold hover:bg-primary-container transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Download Funeral Programme</span>
                </a>
            @else
                <button type="button" disabled class="w-full flex items-center justify-center gap-2 bg-primary/90 text-on-primary py-3.5 rounded-xl text-xs font-semibold opacity-95">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Download Funeral Programme</span>
                </button>
            @endif

            <!-- Share Buttons matching Screenshot -->
            <div class="pt-6 border-t border-surface-container">
                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-4 text-center">Share This Memorial</p>
                
                @php
                    $shareUrl = urlencode(url()->current());
                    $shareText = urlencode("In loving memory of {$obituary->full_name}. Read full obituary and funeral details here: " . url()->current());
                @endphp

                <div class="flex justify-center gap-4">
                    <a href="https://api.whatsapp.com/send?text={{ $shareText }}" target="_blank" class="w-11 h-11 rounded-full bg-[#25D366]/10 flex items-center justify-center text-[#25D366] hover:bg-[#25D366] hover:text-white transition-all" title="Share on WhatsApp">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.438 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.94 3.659 1.437 5.63 1.438h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    </a>

                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" class="w-11 h-11 rounded-full bg-[#1877F2]/10 flex items-center justify-center text-[#1877F2] hover:bg-[#1877F2] hover:text-white transition-all" title="Share on Facebook">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>

                    <button type="button" @click="navigator.clipboard.writeText('{{ url()->current() }}'); copied = true; setTimeout(() => copied = false, 3000)" class="w-11 h-11 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary transition-all" title="Copy Link">
                        <span class="material-symbols-outlined text-[18px]" x-text="copied ? 'check' : 'link'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Decorative Bottom Church Accent -->
<div class="w-full flex justify-center py-16 sm:py-20 border-t border-surface-container-high/50">
    <div class="flex items-center gap-6">
        <div class="w-12 h-[1px] bg-secondary/30"></div>
        <span class="material-symbols-outlined text-secondary opacity-40 text-[24px]">church</span>
        <div class="w-12 h-[1px] bg-secondary/30"></div>
    </div>
</div>

@endsection
