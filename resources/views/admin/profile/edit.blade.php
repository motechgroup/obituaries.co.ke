@extends('layouts.admin')

@section('title', 'Manage Account Profile | Obituaries.co.ke Admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-slate-900">Manage Admin Profile</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Update your personal account information, avatar photo, and security credentials.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                <span class="w-2 h-2 rounded-full bg-amber-500 mr-2 animate-pulse"></span>
                {{ ucfirst(str_replace('_', ' ', $admin->role ?? 'admin')) }} Account
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-5 py-4 rounded-2xl text-xs sm:text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-900 px-5 py-4 rounded-2xl text-xs sm:text-sm space-y-1 shadow-xs">
            <div class="font-bold mb-1 flex items-center space-x-2 text-rose-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Please fix the following validation errors:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-800">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Profile Summary & Stats Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs text-center space-y-4">
                <!-- Avatar Display -->
                <div class="relative w-28 h-28 mx-auto rounded-full overflow-hidden bg-slate-900 border-4 border-amber-500/20 shadow-md">
                    @if($admin->avatar)
                        <img src="{{ asset('storage/' . $admin->avatar) }}" alt="{{ $admin->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-3xl font-bold font-serif text-amber-400 uppercase">
                            {{ strtoupper(substr($admin->name, 0, 2)) }}
                        </div>
                    @endif
                </div>

                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $admin->name }}</h2>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $admin->email }}</p>
                </div>

                <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-4 text-center">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-xs text-slate-400 block font-semibold uppercase">Role</span>
                        <span class="text-xs font-bold text-slate-800 uppercase">{{ $admin->role ?? 'Admin' }}</span>
                    </div>
                    <div class="bg-amber-50 p-3 rounded-xl border border-amber-100">
                        <span class="text-xs text-amber-600 block font-semibold uppercase">Verified</span>
                        <span class="text-sm font-bold text-amber-900">{{ number_format($verifiedCount ?? 0) }} Notices</span>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                    <span>Member since {{ $admin->created_at ? $admin->created_at->format('M Y') : 'N/A' }}</span>
                </div>
            </div>

            <!-- Quick Security Info -->
            <div class="bg-slate-900 text-slate-200 rounded-2xl p-6 border border-slate-800 space-y-3 text-xs">
                <div class="flex items-center space-x-2 text-amber-400 font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Account Security Tip</span>
                </div>
                <p class="text-slate-400 leading-relaxed">
                    Ensure your admin password uses a combination of uppercase letters, numbers, and symbols. Never share your administrative login credentials.
                </p>
            </div>
        </div>

        <!-- Right Column: Profile Edit Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-8">
                @csrf
                @method('PUT')

                <!-- Personal Info Section -->
                <div class="space-y-6">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-amber-600 pb-2 border-b border-slate-100 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Personal Information</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                        <div>
                            <label for="name" class="block font-semibold uppercase text-slate-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                        </div>

                        <div>
                            <label for="email" class="block font-semibold uppercase text-slate-700 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                        </div>

                        <div>
                            <label for="phone" class="block font-semibold uppercase text-slate-700 mb-1.5">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $admin->phone) }}" placeholder="e.g. 0712345678" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div>
                            <label for="avatar" class="block font-semibold uppercase text-slate-700 mb-1.5">Profile Photo / Avatar</label>
                            <input type="file" name="avatar" id="avatar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                            <span class="text-[10px] text-slate-400 mt-1 block">Supported: JPG, PNG, WEBP. Max size: 2MB.</span>
                        </div>
                    </div>
                </div>

                <!-- Password Update Section -->
                <div class="space-y-6 pt-4 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-amber-600 pb-2 border-b border-slate-100 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Change Password (Optional)</span>
                    </h3>

                    <p class="text-xs text-slate-500">Leave password fields empty if you do not wish to change your current password.</p>

                    <div class="space-y-4 text-xs max-w-lg">
                        <div>
                            <label for="current_password" class="block font-semibold uppercase text-slate-700 mb-1.5">Current Password</label>
                            <input type="password" name="current_password" id="current_password" placeholder="Enter current password to verify" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block font-semibold uppercase text-slate-700 mb-1.5">New Password</label>
                                <input type="password" name="password" id="password" placeholder="Min 6 characters" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>

                            <div>
                                <label for="password_confirmation" class="block font-semibold uppercase text-slate-700 mb-1.5">Confirm New Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Re-enter new password" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Action Buttons -->
                <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Save Profile Changes</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
