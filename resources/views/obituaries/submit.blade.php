@extends('layouts.app')

@section('title', 'Submit Obituary | Obituaries.co.ke')

@section('content')
<!-- Header Banner -->
<div class="bg-primary text-on-primary py-10 sm:py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-widest text-secondary-fixed block mb-1.5">Notice Publishing</span>
        <h1 class="font-serif text-2xl sm:text-4xl font-bold mb-2">Submit an Obituary Notice</h1>
        <p class="text-primary-fixed/70 text-xs sm:text-base max-w-xl mx-auto">
            Honoring your loved one with a lasting digital tribute. Fill in the details below to publish across Kenya.
        </p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12" x-data="{ step: 1, goToStep(targetStep) { this.step = targetStep; }, nextStep(next) { this.step = next; window.scrollTo({ top: 180, behavior: 'smooth' }); } }">
    <!-- Step Indicator Progress Bar -->
    <div class="mb-8 sm:mb-10">
        <div class="flex items-center justify-between relative mb-2">
            <!-- Step 1 Indicator -->
            <div class="flex flex-col items-center z-10">
                <button type="button" @click="goToStep(1)" :class="step >= 1 ? 'bg-primary text-secondary-fixed border-secondary' : 'bg-surface text-on-surface-variant/40 border-outline-variant'" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border-2 flex items-center justify-center font-bold text-xs sm:text-sm transition-all duration-200">
                    1
                </button>
                <span class="text-[10px] sm:text-xs font-semibold mt-1.5 text-on-surface">Deceased Info</span>
            </div>

            <!-- Connector Line 1 -->
            <div class="flex-grow h-1 mx-2 rounded transition-all duration-300" :class="step >= 2 ? 'bg-primary' : 'bg-surface-container-high'"></div>

            <!-- Step 2 Indicator -->
            <div class="flex flex-col items-center z-10">
                <button type="button" @click="goToStep(2)" :class="step >= 2 ? 'bg-primary text-secondary-fixed border-secondary' : 'bg-surface text-on-surface-variant/40 border-outline-variant'" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border-2 flex items-center justify-center font-bold text-xs sm:text-sm transition-all duration-200">
                    2
                </button>
                <span class="text-[10px] sm:text-xs font-semibold mt-1.5 text-on-surface">Funeral Details</span>
            </div>

            <!-- Connector Line 2 -->
            <div class="flex-grow h-1 mx-2 rounded transition-all duration-300" :class="step >= 3 ? 'bg-primary' : 'bg-surface-container-high'"></div>

            <!-- Step 3 Indicator -->
            <div class="flex flex-col items-center z-10">
                <button type="button" @click="goToStep(3)" :class="step >= 3 ? 'bg-primary text-secondary-fixed border-secondary' : 'bg-surface text-on-surface-variant/40 border-outline-variant'" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border-2 flex items-center justify-center font-bold text-xs sm:text-sm transition-all duration-200">
                    3
                </button>
                <span class="text-[10px] sm:text-xs font-semibold mt-1.5 text-on-surface">Submitter Info</span>
            </div>
        </div>
    </div>

    <!-- Submission Card Form -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-xl border border-outline-variant/30 overflow-hidden">
        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="p-4 sm:p-6 bg-rose-50 border-b border-rose-200 text-rose-800 text-xs sm:text-sm">
                <div class="font-semibold mb-1.5 flex items-center space-x-2">
                    <span class="material-symbols-outlined text-rose-600 text-[20px]">error</span>
                    <span>Please correct the errors below to proceed:</span>
                </div>
                <ul class="list-disc pl-6 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('obituaries.store') }}" method="POST" enctype="multipart/form-data" class="p-5 sm:p-10 space-y-6 sm:space-y-8">
            @csrf

            <!-- STEP 1: DECEASED INFORMATION -->
            <div x-show="step === 1" class="space-y-5 sm:space-y-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="border-b border-outline-variant/30 pb-3 sm:pb-4">
                    <h2 class="font-serif text-xl sm:text-2xl font-bold text-primary">Step 1: Deceased Information</h2>
                    <p class="text-on-surface-variant/70 text-xs mt-1">Provide basic personal details of the deceased loved one.</p>
                </div>

                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                        Full Name of Deceased <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required placeholder="e.g. Mzee John Kamau Njoroge" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface text-sm focus:outline-none focus:border-primary">
                </div>

                <!-- Dates Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="date_of_birth" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                            Date of Birth <span class="text-on-surface-variant/60 font-normal lowercase">(optional)</span>
                        </label>
                        <input type="text" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" placeholder="DD/MM/YYYY (e.g. 15/04/1945 or 1945)" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface text-sm focus:outline-none focus:border-primary">
                        <span class="text-[10px] text-on-surface-variant/70 mt-1 block">Format: DD/MM/YYYY or 4-digit Year (e.g. 15/04/1945 or 1945). Leave blank if unknown.</span>
                    </div>

                    <div>
                        <label for="date_of_death" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                            Date of Death <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="date_of_death" id="date_of_death" value="{{ old('date_of_death') }}" required placeholder="e.g. 2026 or 28/07/2026" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface text-sm focus:outline-none focus:border-primary">
                        <span class="text-[10px] text-on-surface-variant/70 mt-1 block">Format: DD/MM/YYYY or 4-digit Year (e.g., <strong>2026</strong>).</span>
                    </div>
                </div>

                <!-- County & Town -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="county" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                            County <span class="text-rose-500">*</span>
                        </label>
                        <select name="county" id="county" required class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface text-sm focus:outline-none focus:border-primary">
                            <option value="">Select County</option>
                            @foreach($counties as $c)
                                <option value="{{ $c }}" {{ old('county') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="town" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                            Town / Sub-County <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="town" id="town" value="{{ old('town') }}" required placeholder="e.g. Westlands, Nakuru Town, Eldoret" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface text-sm focus:outline-none focus:border-primary">
                    </div>
                </div>

                <!-- Photo Upload -->
                <div>
                    <label for="photo" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                        Profile Portrait Photo (Optional)
                    </label>
                    <div class="p-3.5 sm:p-4 bg-surface-container-low border border-dashed border-outline-variant rounded-xl flex items-center space-x-3">
                        <span class="material-symbols-outlined text-on-surface-variant text-[28px] sm:text-[32px]">photo_camera</span>
                        <div class="flex-grow text-xs">
                            <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-xs text-on-surface-variant file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-secondary-fixed file:text-on-secondary-fixed">
                            <span class="text-[10px] text-on-surface-variant/60 block mt-1">JPEG, PNG or WEBP (Max 5MB)</span>
                        </div>
                    </div>
                </div>

                <!-- Moments in Time Gallery Upload -->
                <div>
                    <label for="gallery_images" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                        Moments in Time Photo Gallery (Add up to 5 photos)
                    </label>
                    <div class="p-3.5 sm:p-4 bg-surface-container-low border border-dashed border-outline-variant rounded-xl flex items-center space-x-3">
                        <span class="material-symbols-outlined text-on-surface-variant text-[28px] sm:text-[32px]">collections</span>
                        <div class="flex-grow text-xs">
                            <input type="file" name="gallery_images[]" id="gallery_images" multiple accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-xs text-on-surface-variant file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-secondary-fixed file:text-on-secondary-fixed">
                            <span class="text-[10px] text-on-surface-variant/60 block mt-1">Upload up to 5 family photos, wedding moments, career memories, etc. (Max 5MB each)</span>
                        </div>
                    </div>
                </div>

                <!-- Biography with Formatting Toolbar -->
                <div x-data="biographyEditor(`{!! addslashes(old('biography', '')) !!}`)">
                    <div class="flex items-center justify-between mb-2">
                        <label for="biography" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider">
                            Biography / Life Tribute Announcement <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-amber-700 font-semibold bg-amber-50 px-2 py-0.5 rounded border border-amber-200">⚡ Formatting Toolbar</span>
                    </div>

                    <!-- Formatting Toolbar Buttons -->
                    <div class="bg-slate-100 border border-slate-300 border-b-0 rounded-t-xl p-2 flex flex-wrap items-center gap-1.5 text-xs select-none">
                        <button type="button" @click="applyTag('b')" class="px-2.5 py-1 bg-white hover:bg-slate-200 border border-slate-300 rounded font-bold text-slate-900 shadow-2xs" title="Bold Text">B</button>
                        <button type="button" @click="applyTag('i')" class="px-2.5 py-1 bg-white hover:bg-slate-200 border border-slate-300 rounded italic text-slate-900 shadow-2xs" title="Italic Text">I</button>
                        <button type="button" @click="applyTag('u')" class="px-2.5 py-1 bg-white hover:bg-slate-200 border border-slate-300 rounded underline text-slate-900 shadow-2xs" title="Underline Text">U</button>
                        <span class="w-[1px] h-5 bg-slate-300 mx-1"></span>
                        <button type="button" @click="applyTag('h3')" class="px-2.5 py-1 bg-white hover:bg-slate-200 border border-slate-300 rounded font-bold text-amber-800 shadow-2xs" title="Section Heading">Heading 3</button>
                        <button type="button" @click="applyTag('p')" class="px-2.5 py-1 bg-white hover:bg-slate-200 border border-slate-300 rounded text-slate-800 shadow-2xs" title="Paragraph">&lt;p&gt; Para</button>
                        <button type="button" @click="applyList()" class="px-2.5 py-1 bg-white hover:bg-slate-200 border border-slate-300 rounded text-slate-800 shadow-2xs" title="Bullet List">&bull; Bullet List</button>
                        <button type="button" @click="applyTag('blockquote')" class="px-2.5 py-1 bg-white hover:bg-slate-200 border border-slate-300 rounded italic text-slate-700 shadow-2xs" title="Quote">“Quote”</button>
                        <span class="w-[1px] h-5 bg-slate-300 mx-1"></span>
                        <button type="button" @click="previewMode = !previewMode" class="ml-auto px-3 py-1 bg-slate-800 text-white rounded font-bold text-[11px] shadow-2xs flex items-center space-x-1" x-text="previewMode ? '✍️ Edit Text' : '👁️ Live Formatted Preview'"></button>
                    </div>

                    <!-- Editor Textarea -->
                    <div x-show="!previewMode">
                        <textarea name="biography" id="biography" rows="6" required x-model="content" placeholder="Write a respectful summary of their life journey, survivors, career, and legacy..." class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-b-xl text-on-surface text-sm focus:outline-none focus:border-primary leading-relaxed font-mono"></textarea>
                    </div>

                    <!-- Formatted Preview -->
                    <div x-show="previewMode" class="p-5 bg-white border border-slate-300 rounded-b-xl min-h-[160px] prose prose-slate max-w-none text-sm leading-relaxed font-sans shadow-inner">
                        <div x-html="content || '<span class=\'text-slate-400 italic\'>Nothing to preview...</span>'"></div>
                    </div>
                </div>

                <!-- Step 1 Nav -->
                <div class="pt-4 flex justify-end">
                    <button type="button" @click="nextStep(2)" class="w-full sm:w-auto px-6 py-3.5 bg-primary text-on-primary font-semibold rounded-xl text-xs sm:text-sm hover:bg-primary-container transition-all flex items-center justify-center space-x-2">
                        <span>Continue to Step 2 (Funeral Details)</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </div>
            </div>

            <!-- STEP 2: FUNERAL INFORMATION -->
            <div x-show="step === 2" class="space-y-5 sm:space-y-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" x-cloak>
                <div class="border-b border-outline-variant/30 pb-3 sm:pb-4">
                    <h2 class="font-serif text-xl sm:text-2xl font-bold text-primary">Step 2: Funeral & Service Details</h2>
                    <p class="text-on-surface-variant/70 text-xs mt-1">Provide funeral arrangements so friends and family can pay their respects.</p>
                </div>

                <!-- Funeral Date -->
                <div>
                    <label for="funeral_date" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                        Funeral / Burial Date (Optional)
                    </label>
                    <input type="date" name="funeral_date" id="funeral_date" value="{{ old('funeral_date') }}" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface text-sm focus:outline-none focus:border-primary">
                </div>

                <!-- Church Service Location -->
                <div>
                    <label for="church_service_location" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                        Church / Funeral Service Location (Optional)
                    </label>
                    <input type="text" name="church_service_location" id="church_service_location" value="{{ old('church_service_location') }}" placeholder="e.g. AIC Milimani, All Saints Cathedral, St. Stephen's" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface text-sm focus:outline-none focus:border-primary">
                </div>

                <!-- Burial Location -->
                <div>
                    <label for="burial_location" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                        Burial Ground / Final Resting Place (Optional)
                    </label>
                    <input type="text" name="burial_location" id="burial_location" value="{{ old('burial_location') }}" placeholder="e.g. Family Home Kapseret, Lang'ata Cemetery" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface text-sm focus:outline-none focus:border-primary">
                </div>

                <!-- Programme PDF Upload -->
                <div>
                    <label for="programme_file" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                        Funeral Programme PDF Document (Optional)
                    </label>
                    <div class="p-3.5 sm:p-4 bg-surface-container-low border border-dashed border-outline-variant rounded-xl flex items-center space-x-3">
                        <span class="material-symbols-outlined text-on-surface-variant text-[28px] sm:text-[32px]">picture_as_pdf</span>
                        <div class="flex-grow text-xs">
                            <input type="file" name="programme_file" id="programme_file" accept="application/pdf" class="block w-full text-xs text-on-surface-variant file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-secondary-fixed file:text-on-secondary-fixed">
                            <span class="text-[10px] text-on-surface-variant/60 block mt-1">PDF file format only (Max 10MB)</span>
                        </div>
                    </div>
                </div>

                <!-- Step 2 Nav -->
                <div class="pt-4 flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
                    <button type="button" @click="goToStep(1)" class="w-full sm:w-auto px-5 py-3 bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold rounded-xl text-xs sm:text-sm transition-colors text-center">
                        Back to Step 1
                    </button>
                    <button type="button" @click="nextStep(3)" class="w-full sm:w-auto px-6 py-3.5 bg-primary text-on-primary font-semibold rounded-xl text-xs sm:text-sm hover:bg-primary-container transition-all flex items-center justify-center space-x-2">
                        <span>Continue to Step 3 (Submitter Info)</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </div>
            </div>

            <!-- STEP 3: SUBMITTER INFORMATION & CONSENT -->
            <div x-show="step === 3" class="space-y-5 sm:space-y-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" x-cloak>
                <div class="border-b border-outline-variant/30 pb-3 sm:pb-4">
                    <h2 class="font-serif text-xl sm:text-2xl font-bold text-primary">Step 3: Submitter Details & Verification</h2>
                    <p class="text-on-surface-variant/70 text-xs mt-1">Used solely by our administrative team to confirm authorization prior to publishing.</p>
                </div>

                <!-- Submitter Full Name -->
                <div>
                    <label for="submitter_name" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                        Your Full Name (Submitter) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="submitter_name" id="submitter_name" value="{{ old('submitter_name') }}" required placeholder="e.g. David Cheruiyot" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface text-sm focus:outline-none focus:border-primary">
                </div>

                <!-- Phone Number & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="submitter_phone" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                            M-Pesa Contact Phone Number <span class="text-rose-500">*</span>
                        </label>
                        <input type="tel" name="submitter_phone" id="submitter_phone" value="{{ old('submitter_phone') }}" required placeholder="e.g. 0712345678 or 254712345678" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface text-sm focus:outline-none focus:border-primary">
                        <span class="text-[10px] text-on-surface-variant/60 block mt-1">This number will receive the M-Pesa STK push prompt.</span>
                    </div>

                    <div>
                        <label for="submitter_email" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                            Email Address (Optional)
                        </label>
                        <input type="email" name="submitter_email" id="submitter_email" value="{{ old('submitter_email') }}" placeholder="your.email@example.com" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface text-sm focus:outline-none focus:border-primary">
                    </div>
                </div>

                <!-- Relationship -->
                <div>
                    <label for="relationship" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                        Relationship to Deceased <span class="text-rose-500">*</span>
                    </label>
                    <select name="relationship" id="relationship" required class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl text-on-surface text-sm focus:outline-none focus:border-primary">
                        <option value="">Select Relationship</option>
                        @foreach($relationships as $rel)
                            <option value="{{ $rel }}" {{ old('relationship') == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Permission Checkbox -->
                <div class="p-4 bg-secondary-container/20 rounded-xl border border-secondary-container">
                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input type="checkbox" name="family_permission_confirmed" value="1" required class="mt-1 w-4 h-4 text-secondary rounded border-outline-variant focus:ring-secondary">
                        <span class="text-xs text-on-surface leading-relaxed font-medium">
                            I confirm that I have permission from the family to submit this obituary notice to Obituaries.co.ke.
                        </span>
                    </label>
                </div>

                <!-- Summary Box -->
                <div class="p-4 bg-primary text-on-primary rounded-xl text-xs flex items-center justify-between">
                    <div>
                        <span class="font-bold text-secondary-fixed uppercase tracking-wider block mb-0.5">Basic Notice Package</span>
                        <span class="text-primary-fixed/70">Standard Obituary Notice Publishing</span>
                    </div>
                    <div class="text-right">
                        <span class="font-serif text-base sm:text-lg font-bold text-white">KES {{ number_format(\App\Models\Setting::get('obituary_publishing_cost', 500)) }}</span>
                        <span class="text-[10px] text-primary-fixed/60 block">Via M-Pesa STK Push</span>
                    </div>
                </div>

                <!-- Step 3 Nav & Final Submit -->
                <div class="pt-4 flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
                    <button type="button" @click="goToStep(2)" class="w-full sm:w-auto px-5 py-3 bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold rounded-xl text-xs sm:text-sm transition-colors text-center">
                        Back to Step 2
                    </button>
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-secondary text-on-secondary font-bold rounded-xl text-xs sm:text-sm hover:bg-secondary/90 transition-all shadow-md flex items-center justify-center space-x-2">
                        <span>Proceed to M-Pesa Payment (KES {{ number_format(\App\Models\Setting::get('obituary_publishing_cost', 500)) }})</span>
                        <span class="material-symbols-outlined text-[18px]">payments</span>
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
                window.scrollTo({ top: 180, behavior: 'smooth' });
            }
        }
    }
</script>
@endsection
