@extends('layouts.advertiser')

@section('title', 'Business Profile Setup | Advertiser Portal')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Business Profile Setup</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Provide complete business details to display on your sponsored banners and advertiser directory listing.</p>
        </div>
        <a href="{{ route('advertiser.dashboard') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all">
            &larr; Back to Dashboard
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1 font-medium">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('advertiser.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 space-y-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Business Name <span class="text-rose-500">*</span></label>
                <input type="text" name="business_name" value="{{ old('business_name', $profile->business_name) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Business Category <span class="text-rose-500">*</span></label>
                <select name="business_category_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                    <option value="">Select Category...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('business_category_id', $profile->business_category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Primary County Location <span class="text-rose-500">*</span></label>
                <select name="county" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                    <option value="">Select County...</option>
                    @foreach($counties as $c)
                        <option value="{{ $c }}" {{ old('county', $profile->county) === $c ? 'selected' : '' }}>{{ $c }} County</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Phone Number <span class="text-rose-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">WhatsApp Number <span class="text-slate-400 lowercase font-normal">(optional)</span></label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $profile->whatsapp) }}" placeholder="e.g. 254722112233" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Business Email Address</label>
                <input type="email" name="email" value="{{ old('email', $profile->email) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Official Website URL <span class="text-slate-400 lowercase font-normal">(optional)</span></label>
                <input type="url" name="website" value="{{ old('website', $profile->website) }}" placeholder="https://yourbusiness.co.ke" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Google Maps Link <span class="text-slate-400 lowercase font-normal">(optional)</span></label>
                <input type="text" name="google_maps_link" value="{{ old('google_maps_link', $profile->google_maps_link) }}" placeholder="https://maps.google.com/?q=..." class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Physical Address / Location</label>
                <input type="text" name="address" value="{{ old('address', $profile->address) }}" placeholder="e.g. Argwings Kodhek Road, Opposite KNH, Nairobi" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Business Description & Services <span class="text-rose-500">*</span></label>
                <textarea name="description" rows="4" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none leading-relaxed transition-all">{{ old('description', $profile->description) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Business Logo Upload</label>
                <div class="p-4 bg-slate-50 border border-slate-300 rounded-xl flex items-center space-x-4">
                    @if($profile->logo)
                        <img src="{{ asset('storage/' . $profile->logo) }}" class="w-12 h-12 rounded-xl object-cover border border-slate-300 bg-white">
                    @endif
                    <input type="file" name="logo" accept="image/*" class="text-xs text-slate-700 font-medium">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-6 py-3.5 rounded-xl text-xs uppercase tracking-wider transition-all shadow-sm">
                Save Business Profile Details
            </button>
        </div>
    </form>
</div>
@endsection
