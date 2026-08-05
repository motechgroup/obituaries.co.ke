@extends('layouts.admin')

@section('title', 'Edit Advertiser Account | Admin Panel')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Edit Advertiser Account</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Update account credentials, contact info, or account status for {{ $advertiser->business_name }}.</p>
        </div>
        <a href="{{ route('admin.advertising.advertisers.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all">
            &larr; Back to Directory
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1 font-medium">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.advertising.advertisers.update', $advertiser->id) }}" method="POST" class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Business Name <span class="text-rose-500">*</span></label>
                <input type="text" name="business_name" value="{{ old('business_name', $advertiser->business_name) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Contact Person <span class="text-rose-500">*</span></label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $advertiser->contact_person) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Phone Number <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $advertiser->phone_number) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $advertiser->email) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Reset Password <span class="text-slate-400 font-normal">(Leave blank to keep current password)</span></label>
                <input type="password" name="password" placeholder="Enter new password to reset" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Account Status <span class="text-rose-500">*</span></label>
                <select name="status" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                    <option value="active" {{ old('status', $advertiser->status) === 'active' ? 'selected' : '' }}>Active Account (Can log in & manage ads)</option>
                    <option value="suspended" {{ old('status', $advertiser->status) === 'suspended' ? 'selected' : '' }}>Suspended Account (Blocked from login)</option>
                </select>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.advertising.advertisers.index') }}" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all">Cancel</a>
            <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs uppercase tracking-wider transition-all shadow-md">
                Update Advertiser Account &rarr;
            </button>
        </div>
    </form>
</div>
@endsection
