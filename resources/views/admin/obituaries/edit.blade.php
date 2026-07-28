@extends('layouts.admin')

@section('title', 'Edit Obituary: ' . $obituary->full_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <a href="{{ route('admin.obituaries.show', $obituary->id) }}" class="text-xs text-amber-700 font-semibold mb-1 block">&larr; Back to Obituary Detail</a>
            <h1 class="font-serif text-2xl font-bold text-slate-900">Edit Obituary: {{ $obituary->full_name }}</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl">
            <div class="font-bold mb-1">Please fix the errors below:</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs">
        <form action="{{ route('admin.obituaries.update', $obituary->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Notice Status -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                <label for="status" class="block text-xs font-bold uppercase text-slate-800 tracking-wider">Notice Publication Status</label>
                <select name="status" id="status" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-900">
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $obituary->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Deceased Personal Info -->
            <div class="space-y-4 pt-2">
                <h3 class="font-serif text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">Deceased Information</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Deceased Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', $obituary->full_name) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Date of Birth <span class="text-slate-400 font-normal lowercase">(optional)</span></label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($obituary->date_of_birth)->format('Y-m-d')) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Date of Death <span class="text-rose-500">*</span></label>
                        <input type="date" name="date_of_death" value="{{ old('date_of_death', $obituary->date_of_death->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">County <span class="text-rose-500">*</span></label>
                        <select name="county" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                            @foreach($counties as $c)
                                <option value="{{ $c }}" {{ old('county', $obituary->county) == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Town / Sub-County <span class="text-rose-500">*</span></label>
                        <input type="text" name="town" value="{{ old('town', $obituary->town) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Profile Photo Upload (Optional)</label>
                        <div class="flex items-center space-x-4 p-3.5 bg-slate-50 border border-slate-300 rounded-xl">
                            @if($obituary->photo)
                                <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-14 h-14 object-cover rounded-lg flex-shrink-0 border border-slate-300">
                            @endif
                            <div class="flex-grow">
                                <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800">
                                <span class="text-[10px] text-slate-400 block mt-1">Leave empty to keep existing photo.</span>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Moments in Time Gallery Photos (Add up to 5 photos)</label>
                        <div class="p-3.5 bg-slate-50 border border-slate-300 rounded-xl space-y-3">
                            @if(is_array($obituary->gallery_images) && count($obituary->gallery_images) > 0)
                                <div class="flex flex-wrap gap-2 mb-2">
                                    @foreach($obituary->gallery_images as $gImg)
                                        <div class="w-12 h-12 rounded-lg overflow-hidden border border-slate-300">
                                            <img src="{{ asset('storage/' . $gImg) }}" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <input type="file" name="gallery_images[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800">
                            <span class="text-[10px] text-slate-400 block">Select multiple image files to add to the gallery.</span>
                        </div>
                    </div>

                    <div class="sm:col-span-2" x-data="biographyEditor(`{!! addslashes(old('biography', $obituary->biography)) !!}`)">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold uppercase text-slate-700">Biography & Life Story <span class="text-rose-500">*</span></label>
                            <span class="text-[11px] text-amber-700 font-semibold bg-amber-50 px-2 py-0.5 rounded border border-amber-200">⚡ Editor Formatting Toolbar</span>
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
                            <textarea id="admin_biography" name="biography" rows="8" required x-model="content" placeholder="Write the full tribute, life history, and family notices here..." class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-b-xl text-sm leading-relaxed font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                        </div>

                        <!-- Formatted Preview -->
                        <div x-show="previewMode" class="p-5 bg-white border border-slate-300 rounded-b-xl min-h-[200px] prose prose-slate max-w-none text-sm leading-relaxed font-sans shadow-inner">
                            <div x-html="content || '<span class=\'text-slate-400 italic\'>Nothing to preview...</span>'"></div>
                        </div>
                        <span class="text-[11px] text-slate-500 mt-1 block">Highlight text in the editor and click B, I, U, Heading, Bullet List, or Quote to format biography sections.</span>
                    </div>
                </div>
            </div>

            <!-- Funeral Service Info -->
            <div class="space-y-4 pt-4 border-t border-slate-200">
                <h3 class="font-serif text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">Funeral & Service Details</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Funeral / Service Date</label>
                        <input type="date" name="funeral_date" value="{{ old('funeral_date', $obituary->funeral_date ? $obituary->funeral_date->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Service Venue / Location</label>
                        <input type="text" name="church_service_location" value="{{ old('church_service_location', $obituary->church_service_location) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Burial Location / Resting Place</label>
                        <input type="text" name="burial_location" value="{{ old('burial_location', $obituary->burial_location) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Funeral Programme PDF Upload (Optional)</label>
                        <div class="p-3.5 bg-slate-50 border border-slate-300 rounded-xl flex items-center space-x-3">
                            <input type="file" name="programme_file" accept="application/pdf" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800">
                            @if($obituary->programme_file)
                                <a href="{{ asset('storage/' . $obituary->programme_file) }}" target="_blank" class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-semibold flex-shrink-0">
                                    Current PDF
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submitter Information -->
            <div class="space-y-4 pt-4 border-t border-slate-200">
                <h3 class="font-serif text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">Submitter Contact Information</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Submitter Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="submitter_name" value="{{ old('submitter_name', $obituary->submitter_name) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Submitter Phone <span class="text-rose-500">*</span></label>
                        <input type="text" name="submitter_phone" value="{{ old('submitter_phone', $obituary->submitter_phone) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Submitter Email</label>
                        <input type="email" name="submitter_email" value="{{ old('submitter_email', $obituary->submitter_email) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Relationship to Deceased <span class="text-rose-500">*</span></label>
                        <input type="text" name="relationship" value="{{ old('relationship', $obituary->relationship) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                </div>
            </div>

            <!-- SEO & Search Engine Optimization Metadata -->
            <div class="space-y-4 pt-4 border-t border-slate-200">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <h3 class="font-serif text-lg font-bold text-slate-900">🔍 Search Engine Optimization (SEO Metadata)</h3>
                    <span class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full font-semibold">Google Search Ready</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Custom Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $obituary->meta_title) }}" placeholder="e.g. {{ $obituary->full_name }} Obituary | Death Notice & Funeral Details" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                        <p class="text-[11px] text-slate-400 mt-1">Leave blank to automatically use system default format: "{Name} Obituary | Obituaries.co.ke"</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Custom Meta Description</label>
                        <textarea name="meta_description" rows="2" placeholder="e.g. Read the obituary, life story, funeral details, and memories of {{ $obituary->full_name }} from {{ $obituary->town }}, {{ $obituary->county }}." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm leading-relaxed">{{ old('meta_description', $obituary->meta_description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">SEO Keywords (Comma Separated)</label>
                        <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $obituary->seo_keywords) }}" placeholder="e.g. {{ $obituary->full_name }} obituary, {{ $obituary->county }} obituaries, death notice Kenya" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Canonical URL (Optional)</label>
                        <input type="text" name="canonical_url" value="{{ old('canonical_url', $obituary->canonical_url) }}" placeholder="e.g. https://obituaries.co.ke/obituary/{{ $obituary->slug }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-200 flex justify-end space-x-3">
                <a href="{{ route('admin.obituaries.show', $obituary->id) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs shadow-md">
                    Update Obituary Record
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function biographyEditor(initialContent = '') {
        return {
            content: initialContent,
            previewMode: false,
            applyTag(tag) {
                const textarea = document.getElementById('admin_biography');
                if (!textarea) return;
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const selected = textarea.value.substring(start, end) || 'Sample Text';
                const replacement = `<${tag}>${selected}</${tag}>`;
                textarea.setRangeText(replacement, start, end, 'select');
                this.content = textarea.value;
            },
            applyList() {
                const textarea = document.getElementById('admin_biography');
                if (!textarea) return;
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const selected = textarea.value.substring(start, end) || 'First item\nSecond item';
                const items = selected.split('\n').map(item => `  <li>${item.trim()}</li>`).join('\n');
                const replacement = `<ul>\n${items}\n</ul>`;
                textarea.setRangeText(replacement, start, end, 'select');
                this.content = textarea.value;
            }
        }
    }
</script>
@endsection
