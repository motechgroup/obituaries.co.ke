@extends('layouts.admin')

@section('title', 'Manage Business Categories | Admin Panel')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Funeral Business Categories</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Database-driven categories for funeral homes, hearses, florists, caterers, insurance, etc.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Create Category Form & List -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">Add New Category</h3>
            <form action="{{ route('admin.advertising.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Category Name</label>
                    <input type="text" name="name" required placeholder="e.g. Funeral Insurance" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 outline-none focus:bg-white focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="Category summary..." class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 outline-none focus:bg-white focus:border-amber-500"></textarea>
                </div>
                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-3 rounded-xl text-xs uppercase tracking-wider shadow-sm transition-all">
                    + Add Business Category
                </button>
            </form>
        </div>

        <div class="md:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">All Business Categories</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">Category Name</th>
                            <th class="py-3.5 px-4">Profiles</th>
                            <th class="py-3.5 px-4">Campaigns</th>
                            <th class="py-3.5 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($categories as $cat)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $cat->name }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-extrabold text-sky-700">
                                    {{ $cat->profiles_count }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-extrabold text-amber-900">
                                    {{ $cat->campaigns_count }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">Active</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
