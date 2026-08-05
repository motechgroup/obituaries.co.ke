@extends('layouts.admin')

@section('title', 'Manage Business Categories')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="font-serif text-2xl font-bold text-white">Funeral Business Categories</h1>
            <p class="text-xs text-slate-400">Database-driven categories for funeral homes, hearses, florists, caterers, insurance, etc.</p>
        </div>
    </div>

    <!-- Create Category Form & List -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400">Add New Category</h3>
            <form action="{{ route('admin.advertising.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Category Name</label>
                    <input type="text" name="name" required placeholder="e.g. Funeral Insurance" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="Category summary..." class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold py-2.5 rounded-xl text-xs uppercase">
                    Add Category
                </button>
            </form>
        </div>

        <div class="md:col-span-2 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400">All Business Categories</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Category Name</th>
                            <th class="py-3 px-4">Profiles</th>
                            <th class="py-3 px-4">Campaigns</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($categories as $cat)
                            <tr class="hover:bg-slate-800/50">
                                <td class="py-3 px-4 font-bold text-white">
                                    {{ $cat->name }}
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-sky-400">
                                    {{ $cat->profiles_count }}
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-amber-400">
                                    {{ $cat->campaigns_count }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-950 text-emerald-300 border border-emerald-800">Active</span>
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
