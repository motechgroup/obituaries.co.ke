@extends('layouts.admin')

@section('title', 'Manage Business Categories | Admin Panel')

@section('content')
<div class="space-y-6" x-data="{ showModal: {{ $errors->any() ? 'true' : 'false' }} }">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Funeral Business Categories</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Database-driven categories for funeral homes, hearses, florists, caterers, insurance, etc.</p>
        </div>
        <button @click="showModal = true" type="button" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-4 py-2.5 rounded-xl text-xs shadow-sm transition-all flex items-center space-x-1.5 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>+ Add New Category</span>
        </button>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-semibold flex items-center space-x-2">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Top Summary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Total Categories</span>
                <span class="text-2xl font-extrabold text-slate-900 font-mono">{{ count($categories) }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-900 flex items-center justify-center font-bold">
                <span class="material-symbols-outlined text-[24px]">category</span>
            </div>
        </div>

        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Registered Profiles</span>
                <span class="text-2xl font-extrabold text-sky-800 font-mono">{{ $categories->sum('profiles_count') }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-800 flex items-center justify-center font-bold">
                <span class="material-symbols-outlined text-[24px]">storefront</span>
            </div>
        </div>

        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Category Campaigns</span>
                <span class="text-2xl font-extrabold text-emerald-800 font-mono">{{ $categories->sum('campaigns_count') }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-800 flex items-center justify-center font-bold">
                <span class="material-symbols-outlined text-[24px]">campaign</span>
            </div>
        </div>
    </div>

    <!-- Full-Width All Categories Table -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900">All Business Categories</h3>
            <span class="text-xs font-semibold text-slate-500">{{ count($categories) }} Categories Configured</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4 text-center">Profiles</th>
                        <th class="py-3.5 px-4 text-center">Campaigns</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($categories as $cat)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-900 flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-[18px]">{{ $cat->icon ?: 'storefront' }}</span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ $cat->name }}</div>
                                        @if($cat->description)
                                            <div class="text-[11px] text-slate-500 truncate max-w-md">{{ $cat->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-extrabold text-sky-800 text-sm">
                                {{ $cat->profiles_count }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-extrabold text-amber-900 text-sm">
                                {{ $cat->campaigns_count }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($cat->status)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-slate-100 text-slate-600 border border-slate-300">
                                        Disabled
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <form action="{{ route('admin.advertising.categories.toggle', $cat->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-lg text-[11px] border border-slate-300 transition-colors">
                                            {{ $cat->status ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>

                                    @if($cat->profiles_count === 0 && $cat->campaigns_count === 0)
                                        <form action="{{ route('admin.advertising.categories.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete category {{ $cat->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg text-[11px] border border-rose-200 transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Floating Modal: Add New Category -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4" 
         style="display: none;" 
         x-cloak>
        <div @click.away="showModal = false" class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-5 relative">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-900 flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-[20px]">category</span>
                    </div>
                    <div>
                        <h3 class="font-serif text-lg font-bold text-slate-900">Add New Category</h3>
                        <p class="text-[11px] text-slate-500">Create a commercial service category for advertisers.</p>
                    </div>
                </div>
                <button @click="showModal = false" type="button" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            @if ($errors->any())
                <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 space-y-1 font-medium">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.advertising.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Category Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Funeral Insurance Services" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 outline-none focus:bg-white focus:border-amber-500 font-medium transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Icon Identifier (Material Symbols)</label>
                    <input type="text" name="icon" value="{{ old('icon', 'storefront') }}" placeholder="e.g. storefront, local_florist, airport_shuttle" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 outline-none focus:bg-white focus:border-amber-500 font-mono transition-all">
                    <span class="text-[10px] text-slate-500 mt-1 block">Google Material Symbol icon code. Defaults to <code>storefront</code>.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Description (Optional)</label>
                    <textarea name="description" rows="3" placeholder="Short overview of services under this category..." class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 outline-none focus:bg-white focus:border-amber-500 transition-all">{{ old('description') }}</textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-3">
                    <button @click="showModal = false" type="button" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-5 py-2.5 rounded-xl text-xs uppercase tracking-wider shadow-md transition-all flex items-center space-x-1.5">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        <span>Create Category</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
