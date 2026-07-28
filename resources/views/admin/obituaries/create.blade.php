@extends('layouts.admin')

@section('title', 'Create New Obituary (Admin Submission)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <a href="{{ route('admin.obituaries.index') }}" class="text-xs text-amber-700 font-semibold mb-1 block">&larr; Back to Obituaries List</a>
            <h1 class="font-serif text-2xl font-bold text-slate-900">Create New Obituary Notice</h1>
            <p class="text-xs text-slate-500 mt-0.5">Admin Direct Submission &mdash; Payment is automatically waived.</p>
        </div>
    </div>

    <!-- Admin Waiver Notice -->
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-900 text-xs font-semibold flex items-center space-x-2">
        <span class="text-emerald-600 text-base">⚡</span>
        <span><strong>Admin Privilege:</strong> Submitting an obituary directly through the Admin Portal bypasses M-Pesa payment and marks the notice as verified.</span>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl">
            <div class="font-bold mb-1">Please fix the validation errors below:</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs">
        <form action="{{ route('admin.obituaries.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Publication Status -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                <label for="status" class="block text-xs font-bold uppercase text-slate-800 tracking-wider">Initial Publication Status</label>
                <select name="status" id="status" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-900">
                    <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>⚡ Publish Live Immediately (Verified & Active)</option>
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>📁 Save as Draft (Hidden from Public)</option>
                    <option value="pending_verification" {{ old('status') === 'pending_verification' ? 'selected' : '' }}>⏳ Pending Verification Review</option>
                </select>
            </div>

            <!-- Deceased Information -->
            <div class="space-y-4 pt-2">
                <h3 class="font-serif text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">Deceased Information</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Deceased Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="e.g. Mzee Joseph Kiarie Kimani" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Date of Birth <span class="text-rose-500">*</span></label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Date of Passing <span class="text-rose-500">*</span></label>
                        <input type="date" name="date_of_death" value="{{ old('date_of_death') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">County <span class="text-rose-500">*</span></label>
                        <select name="county" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                            <option value="">Select County...</option>
                            @foreach($counties as $c)
                                <option value="{{ $c }}" {{ old('county') === $c ? 'selected' : '' }}>{{ $c }} County</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Town / Village / City <span class="text-rose-500">*</span></label>
                        <input type="text" name="town" value="{{ old('town') }}" required placeholder="e.g. Ruiru / Westlands" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Biography & Life Tribute <span class="text-rose-500">*</span></label>
                        <textarea name="biography" rows="6" required placeholder="Write the full tribute, life history, and family notices here..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm leading-relaxed">{{ old('biography') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Funeral Service & Burial Details -->
            <div class="space-y-4 pt-4 border-t border-slate-200">
                <h3 class="font-serif text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">Funeral Service & Burial Details</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Funeral / Burial Date</label>
                        <input type="date" name="funeral_date" value="{{ old('funeral_date') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Burial Location / Family Farm</label>
                        <input type="text" name="burial_location" value="{{ old('burial_location') }}" placeholder="e.g. Lang'ata Cemetery / Kiambu Farm" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Church Service Location</label>
                        <input type="text" name="church_service_location" value="{{ old('church_service_location') }}" placeholder="e.g. All Saints Cathedral, Nairobi" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                </div>
            </div>

            <!-- Submitter & Contact Details -->
            <div class="space-y-4 pt-4 border-t border-slate-200">
                <h3 class="font-serif text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">Family Submitter / Contact Person</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Submitter Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="submitter_name" value="{{ old('submitter_name', 'Admin Editorial Team') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Submitter Phone Number <span class="text-rose-500">*</span></label>
                        <input type="text" name="submitter_phone" value="{{ old('submitter_phone', '0700000000') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Submitter Email</label>
                        <input type="email" name="submitter_email" value="{{ old('submitter_email') }}" placeholder="e.g. family@gmail.com" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Relationship to Deceased <span class="text-rose-500">*</span></label>
                        <input type="text" name="relationship" value="{{ old('relationship', 'Family Representative') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                </div>
            </div>

            <!-- Media & Document Uploads -->
            <div class="space-y-4 pt-4 border-t border-slate-200">
                <h3 class="font-serif text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">Photos & Documents</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Main Obituary Photo (JPEG, PNG, WEBP)</label>
                        <input type="file" name="photo" accept="image/*" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                        <p class="text-[11px] text-slate-500 mt-1">Primary portrait picture displayed on home and public memorial page.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Funeral Programme PDF</label>
                        <input type="file" name="programme_file" accept=".pdf" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                        <p class="text-[11px] text-slate-500 mt-1">Optional PDF order of service booklet download.</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Moments in Time Photo Gallery (Up to 8 Images)</label>
                        <input type="file" name="gallery_images[]" multiple accept="image/*" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                        <p class="text-[11px] text-slate-500 mt-1">Select multiple family photographs for the memorial gallery.</p>
                    </div>
                </div>
            </div>

            <!-- Custom SEO Metadata -->
            <div class="space-y-4 pt-4 border-t border-slate-200">
                <h3 class="font-serif text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">SEO Search Engine Metadata (Optional)</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Custom SEO Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title') }}" placeholder="Auto-generated if left blank" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Meta Description</label>
                        <textarea name="meta_description" rows="2" placeholder="Short description for Google search results..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">{{ old('meta_description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">SEO Keywords (Comma separated)</label>
                        <input type="text" name="seo_keywords" value="{{ old('seo_keywords') }}" placeholder="e.g. John Doe Obituary, Kiambu Obituaries, Kenya Funeral" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.obituaries.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition-all">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-amber-700 text-white rounded-xl text-xs font-bold hover:bg-amber-800 transition-all shadow-md flex items-center space-x-2">
                    <span>⚡ Publish Obituary (Admin Submission)</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
