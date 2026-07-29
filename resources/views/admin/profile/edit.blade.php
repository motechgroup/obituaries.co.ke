@extends('layouts.admin')

@section('title', 'Manage Account Profile | Obituaries.co.ke Admin')

@section('content')
<div class="space-y-8 w-full">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-slate-900">Manage Admin Profile</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Update your account information, profile avatar, and security credentials.</p>
        </div>
        <div>
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-500/10 text-amber-800 border border-amber-500/20 shadow-2xs">
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
                <span class="font-semibold">{{ session('success') }}</span>
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Profile Summary & Stats Card (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm text-center space-y-5">
                <!-- Avatar Display -->
                <div class="relative w-32 h-32 mx-auto rounded-full overflow-hidden bg-slate-900 border-4 border-amber-500/30 shadow-lg flex items-center justify-center">
                    @if($admin->avatar)
                        <img src="{{ asset('storage/' . $admin->avatar) }}" alt="{{ $admin->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-slate-900 to-slate-950 flex items-center justify-center text-3xl font-bold font-serif text-amber-400 uppercase tracking-widest">
                            {{ strtoupper(substr($admin->name, 0, 2)) }}
                        </div>
                    @endif
                </div>

                <div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $admin->name }}</h2>
                    <p class="text-xs text-slate-500 font-mono mt-1 break-all">{{ $admin->email }}</p>
                </div>

                <div class="pt-4 border-t border-slate-100 grid grid-cols-2 gap-3 text-center">
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                        <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-wider">Role</span>
                        <span class="text-xs font-bold text-slate-800 uppercase mt-0.5 block">{{ $admin->role ?? 'Admin' }}</span>
                    </div>
                    <div class="bg-amber-50 p-3.5 rounded-xl border border-amber-200">
                        <span class="text-[10px] text-amber-700 block font-bold uppercase tracking-wider">Verified</span>
                        <span class="text-xs font-extrabold text-amber-900 mt-0.5 block">{{ number_format($verifiedCount ?? 0) }} Notices</span>
                    </div>
                </div>

                <div class="text-xs text-slate-500 pt-3 border-t border-slate-100 font-medium">
                    <span>Member since {{ $admin->created_at ? $admin->created_at->format('M Y') : 'N/A' }}</span>
                </div>
            </div>

            <!-- Account Security Tip -->
            <div class="bg-slate-950 text-slate-200 rounded-2xl p-6 border border-slate-800 shadow-sm space-y-3 text-xs">
                <div class="flex items-center space-x-2 text-amber-400 font-bold text-sm">
                    <span class="material-symbols-outlined text-[18px]">lock</span>
                    <span>Account Security Tip</span>
                </div>
                <p class="text-slate-400 leading-relaxed">
                    Ensure your admin password contains uppercase letters, numbers, and special characters. Never share your credentials with unauthorized users.
                </p>
            </div>
        </div>

        <!-- Right Column: Profile Edit Form (8 cols) -->
        <div class="lg:col-span-8">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-8">
                @csrf
                @method('PUT')

                <!-- Personal Info Section -->
                <div class="space-y-6">
                    <div class="pb-3 border-b border-slate-200 flex items-center space-x-2">
                        <span class="material-symbols-outlined text-amber-600 text-[20px]">person</span>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800">Personal Information</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Full Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email Address <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $admin->phone) }}" placeholder="e.g. 0712345678" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>

                        <div>
                            <label for="avatar" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Profile Photo / Avatar</label>
                            <input type="file" name="avatar" id="avatar" accept="image/*" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-900 hover:file:bg-amber-200">
                            <span class="text-[11px] text-slate-400 mt-1.5 block">Supported: JPG, PNG, WEBP. Max size: 2MB.</span>
                        </div>
                    </div>
                </div>

                <!-- Password Update Section -->
                <div class="space-y-6 pt-6 border-t border-slate-200">
                    <div class="pb-3 border-b border-slate-200 flex items-center space-x-2">
                        <span class="material-symbols-outlined text-amber-600 text-[20px]">lock_reset</span>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800">Change Password (Optional)</h3>
                    </div>

                    <p class="text-xs text-slate-500">Leave password fields empty if you do not wish to change your current password.</p>

                    <div class="space-y-5">
                        <div>
                            <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Current Password</label>
                            <input type="password" name="current_password" id="current_password" placeholder="Enter current password to verify" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">New Password</label>
                                <input type="password" name="password" id="password" placeholder="Min 6 characters" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Confirm New Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Re-enter new password" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Action Buttons -->
                <div class="pt-6 border-t border-slate-200 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="px-6 py-3.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3.5 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-md transition-all flex items-center space-x-2 border border-amber-700 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Save Profile Changes</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
