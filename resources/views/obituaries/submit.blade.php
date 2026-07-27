@extends('layouts.app')

@section('title', 'Submit Obituary | Obituaries.co.ke')

@section('content')
<!-- Header Banner -->
<div class="bg-slate-900 text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-xs font-semibold uppercase tracking-widest text-amber-400 block mb-2">Notice Publishing</span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold mb-3">Submit an Obituary Notice</h1>
        <p class="text-slate-300 text-sm sm:text-base max-w-xl mx-auto">
            Honoring your loved one with a lasting digital tribute. Fill in the details below to publish across Kenya.
        </p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="submissionForm()">
    <!-- Step Indicator Progress Bar -->
    <div class="mb-10">
        <div class="flex items-center justify-between relative mb-4">
            <!-- Step 1 Indicator -->
            <div class="flex flex-col items-center z-10">
                <button type="button" @click="goToStep(1)" :class="step >= 1 ? 'bg-slate-900 text-amber-400 border-amber-500' : 'bg-white text-slate-400 border-slate-300'" class="w-10 h-10 rounded-full border-2 flex items-center justify-center font-bold text-sm transition-all duration-200">
                    1
                </button>
                <span class="text-xs font-semibold mt-2 text-slate-700">Deceased Info</span>
            </div>

            <!-- Connector Line 1 -->
            <div class="flex-grow h-1 mx-2 rounded transition-all duration-300" :class="step >= 2 ? 'bg-slate-900' : 'bg-slate-200'"></div>

            <!-- Step 2 Indicator -->
            <div class="flex flex-col items-center z-10">
                <button type="button" @click="goToStep(2)" :class="step >= 2 ? 'bg-slate-900 text-amber-400 border-amber-500' : 'bg-white text-slate-400 border-slate-300'" class="w-10 h-10 rounded-full border-2 flex items-center justify-center font-bold text-sm transition-all duration-200">
                    2
                </button>
                <span class="text-xs font-semibold mt-2 text-slate-700">Funeral Details</span>
            </div>

            <!-- Connector Line 2 -->
            <div class="flex-grow h-1 mx-2 rounded transition-all duration-300" :class="step >= 3 ? 'bg-slate-900' : 'bg-slate-200'"></div>

            <!-- Step 3 Indicator -->
            <div class="flex flex-col items-center z-10">
                <button type="button" @click="goToStep(3)" :class="step >= 3 ? 'bg-slate-900 text-amber-400 border-amber-500' : 'bg-white text-slate-400 border-slate-300'" class="w-10 h-10 rounded-full border-2 flex items-center justify-center font-bold text-sm transition-all duration-200">
                    3
                </button>
                <span class="text-xs font-semibold mt-2 text-slate-700">Submitter Info</span>
            </div>
        </div>
    </div>

    <!-- Submission Card Form -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="p-6 bg-rose-50 border-b border-rose-200 text-rose-800 text-sm">
                <div class="font-semibold mb-2 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Please correct the errors below to proceed:</span>
                </div>
                <ul class="list-disc pl-8 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('obituaries.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-10 space-y-8">
            @csrf

            <!-- STEP 1: DECEASED INFORMATION -->
            <div x-show="step === 1" class="space-y-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="border-b border-slate-200 pb-4">
                    <h2 class="font-serif text-2xl font-bold text-slate-900">Step 1: Deceased Information</h2>
                    <p class="text-slate-500 text-xs mt-1">Provide basic personal details of the deceased loved one.</p>
                </div>

                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Full Name of Deceased <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required placeholder="e.g. Mzee John Kamau Njoroge" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Dates Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="date_of_birth" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Date of Birth <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    <div>
                        <label for="date_of_death" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Date of Death <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="date_of_death" id="date_of_death" value="{{ old('date_of_death') }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>

                <!-- County & Town -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="county" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            County <span class="text-rose-500">*</span>
                        </label>
                        <select name="county" id="county" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Select County</option>
                            @foreach($counties as $c)
                                <option value="{{ $c }}" {{ old('county') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="town" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Town / Sub-County <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="town" id="town" value="{{ old('town') }}" required placeholder="e.g. Westlands, Nakuru Town, Eldoret" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>

                <!-- Photo Upload -->
                <div>
                    <label for="photo" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Profile Portrait Photo (Optional)
                    </label>
                    <div class="mt-1 flex items-center space-x-4 p-4 bg-slate-50 border border-dashed border-slate-300 rounded-xl">
                        <svg class="w-10 h-10 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div class="flex-grow text-xs text-slate-600">
                            <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200">
                            <span class="text-[11px] text-slate-400 block mt-1">JPEG, PNG or WEBP (Max 5MB)</span>
                        </div>
                    </div>
                </div>

                <!-- Biography -->
                <div>
                    <label for="biography" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Biography / Life Tribute Announcement <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="biography" id="biography" rows="6" required placeholder="Write a respectful summary of their life journey, survivors, career, and legacy..." class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 leading-relaxed">{{ old('biography') }}</textarea>
                </div>

                <!-- Step 1 Nav -->
                <div class="pt-4 flex justify-end">
                    <button type="button" @click="nextStep(2)" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-sm transition-all flex items-center space-x-2">
                        <span>Continue to Step 2 (Funeral Details)</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- STEP 2: FUNERAL INFORMATION -->
            <div x-show="step === 2" class="space-y-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" x-cloak>
                <div class="border-b border-slate-200 pb-4">
                    <h2 class="font-serif text-2xl font-bold text-slate-900">Step 2: Funeral & Service Details</h2>
                    <p class="text-slate-500 text-xs mt-1">Provide funeral arrangements so friends and family can pay their respects.</p>
                </div>

                <!-- Funeral Date -->
                <div>
                    <label for="funeral_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Funeral / Burial Date (Optional)
                    </label>
                    <input type="date" name="funeral_date" id="funeral_date" value="{{ old('funeral_date') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Church Service Location -->
                <div>
                    <label for="church_service_location" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Church / Funeral Service Location (Optional)
                    </label>
                    <input type="text" name="church_service_location" id="church_service_location" value="{{ old('church_service_location') }}" placeholder="e.g. AIC Milimani, All Saints Cathedral, St. Stephen's" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Burial Location -->
                <div>
                    <label for="burial_location" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Burial Ground / Final Resting Place (Optional)
                    </label>
                    <input type="text" name="burial_location" id="burial_location" value="{{ old('burial_location') }}" placeholder="e.g. Family Home Kapseret, Lang'ata Cemetery, Limuru Memorial Gardens" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Programme PDF Upload -->
                <div>
                    <label for="programme_file" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Funeral Programme PDF Document (Optional)
                    </label>
                    <div class="mt-1 flex items-center space-x-4 p-4 bg-slate-50 border border-dashed border-slate-300 rounded-xl">
                        <svg class="w-10 h-10 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div class="flex-grow text-xs text-slate-600">
                            <input type="file" name="programme_file" id="programme_file" accept="application/pdf" class="block w-full text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200">
                            <span class="text-[11px] text-slate-400 block mt-1">PDF file format only (Max 10MB)</span>
                        </div>
                    </div>
                </div>

                <!-- Step 2 Nav -->
                <div class="pt-4 flex items-center justify-between">
                    <button type="button" @click="goToStep(1)" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition-colors">
                        Back to Step 1
                    </button>
                    <button type="button" @click="nextStep(3)" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-sm transition-all flex items-center space-x-2">
                        <span>Continue to Step 3 (Submitter Info)</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- STEP 3: SUBMITTER INFORMATION & CONSENT -->
            <div x-show="step === 3" class="space-y-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" x-cloak>
                <div class="border-b border-slate-200 pb-4">
                    <h2 class="font-serif text-2xl font-bold text-slate-900">Step 3: Submitter Details & Verification</h2>
                    <p class="text-slate-500 text-xs mt-1">Used solely by our administrative team to confirm authorization prior to publishing.</p>
                </div>

                <!-- Submitter Full Name -->
                <div>
                    <label for="submitter_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Your Full Name (Submitter) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="submitter_name" id="submitter_name" value="{{ old('submitter_name') }}" required placeholder="e.g. David Cheruiyot" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Phone Number & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="submitter_phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            M-Pesa Contact Phone Number <span class="text-rose-500">*</span>
                        </label>
                        <input type="tel" name="submitter_phone" id="submitter_phone" value="{{ old('submitter_phone') }}" required placeholder="e.g. 0712345678 or 254712345678" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <span class="text-[11px] text-slate-400 block mt-1">This number will also receive the STK push prompt.</span>
                    </div>

                    <div>
                        <label for="submitter_email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Email Address (Optional)
                        </label>
                        <input type="email" name="submitter_email" id="submitter_email" value="{{ old('submitter_email') }}" placeholder="your.email@example.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>

                <!-- Relationship -->
                <div>
                    <label for="relationship" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Relationship to Deceased <span class="text-rose-500">*</span>
                    </label>
                    <select name="relationship" id="relationship" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Select Relationship</option>
                        @foreach($relationships as $rel)
                            <option value="{{ $rel }}" {{ old('relationship') == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Permission Checkbox -->
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-200">
                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input type="checkbox" name="family_permission_confirmed" value="1" required class="mt-1 w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500">
                        <span class="text-xs text-amber-950 leading-relaxed font-medium">
                            I confirm that I have permission from the family to submit this obituary notice to Obituaries.co.ke.
                        </span>
                    </label>
                </div>

                <!-- Summary Box -->
                <div class="p-4 bg-slate-900 text-white rounded-xl text-xs flex items-center justify-between">
                    <div>
                        <span class="font-bold text-amber-400 uppercase tracking-wider block mb-0.5">Basic Notice Package</span>
                        <span class="text-slate-300">Standard Obituary Notice Publishing</span>
                    </div>
                    <div class="text-right">
                        <span class="font-serif text-lg font-bold text-white">KES 500</span>
                        <span class="text-[10px] text-slate-400 block">Via M-Pesa STK Push</span>
                    </div>
                </div>

                <!-- Step 3 Nav & Final Submit -->
                <div class="pt-4 flex items-center justify-between">
                    <button type="button" @click="goToStep(2)" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition-colors">
                        Back to Step 2
                    </button>
                    <button type="submit" class="px-8 py-3.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-sm transition-all shadow-lg shadow-amber-600/30 flex items-center space-x-2">
                        <span>Proceed to M-Pesa Payment (KES 500)</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function submissionForm() {
        return {
            step: 1,
            goToStep(targetStep) {
                this.step = targetStep;
            },
            nextStep(next) {
                this.step = next;
                window.scrollTo({ top: 200, behavior: 'smooth' });
            }
        }
    }
</script>
@endsection
