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

    <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-xs">
        <form action="{{ route('admin.obituaries.update', $obituary->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">Deceased Full Name</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $obituary->full_name) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $obituary->date_of_birth->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">Date of Death</label>
                    <input type="date" name="date_of_death" value="{{ old('date_of_death', $obituary->date_of_death->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">County</label>
                    <select name="county" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                        @foreach($counties as $c)
                            <option value="{{ $c }}" {{ old('county', $obituary->county) == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">Town</label>
                    <input type="text" name="town" value="{{ old('town', $obituary->town) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">Biography</label>
                    <textarea name="biography" rows="6" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">{{ old('biography', $obituary->biography) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">Funeral Date</label>
                    <input type="date" name="funeral_date" value="{{ old('funeral_date', $obituary->funeral_date ? $obituary->funeral_date->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">Service Location</label>
                    <input type="text" name="church_service_location" value="{{ old('church_service_location', $obituary->church_service_location) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">Burial Location</label>
                    <input type="text" name="burial_location" value="{{ old('burial_location', $obituary->burial_location) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">Submitter Name</label>
                    <input type="text" name="submitter_name" value="{{ old('submitter_name', $obituary->submitter_name) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">Submitter Phone</label>
                    <input type="text" name="submitter_phone" value="{{ old('submitter_phone', $obituary->submitter_phone) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">Submitter Email</label>
                    <input type="email" name="submitter_email" value="{{ old('submitter_email', $obituary->submitter_email) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">Relationship</label>
                    <input type="text" name="relationship" value="{{ old('relationship', $obituary->relationship) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>
            </div>

            <div class="pt-4 flex justify-end space-x-3">
                <a href="{{ route('admin.obituaries.show', $obituary->id) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs">Update Obituary</button>
            </div>
        </form>
    </div>
</div>
@endsection
