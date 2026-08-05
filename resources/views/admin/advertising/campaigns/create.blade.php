@extends('layouts.admin')

@section('title', 'Create & Place Ad Campaign | Admin Panel')

@section('content')
<div x-data="adminCampaignWizard()" class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Create & Place Ad Campaign</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Directly create, price, target, and publish banner advertisements on Obituaries.co.ke.</p>
        </div>
        <a href="{{ route('admin.advertising.campaigns.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 inline-flex items-center space-x-1">
            <span>&larr; Back to Ad Campaigns</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.advertising.campaigns.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 space-y-8 shadow-sm">
        @csrf

        <!-- 1. Advertiser Selection & Status -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 border-b border-slate-200 pb-2">1. Advertiser & Publication Status</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Select Advertiser Account <span class="text-rose-500">*</span></label>
                    <select name="advertiser_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-medium focus:bg-white focus:border-amber-500 outline-none">
                        @foreach($advertisers as $adv)
                            <option value="{{ $adv->id }}">{{ $adv->business_name }} ({{ $adv->contact_person }} • {{ $adv->phone_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Initial Publication Status <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-extrabold focus:bg-white focus:border-amber-500 outline-none">
                        <option value="running" selected>🟢 RUNNING (Approve & Publish Immediately)</option>
                        <option value="pending_approval">🟡 PENDING APPROVAL (Hold for Review)</option>
                        <option value="draft">⚪ DRAFT (Saved Draft)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Campaign Name & Landing Link -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 border-b border-slate-200 pb-2">2. Campaign Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Campaign Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Lee Funeral Home Premier Care Ad" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Landing URL / Website Link <span class="text-slate-400 font-normal lowercase">(optional)</span></label>
                    <input type="url" name="landing_url" value="{{ old('landing_url') }}" placeholder="https://example.co.ke (Optional)" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-amber-500 outline-none">
                    <span class="text-[10px] text-slate-500 mt-1 block">Leave blank for a display-only branding banner without website redirection.</span>
                </div>
            </div>
        </div>

        <!-- 3. Ad Placement & Banner Size Selection -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 border-b border-slate-200 pb-2">3. Placement Slot & Banner Dimension</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Placement Slot <span class="text-rose-500">*</span></label>
                    <select name="ad_placement_id" x-model="placementId" @change="updateSizes(); calculatePrice()" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-medium focus:bg-white focus:border-amber-500 outline-none">
                        <option value="">Select Placement Slot...</option>
                        @foreach($placements as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ ucfirst($p->page_type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Banner Size <span class="text-rose-500">*</span></label>
                    <select name="banner_size_id" x-model="sizeId" @change="calculatePrice()" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-medium focus:bg-white focus:border-amber-500 outline-none">
                        <option value="">Select Placement First...</option>
                        <template x-for="sz in availableSizes" :key="sz.id">
                            <option :value="sz.id" x-text="sz.name + ' (' + sz.width + ' × ' + sz.height + ' px)'"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Banner Upload Field -->
            <div class="pt-2">
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Upload Banner Image File <span class="text-rose-500">*</span></label>
                <div class="p-4 bg-slate-50 border border-slate-300 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <input type="file" name="banner_image" accept="image/jpeg,image/png,image/jpg" required class="text-xs text-slate-700">
                    <p class="text-[11px] text-slate-600 font-medium">Max Size: <strong class="text-amber-800">5MB</strong>. Valid formats: <strong class="text-slate-900">PNG, JPEG, JPG only</strong>.</p>
                </div>
            </div>
        </div>

        <!-- 4. Geographic County & National Targeting -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 border-b border-slate-200 pb-2">4. County & National Targeting</h3>

            <div class="flex items-center space-x-3 p-4 bg-slate-50 rounded-xl border border-slate-300">
                <input type="checkbox" name="is_national" id="is_national" value="1" x-model="isNational" @change="calculatePrice()" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                <label for="is_national" class="text-xs font-bold text-slate-900 cursor-pointer">
                    Target Entire Kenya (National Coverage Across All 47 Counties)
                </label>
            </div>

            <div x-show="!isNational" class="space-y-2">
                <label class="block text-xs font-bold uppercase text-slate-700">Select Target Counties</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 max-h-48 overflow-y-auto p-4 bg-slate-50 border border-slate-300 rounded-xl">
                    @foreach($counties as $c)
                        <label class="flex items-center space-x-2 text-xs text-slate-700 hover:text-slate-900 cursor-pointer py-1">
                            <input type="checkbox" name="counties[]" value="{{ $c }}" x-model="selectedCounties" @change="calculatePrice()" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <span>{{ $c }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 5. Schedule & Featured Options -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 border-b border-slate-200 pb-2">5. Schedule & Featured Options</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Start Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" x-model="startDate" @change="calculatePrice()" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">End Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" x-model="endDate" @change="calculatePrice()" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-amber-500 outline-none">
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

        <!-- Price Summary & Submit -->
        <div class="p-6 bg-amber-500/10 border border-amber-300/40 rounded-2xl space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-900">Calculated Investment Value</span>
                <span class="text-2xl font-extrabold text-amber-900 font-mono" x-text="'KES ' + priceData.total_price.toLocaleString()">KES 0</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs text-slate-700 font-medium pt-2 border-t border-amber-200">
                <div>Duration: <strong class="text-slate-900" x-text="priceData.total_days + ' Days'">1 Day</strong></div>
                <div>Daily Rate: <strong class="text-slate-900" x-text="'KES ' + priceData.daily_rate">KES 0</strong></div>
                <div>Subtotal: <strong class="text-slate-900" x-text="'KES ' + priceData.subtotal">KES 0</strong></div>
                <div>Featured Fee: <strong class="text-slate-900" x-text="'KES ' + priceData.featured_fee">KES 0</strong></div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-200 flex justify-end">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-8 py-4 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md flex items-center space-x-2">
                <span>🚀 Create & Place Ad Banner Now</span>
            </button>
        </div>
    </form>
</div>

<script>
function adminCampaignWizard() {
    const placements = @json($placements);

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
            }
        },

        updateSizes() {
            const p = placements.find(x => x.id == this.placementId);
            if (p && p.banner_sizes) {
                this.availableSizes = p.banner_sizes;
                if (this.availableSizes.length > 0) {
                    this.sizeId = this.availableSizes[0].id;
                }
            } else {
                this.availableSizes = [];
                this.sizeId = '';
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
