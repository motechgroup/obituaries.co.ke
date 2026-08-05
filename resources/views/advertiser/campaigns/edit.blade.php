@extends('layouts.advertiser')

@section('title', 'Edit Campaign - ' . $campaign->name . ' | Advertiser Portal')

@section('content')
<div x-data="advertiserEditCampaign()" class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Edit Campaign: {{ $campaign->name }}</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Update campaign details, landing link, or replace banner graphic.</p>
        </div>
        <a href="{{ route('advertiser.campaigns.show', $campaign->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all">
            &larr; Back to Campaign Details
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1 font-medium">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('advertiser.campaigns.update', $campaign->id) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 space-y-8 shadow-sm">
        @csrf
        @method('PUT')

        <!-- Campaign Details -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">1. Campaign Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Campaign Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $campaign->name) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Landing URL / Website Link <span class="text-slate-400 font-normal lowercase">(optional)</span></label>
                    <input type="url" name="landing_url" value="{{ old('landing_url', $campaign->landing_url) }}" placeholder="https://yourbusiness.co.ke (Optional)" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                    <span class="text-[10px] text-slate-500 mt-1 block">Leave blank for a display-only branding banner without website redirection.</span>
                </div>
            </div>
        </div>

        <!-- Placement & Banner Size -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">2. Placement Slot & Dimensions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Placement Slot <span class="text-rose-500">*</span></label>
                    <select name="ad_placement_id" x-model="placementId" @change="updateSizes()" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                        @foreach($placements as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ ucfirst($p->page_type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Banner Size <span class="text-rose-500">*</span></label>
                    <select name="banner_size_id" x-model="sizeId" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                        <template x-for="sz in availableSizes" :key="sz.id">
                            <option :value="sz.id" x-text="sz.name + ' (' + sz.width + ' × ' + sz.height + ' px)'"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Replace Banner Image -->
            <div class="p-4 bg-slate-50 border border-slate-300 rounded-xl space-y-3">
                <div class="flex items-center space-x-4">
                    <img src="{{ $campaign->banner_url }}" class="h-16 w-auto object-contain rounded border border-slate-300 bg-white">
                    <div class="text-xs text-slate-600">
                        <span class="font-bold text-slate-900 block">Current Banner Image</span>
                        <span>Upload a new image below if replacing the current graphic.</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-200">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Replace Image File (Optional)</label>
                    <input type="file" name="banner_image" accept="image/jpeg,image/png,image/jpg" class="text-xs text-slate-800 font-medium">
                    <p class="text-[11px] text-slate-500 mt-1">Max Size: <strong class="text-amber-900">5MB</strong>. Formats: <strong class="text-slate-900">PNG, JPEG, JPG only</strong>.</p>
                </div>
            </div>
        </div>

        <!-- Geographic Targeting -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">3. County & National Targeting</h3>
            <div class="flex items-center space-x-3 p-4 bg-slate-50 rounded-xl border border-slate-300">
                <input type="checkbox" name="is_national" id="is_national" value="1" x-model="isNational" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                <label for="is_national" class="text-xs font-bold text-slate-900 cursor-pointer">
                    Target Entire Kenya (National Coverage Across All 47 Counties)
                </label>
            </div>

            <div x-show="!isNational" class="space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Select Target Counties</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 max-h-48 overflow-y-auto p-4 bg-slate-50 border border-slate-300 rounded-xl">
                    @foreach($counties as $c)
                        <label class="flex items-center space-x-2 text-xs text-slate-800 font-medium hover:text-slate-950 cursor-pointer py-1">
                            <input type="checkbox" name="counties[]" value="{{ $c }}" x-model="selectedCounties" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <span>{{ $c }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Schedule & Featured -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">4. Schedule Options</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Start Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $campaign->start_date->format('Y-m-d')) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">End Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', $campaign->end_date->format('Y-m-d')) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                </div>
            </div>

            <div class="flex items-center space-x-3 p-4 bg-slate-50 rounded-xl border border-slate-300">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" x-model="isFeatured" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                <div>
                    <label for="is_featured" class="text-xs font-bold text-slate-900 cursor-pointer block">
                        Enable Featured Premium Weighting (+3x Rotation Priority)
                    </label>
                    <p class="text-[11px] text-slate-600 font-medium">Featured banners get 3x higher display frequency.</p>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end space-x-3">
            <a href="{{ route('advertiser.campaigns.show', $campaign->id) }}" class="px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all">Cancel</a>
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-8 py-3.5 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md">
                <span>Save Changes</span>
            </button>
        </div>
    </form>
</div>

<script>
function advertiserEditCampaign() {
    const placements = @json($placements);
    const fallbackSizes = @json($bannerSizes);
    const initialPlacementId = {{ $campaign->ad_placement_id }};
    const initialSizeId = {{ $campaign->banner_size_id }};
    const initialCounties = @json($campaign->counties->pluck('county'));

    return {
        placementId: initialPlacementId,
        sizeId: initialSizeId,
        isNational: {{ $campaign->is_national ? 'true' : 'false' }},
        isFeatured: {{ $campaign->is_featured ? 'true' : 'false' }},
        selectedCounties: initialCounties.length > 0 ? initialCounties : ['Nairobi'],
        availableSizes: [],

        init() {
            this.updateSizes();
            this.sizeId = initialSizeId;
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
        }
    }
}
</script>
@endsection
