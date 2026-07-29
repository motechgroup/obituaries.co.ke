@extends('layouts.admin')

@section('title', 'Verify Obituary: ' . $obituary->full_name)

@section('content')
<div class="space-y-8">
    <!-- Header Nav & Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
        <div>
            <a href="{{ route('admin.obituaries.index') }}" class="text-xs text-amber-700 hover:text-amber-800 font-semibold flex items-center space-x-1 mb-2">
                <span>&larr; Back to Obituaries List</span>
            </a>
            <h1 class="font-serif text-3xl font-bold text-slate-900">{{ $obituary->full_name }}</h1>
            <p class="text-slate-500 text-xs mt-1">Submitted on {{ $obituary->created_at->format('M d, Y \a\t H:i') }} &bull; Slug: <code class="bg-slate-100 px-1 py-0.5 rounded font-mono">{{ $obituary->slug }}</code></p>
        </div>

        <div class="flex items-center space-x-3">
            @if($obituary->status === 'published')
                <a href="{{ route('obituaries.show', $obituary->slug) }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all flex items-center space-x-1">
                    <span>View Live Notice</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
                <form action="{{ route('admin.obituaries.unpublish', $obituary->id) }}" method="POST" class="inline" onsubmit="return confirm('Unpublish this obituary notice from live website?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all flex items-center space-x-1">
                        <span class="material-symbols-outlined text-[16px]">visibility_off</span>
                        <span>Unpublish Notice</span>
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.obituaries.edit', $obituary->id) }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-xl text-xs font-bold transition-all">
                Edit Notice
            </a>

            <form action="{{ route('admin.obituaries.destroy', $obituary->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to PERMANENTLY delete obituary notice for {{ e($obituary->full_name) }}? This action cannot be undone!')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all flex items-center space-x-1 shadow-xs">
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                    <span>Delete Notice</span>
                </button>
            </form>
        </div>
    </div>

    @if($obituary->status === 'pending_payment')
        <div class="p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl text-xs flex items-start space-x-3">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <span class="font-bold text-sm block mb-1">M-Pesa Payment Pending</span>
                <span>The submitter completed the form but has not triggered the M-Pesa STK Push prompt yet. You can call them at <strong>{{ $obituary->submitter_phone }}</strong> or approve and publish this notice directly using the panel on the right.</span>
            </div>
        </div>
    @endif

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left 8 Cols: Deceased Details & Bio -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Details Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                <div class="flex flex-col sm:flex-row gap-6 items-start">
                    <!-- Photo -->
                    <div class="w-32 h-40 bg-slate-100 rounded-xl border border-slate-200 overflow-hidden flex-shrink-0">
                        @if($obituary->photo)
                            <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-slate-800 flex items-center justify-center text-slate-500 text-xs italic text-center p-2">
                                No Photo Uploaded
                            </div>
                        @endif
                    </div>

                    <!-- Meta details grid -->
                    <div class="flex-grow grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 font-semibold uppercase block">Date of Birth</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $obituary->date_of_birth ? $obituary->date_of_birth->format('M d, Y') : 'Not Specified' }}</span>
                        </div>

                        <div>
                            <span class="text-slate-400 font-semibold uppercase block">Date of Death</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $obituary->date_of_death ? $obituary->date_of_death->format('M d, Y') : 'Not Specified' }}</span>
                        </div>

                        <div>
                            <span class="text-slate-400 font-semibold uppercase block">Age</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $obituary->age ? $obituary->age . ' Years' : 'N/A' }}</span>
                        </div>

                        <div>
                            <span class="text-slate-400 font-semibold uppercase block">County & Town</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $obituary->town }}, {{ $obituary->county }}</span>
                        </div>

                        <div>
                            <span class="text-slate-400 font-semibold uppercase block">Funeral Date</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $obituary->funeral_date ? $obituary->funeral_date->format('M d, Y') : 'Not specified' }}</span>
                        </div>

                        <div>
                            <span class="text-slate-400 font-semibold uppercase block">Service Location</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $obituary->church_service_location ?? 'Not specified' }}</span>
                        </div>

                        <div class="sm:col-span-2">
                            <span class="text-slate-400 font-semibold uppercase block">Burial Location</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $obituary->burial_location ?? 'Not specified' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Programme PDF -->
                @if($obituary->programme_file)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                        <div class="flex items-center space-x-3 text-xs">
                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V7.5L14.5 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-bold text-slate-800">Funeral Programme PDF Uploaded</span>
                        </div>
                        <a href="{{ asset('storage/' . $obituary->programme_file) }}" target="_blank" class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-semibold hover:bg-slate-800">
                            Download / Preview PDF
                        </a>
                    </div>
                @endif

                <!-- Biography text -->
                <div>
                    <h3 class="font-serif text-lg font-bold text-slate-900 mb-2">Biography & Life Story</h3>
                    <div class="obituary-biography p-5 bg-slate-50 rounded-xl border border-slate-200 text-slate-800 text-sm leading-relaxed font-serif break-words overflow-hidden">
                        {!! \App\Helpers\StorageHelper::formatBiographyHtml($obituary->biography) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 4 Cols: Verification Workflow & Submitter Info -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Verification Status Card & Action Form -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                <h2 class="font-serif text-xl font-bold text-slate-900 border-b border-slate-200 pb-3">Admin Verification Panel</h2>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Notice Status:</span>
                        <span class="font-bold uppercase tracking-wider text-amber-700">{{ str_replace('_', ' ', $obituary->status) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Verification Status:</span>
                        <span class="font-bold capitalize text-slate-900">{{ $obituary->verification_status }}</span>
                    </div>
                    @if($obituary->verified_at)
                        <div class="flex justify-between">
                            <span class="text-slate-500">Verified By:</span>
                            <span class="font-medium text-slate-800">{{ $obituary->verifier->name ?? 'Admin' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Verified Date:</span>
                            <span class="text-slate-800">{{ $obituary->verified_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Action Form -->
                <form action="{{ route('admin.obituaries.verify', $obituary->id) }}" method="POST" class="space-y-4 pt-4 border-t border-slate-200">
                    @csrf

                    @if(Auth::guard('admin')->user()->isEditor() && $obituary->status !== 'published')
                        <div>
                            <label for="mpesa_transaction_code" class="block text-xs font-bold text-amber-900 uppercase tracking-wider mb-1">
                                M-Pesa Receipt / Transaction Code <span class="text-rose-600">* (Required for Editors)</span>
                            </label>
                            <input type="text" name="mpesa_transaction_code" id="mpesa_transaction_code" value="{{ old('mpesa_transaction_code') }}" required placeholder="e.g. QJK1234567 or RAB9876543" class="w-full px-3 py-2 bg-amber-50 border border-amber-300 rounded-xl text-xs font-mono font-bold uppercase text-amber-950 focus:ring-2 focus:ring-amber-500">
                            <p class="text-[10px] text-amber-800 mt-1">Editors cannot publish free notices. Enter a verified M-Pesa code.</p>
                        </div>
                    @endif

                    <div>
                        <label for="verification_notes" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Internal Verification Notes
                        </label>
                        <textarea name="verification_notes" id="verification_notes" rows="3" placeholder="e.g. Spoke with submitter David Cheruiyot on phone. Confirmed family approval." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-amber-500">{{ old('verification_notes', $obituary->verification_notes) }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <button type="submit" name="action" value="approve" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition-all shadow-md flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Approve & Publish Notice</span>
                        </button>

                        <button type="submit" name="action" value="reject" onclick="return confirm('Are you sure you want to reject this obituary submission?')" class="w-full py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs uppercase tracking-wider transition-colors border border-rose-200 flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>Reject Notice</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Submitter Details Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4 text-xs">
                <h3 class="font-serif text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">Submitter Contact Details</h3>

                <div>
                    <span class="text-slate-400 font-semibold uppercase block">Submitter Name</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $obituary->submitter_name }}</span>
                </div>

                <div>
                    <span class="text-slate-400 font-semibold uppercase block">Phone Number</span>
                    <a href="tel:{{ $obituary->submitter_phone }}" class="font-bold text-amber-700 text-sm hover:underline flex items-center space-x-1.5 mt-0.5">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span>{{ $obituary->submitter_phone }} (Click to Call)</span>
                    </a>
                </div>

                <div>
                    <span class="text-slate-400 font-semibold uppercase block">Relationship</span>
                    <span class="font-semibold text-slate-800">{{ $obituary->relationship }}</span>
                </div>

                <div>
                    <span class="text-slate-400 font-semibold uppercase block">Email Address</span>
                    <span class="text-slate-800">{{ $obituary->submitter_email ?? 'None provided' }}</span>
                </div>

                <div class="pt-2">
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-900 font-medium flex items-center space-x-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Family Permission Confirmed</span>
                    </div>
                </div>
            </div>

            <!-- Payment Info Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-3 text-xs">
                <h3 class="font-serif text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">Payment Details</h3>

                @if($obituary->latestPayment)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Receipt No:</span>
                        <span class="font-mono font-bold text-slate-900">{{ $obituary->latestPayment->mpesa_receipt_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Amount:</span>
                        <span class="font-bold text-slate-900">KES {{ number_format($obituary->latestPayment->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Payment Status:</span>
                        <span class="font-bold capitalize text-emerald-700">{{ $obituary->latestPayment->status }}</span>
                    </div>
                @else
                    <p class="text-slate-400">No payment record completed yet.</p>
                @endif
            </div>

            <!-- Delete Form -->
            <form action="{{ route('admin.obituaries.destroy', $obituary->id) }}" method="POST" onsubmit="return confirm('Permanently delete this obituary record?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-2 bg-slate-100 hover:bg-rose-50 text-slate-500 hover:text-rose-700 rounded-xl text-xs font-semibold transition-colors">
                    Delete Obituary Record
                </button>
            </form>

        </div>
    </div>
</div>
@endsection
