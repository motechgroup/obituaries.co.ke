@extends('layouts.admin')

@section('title', 'Edit Campaign - ' . $campaign->name . ' | Admin Panel')

@section('content')
<div x-data="adminEditCampaign()" class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Edit Campaign: {{ $campaign->name }}</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Modify advertisement parameters, placement slot, banner graphic, or status.</p>
        </div>
        <a href="{{ route('admin.advertising.campaigns.show', $campaign->id) }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 inline-flex items-center space-x-1">
            <span>&larr; Back to Campaign Details</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.advertising.campaigns.update', $campaign->id) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 space-y-8 shadow-sm">
        @csrf
        @method('PUT')

        <!-- 1. Advertiser Selection & Status -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-200 pb-2">1. Advertiser & Publication Status</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Select Advertiser Account <span class="text-rose-500">*</span></label>
                    <select name="advertiser_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-bold focus:bg-white focus:border-amber-500 outline-none">
                        @foreach($advertisers as $adv)
                            <option value="{{ $adv->id }}" {{ old('advertiser_id', $campaign->advertiser_id) == $adv->id ? 'selected' : '' }}>
                                {{ str_contains(strtolower($adv->business_name), 'system') ? '⚙️ System Account: ' : '🏢 ' }}{{ $adv->business_name }} ({{ $adv->contact_person }} • {{ $adv->phone_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Publication Status <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-extrabold focus:bg-white focus:border-amber-500 outline-none">
                        <option value="running" {{ old('status', $campaign->status) === 'running' ? 'selected' : '' }}>🟢 RUNNING (Live)</option>
                        <option value="pending_approval" {{ old('status', $campaign->status) === 'pending_approval' ? 'selected' : '' }}>🟡 PENDING APPROVAL</option>
                        <option value="approved" {{ old('status', $campaign->status) === 'approved' ? 'selected' : '' }}>🔵 APPROVED (Paused)</option>
                        <option value="payment_pending" {{ old('status', $campaign->status) === 'payment_pending' ? 'selected' : '' }}>🔴 PAYMENT PENDING</option>
                        <option value="rejected" {{ old('status', $campaign->status) === 'rejected' ? 'selected' : '' }}>⛔ REJECTED</option>
                        <option value="expired" {{ old('status', $campaign->status) === 'expired' ? 'selected' : '' }}>⚪ EXPIRED</option>
                        <option value="draft" {{ old('status', $campaign->status) === 'draft' ? 'selected' : '' }}>⚪ DRAFT</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Campaign Name & Landing Link -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-200 pb-2">2. Campaign Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Campaign Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $campaign->name) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-amber-500 outline-none font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Landing URL / Website Link <span class="text-slate-400 font-normal lowercase">(optional)</span></label>
                    <input type="url" name="landing_url" value="{{ old('landing_url', $campaign->landing_url) }}" placeholder="https://example.co.ke (Optional)" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-amber-500 outline-none">
                    <span class="text-[10px] text-slate-500 mt-1 block">Leave blank for a display-only branding banner without website redirection.</span>
                </div>
            </div>
        </div>

        <!-- 3. Placement Slot & Banner Size Selection -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-200 pb-2">3. Placement Slot & Banner Dimension</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Placement Slot <span class="text-rose-500">*</span></label>
                    <select name="ad_placement_id" x-model="placementId" @change="updateSizes()" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-medium focus:bg-white focus:border-amber-500 outline-none">
                        @foreach($placements as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ ucfirst($p->page_type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Banner Size <span class="text-rose-500">*</span></label>
                    <select name="banner_size_id" x-model="sizeId" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-medium focus:bg-white focus:border-amber-500 outline-none">
                        <template x-for="sz in availableSizes" :key="sz.id">
                            <option :value="sz.id" x-text="sz.name + ' (' + sz.width + ' × ' + sz.height + ' px)'"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Current Banner & Replacement Upload -->
            <div class="pt-2 space-y-3">
                <label class="block text-xs font-bold uppercase text-slate-700">Banner Asset Graphic</label>
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                    <div class="flex items-center space-x-4">
                        <img src="{{ $campaign->banner_url }}" class="h-16 w-auto object-contain rounded border border-slate-300">
                        <div class="text-xs text-slate-600">
                            <span class="font-bold text-slate-900 block">Current Active Banner Image</span>
                            <span>Upload a new image below if replacing current banner.</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-200">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Replace Banner Image (Optional)</label>
                        <input type="file" name="banner_image" accept="image/jpeg,image/png,image/jpg" class="text-xs text-slate-700">
                        <p class="text-[11px] text-slate-500 mt-1">Max Size: <strong class="text-amber-900">5MB</strong>. Valid formats: <strong class="text-slate-900">PNG, JPEG, JPG only</strong>.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Geographic County & National Targeting -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-200 pb-2">4. County & National Targeting</h3>

            <div class="flex items-center space-x-3 p-4 bg-slate-50 rounded-xl border border-slate-300">
                <input type="checkbox" name="is_national" id="is_national" value="1" x-model="isNational" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                <label for="is_national" class="text-xs font-bold text-slate-900 cursor-pointer">
                    Target Entire Kenya (National Coverage Across All 47 Counties)
                </label>
            </div>

            <div x-show="!isNational" class="space-y-2">
                <label class="block text-xs font-bold uppercase text-slate-700">Select Target Counties</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 max-h-48 overflow-y-auto p-4 bg-slate-50 border border-slate-300 rounded-xl">
                    @foreach($counties as $c)
                        <label class="flex items-center space-x-2 text-xs text-slate-700 hover:text-slate-900 cursor-pointer py-1">
                            <input type="checkbox" name="counties[]" value="{{ $c }}" x-model="selectedCounties" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <span>{{ $c }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 5. Schedule & Featured Options -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-200 pb-2">5. Schedule & Featured Options</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Start Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $campaign->start_date->format('Y-m-d')) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">End Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', $campaign->end_date->format('Y-m-d')) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-amber-500 outline-none">
                </div>
            </div>

            <div class="flex items-center space-x-3 p-4 bg-slate-50 rounded-xl border border-slate-300">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" x-model="isFeatured" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                <div>
                    <label for="is_featured" class="text-xs font-bold text-slate-900 cursor-pointer block">
                        Enable Featured Premium Weighting (+3x Rotation Priority)
                    </label>
                    <p class="text-[11px] text-slate-600 font-medium">Featured banners get 3x higher display frequency and top priority.</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Calculated Investment Price (KES)</label>
                <input type="number" step="0.01" name="calculated_price" value="{{ old('calculated_price', $campaign->calculated_price) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-mono font-bold focus:bg-white focus:border-amber-500 outline-none">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-200 flex justify-end space-x-3">
            <a href="{{ route('admin.advertising.campaigns.show', $campaign->id) }}" class="px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition-all">Cancel</a>
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-8 py-3.5 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md flex items-center space-x-2">
                <span>💾 Save Campaign Changes</span>
            </button>
        </div>
    </form>
</div>

<script>
function adminEditCampaign() {
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
