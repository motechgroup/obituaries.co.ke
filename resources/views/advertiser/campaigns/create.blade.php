@extends('layouts.advertiser')

@section('title', 'Create New Ad Campaign | Advertiser Portal')

@section('content')
<div x-data="campaignWizard()" class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Create New Ad Campaign</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Target high-intent audiences across Kenya by county and placement location.</p>
        </div>
        <a href="{{ route('advertiser.campaigns.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all">
            &larr; Back to Campaigns
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1 font-medium">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('advertiser.campaigns.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 space-y-8 shadow-sm">
        @csrf

        <!-- 1. Campaign Details -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">1. Campaign Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Campaign Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Funeral Service Promo Q3" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Landing URL / Website Link <span class="text-slate-400 font-normal lowercase">(optional)</span></label>
                    <input type="url" name="landing_url" value="{{ old('landing_url') }}" placeholder="https://yourbusiness.co.ke (Optional)" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                    <span class="text-[10px] text-slate-500 mt-1 block">Leave blank for a display-only branding banner without website redirection.</span>
                </div>
            </div>
        </div>

        <!-- 2. Placement Slot & Banner Dimension -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">2. Placement Slot & Banner Dimension</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Placement Slot <span class="text-rose-500">*</span></label>
                    <select name="ad_placement_id" x-model="placementId" @change="updateSizes(); calculatePrice()" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                        <option value="">Select Placement Slot...</option>
                        @foreach($placements as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ ucfirst($p->page_type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Banner Size <span class="text-rose-500">*</span></label>
                    <select name="banner_size_id" x-model="sizeId" @change="calculatePrice()" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                        <option value="">Select Placement First...</option>
                        <template x-for="sz in availableSizes" :key="sz.id">
                            <option :value="sz.id" x-text="sz.name + ' (' + sz.width + ' × ' + sz.height + ' px)'"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Banner Upload Field -->
            <div class="pt-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Upload Banner Image File <span class="text-rose-500">*</span></label>
                <div class="p-4 bg-slate-50 border border-slate-300 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <input type="file" name="banner_image" accept="image/jpeg,image/png,image/jpg" required class="text-xs text-slate-800 font-medium">
                    <p class="text-[11px] text-slate-600">Max Size: <strong class="text-amber-900">5MB</strong>. Valid formats: <strong class="text-slate-900">PNG, JPEG, JPG only</strong>.</p>
                </div>
            </div>
        </div>

        <!-- 3. Geographic County Targeting -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">3. Geographic County Targeting</h3>

            <div class="flex items-center space-x-3 p-4 bg-slate-50 rounded-xl border border-slate-300">
                <input type="checkbox" name="is_national" id="is_national" value="1" x-model="isNational" @change="toggleNational()" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                <label for="is_national" class="text-xs font-bold text-slate-900 cursor-pointer">
                    Target Entire Kenya (National Coverage Across All 47 Counties)
                </label>
            </div>

            <div x-show="isNational" class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs font-bold text-amber-900 flex items-center space-x-2" x-cloak>
                <span class="material-symbols-outlined text-[20px] text-amber-600">public</span>
                <span>National Coverage Active: Your advertisement will be broadcast across all 47 counties in Kenya.</span>
            </div>

            <div x-show="!isNational" class="space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Select Target Counties</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 max-h-48 overflow-y-auto p-4 bg-slate-50 border border-slate-300 rounded-xl">
                    @foreach($counties as $c)
                        <label class="flex items-center space-x-2 text-xs text-slate-800 font-medium hover:text-slate-950 cursor-pointer py-1">
                            <input type="checkbox" name="counties[]" value="{{ $c }}" x-model="selectedCounties" @change="calculatePrice()" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <span>{{ $c }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 4. Schedule & Options -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">4. Schedule & Options</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Start Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" x-model="startDate" @change="calculatePrice()" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">End Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" x-model="endDate" @change="calculatePrice()" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                </div>
            </div>

            <div class="flex items-center space-x-3 p-4 bg-slate-50 rounded-xl border border-slate-300">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" x-model="isFeatured" @change="calculatePrice()" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                <div>
                    <label for="is_featured" class="text-xs font-bold text-slate-900 cursor-pointer block">
                        Enable Featured Premium Weighting (+3x Rotation Priority)
                    </label>
                    <p class="text-[11px] text-slate-600 font-medium">Featured banners get 3x higher display frequency and top priority.</p>
                </div>
            </div>
        </div>

        <!-- Live Price Summary Card -->
        <div class="p-6 bg-amber-500/10 border border-amber-300/60 rounded-2xl space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-950">Total Campaign Investment</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-amber-950 font-mono" x-text="'KES ' + priceData.total_price.toLocaleString()">KES 0</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs text-slate-700 font-medium pt-2 border-t border-amber-300/40">
                <div>Duration: <strong class="text-slate-900 font-bold" x-text="priceData.total_days + ' Days'">1 Day</strong></div>
                <div>Daily Rate: <strong class="text-slate-900 font-bold" x-text="'KES ' + priceData.daily_rate">KES 0</strong></div>
                <div>Subtotal: <strong class="text-slate-900 font-bold" x-text="'KES ' + priceData.subtotal">KES 0</strong></div>
                <div>Featured Fee: <strong class="text-slate-900 font-bold" x-text="'KES ' + priceData.featured_fee">KES 0</strong></div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-8 py-4 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md flex items-center space-x-2">
                <span>Proceed to M-Pesa Payment</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </button>
        </div>
    </form>
</div>

<script>
function campaignWizard() {
    const placements = @json($placements);
    const fallbackSizes = @json($bannerSizes);
    const allCounties = @json($counties);

    return {
        placementId: '',
        sizeId: '',
        isNational: false,
        isFeatured: false,
        selectedCounties: ['Nairobi'],
        startDate: '{{ date("Y-m-d") }}',
        endDate: '{{ date("Y-m-d", strtotime("+30 days")) }}',
        availableSizes: [],
        priceData: {
            total_days: 30,
            daily_rate: 600,
            subtotal: 18000,
            featured_fee: 0,
            total_price: 18000
        },

        init() {
            if (placements.length > 0) {
                this.placementId = placements[0].id;
                this.updateSizes();
            } else if (fallbackSizes.length > 0) {
                this.availableSizes = fallbackSizes;
                this.sizeId = fallbackSizes[0].id;
            }
        },

        updateSizes() {
            const p = placements.find(x => x.id == this.placementId);
            let sizes = [];
            if (p) {
                sizes = p.banner_sizes || p.bannerSizes || [];
            }
            if (!sizes || sizes.length === 0) {
                sizes = fallbackSizes;
            }
            this.availableSizes = sizes;
            if (this.availableSizes.length > 0) {
                this.sizeId = this.availableSizes[0].id;
            } else {
                this.sizeId = '';
            }
            this.calculatePrice();
        },

        toggleNational() {
            if (this.isNational) {
                this.selectedCounties = [...allCounties];
            } else {
                this.selectedCounties = ['Nairobi'];
            }
            this.calculatePrice();
        },

        calculatePrice() {
            if (!this.placementId || !this.sizeId) return;

            fetch('{{ route("advertiser.campaigns.pricing-calculator") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ad_placement_id: this.placementId,
                    banner_size_id: this.sizeId,
                    start_date: this.startDate,
                    end_date: this.endDate,
                    counties: this.selectedCounties,
                    is_national: this.isNational,
                    is_featured: this.isFeatured
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.total_price !== undefined) {
                    this.priceData = data;
                }
            })
            .catch(err => console.error(err));
        }
    }
}
</script>
@endsection
