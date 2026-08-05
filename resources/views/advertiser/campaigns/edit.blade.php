@extends('layouts.advertiser')

@section('title', 'Edit Campaign - ' . $campaign->name . ' | Advertiser Portal')

@section('content')
<div x-data="advertiserEditCampaign()" class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white">Edit Campaign: {{ $campaign->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-400">Update campaign details, landing link, or replace banner graphic.</p>
        </div>
        <a href="{{ route('advertiser.campaigns.show', $campaign->id) }}" class="text-xs font-bold text-slate-400 hover:text-white inline-flex items-center space-x-1">
            <span>&larr; Back to Campaign Details</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-xs text-rose-300 space-y-1">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('advertiser.campaigns.update', $campaign->id) }}" method="POST" enctype="multipart/form-data" class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-8 shadow-xl">
        @csrf
        @method('PUT')

        <!-- Campaign Details -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400 border-b border-slate-800 pb-2">1. Campaign Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Campaign Name <span class="text-amber-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $campaign->name) }}" required class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Landing URL / Website Link <span class="text-slate-400 font-normal lowercase">(optional)</span></label>
                    <input type="url" name="landing_url" value="{{ old('landing_url', $campaign->landing_url) }}" placeholder="https://yourbusiness.co.ke (Optional)" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:border-amber-500 outline-none">
                    <span class="text-[10px] text-slate-400 mt-1 block">Leave blank for a display-only branding banner without website redirection.</span>
                </div>
            </div>
        </div>

        <!-- Placement & Banner Size -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400 border-b border-slate-800 pb-2">2. Placement Slot & Dimensions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Placement Slot <span class="text-amber-400">*</span></label>
                    <select name="ad_placement_id" x-model="placementId" @change="updateSizes()" required class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:border-amber-500 outline-none">
                        @foreach($placements as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ ucfirst($p->page_type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Banner Size <span class="text-amber-400">*</span></label>
                    <select name="banner_size_id" x-model="sizeId" required class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:border-amber-500 outline-none">
                        <template x-for="sz in availableSizes" :key="sz.id">
                            <option :value="sz.id" x-text="sz.name + ' (' + sz.width + ' × ' + sz.height + ' px)'"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Replace Banner Image -->
            <div class="p-4 bg-slate-950 border border-slate-800 rounded-xl space-y-3">
                <div class="flex items-center space-x-4">
                    <img src="{{ $campaign->banner_url }}" class="h-16 w-auto object-contain rounded border border-slate-800">
                    <div class="text-xs text-slate-400">
                        <span class="font-bold text-white block">Current Banner Image</span>
                        <span>Upload a new image below if replacing the current graphic.</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-800">
                    <label class="block text-[11px] font-bold text-slate-300 mb-1">Replace Image File (Optional)</label>
                    <input type="file" name="banner_image" accept="image/jpeg,image/png,image/jpg" class="text-xs text-slate-300">
                    <p class="text-[11px] text-slate-500 mt-1">Max Size: <strong class="text-amber-400">5MB</strong>. Formats: <strong class="text-white">PNG, JPEG, JPG only</strong>.</p>
                </div>
            </div>
        </div>

        <!-- Geographic Targeting -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400 border-b border-slate-800 pb-2">3. County & National Targeting</h3>
            <div class="flex items-center space-x-3 p-4 bg-slate-950 rounded-xl border border-slate-800">
                <input type="checkbox" name="is_national" id="is_national" value="1" x-model="isNational" class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-amber-500 focus:ring-amber-500">
                <label for="is_national" class="text-xs font-bold text-white cursor-pointer">
                    Target Entire Kenya (National Coverage Across All 47 Counties)
                </label>
            </div>

            <div x-show="!isNational" class="space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">Select Target Counties</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 max-h-48 overflow-y-auto p-4 bg-slate-950 border border-slate-800 rounded-xl">
                    @foreach($counties as $c)
                        <label class="flex items-center space-x-2 text-xs text-slate-300 hover:text-white cursor-pointer py-1">
                            <input type="checkbox" name="counties[]" value="{{ $c }}" x-model="selectedCounties" class="rounded border-slate-700 bg-slate-900 text-amber-500 focus:ring-amber-500">
                            <span>{{ $c }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Schedule & Featured -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400 border-b border-slate-800 pb-2">4. Schedule Options</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Start Date <span class="text-amber-400">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $campaign->start_date->format('Y-m-d')) }}" required class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">End Date <span class="text-amber-400">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', $campaign->end_date->format('Y-m-d')) }}" required class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:border-amber-500 outline-none">
                </div>
            </div>

            <div class="flex items-center space-x-3 p-4 bg-slate-950 rounded-xl border border-slate-800">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" x-model="isFeatured" class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-amber-500 focus:ring-amber-500">
                <div>
                    <label for="is_featured" class="text-xs font-bold text-white cursor-pointer block">
                        Enable Featured Premium Weighting (+3x Rotation Priority)
                    </label>
                    <p class="text-[11px] text-slate-400 font-medium">Featured banners get 3x higher display frequency.</p>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end space-x-3">
            <a href="{{ route('advertiser.campaigns.show', $campaign->id) }}" class="px-6 py-3.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition-all">Cancel</a>
            <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-8 py-3.5 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md">
                <span>Save Changes</span>
            </button>
        </div>
    </form>
</div>

<script>
function advertiserEditCampaign() {
    const placements = @json($placements);
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
            if (p && p.banner_sizes) {
                this.availableSizes = p.banner_sizes;
            } else {
                this.availableSizes = [];
            }
        }
    }
}
</script>
@endsection
