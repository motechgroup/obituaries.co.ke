@extends('layouts.admin')

@section('title', 'Manage Staff & Roles | Admin')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, editModal: false, activeUser: {} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl font-bold text-slate-900">Admin Staff & Role Management</h1>
            <p class="text-slate-500 text-sm mt-1">Manage platform administrators and editors with obituary verification privileges.</p>
        </div>
        <button type="button" @click="createModal = true" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition-all shadow-md flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Add Staff Account</span>
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-bold">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl font-bold">
            {{ session('error') }}
        </div>
    @endif

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <table class="w-full text-left text-sm text-slate-700">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-6 py-3.5">Staff Name & Email</th>
                    <th class="px-6 py-3.5">Assigned Role</th>
                    <th class="px-6 py-3.5">Created Date</th>
                    <th class="px-6 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($users as $u)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900">{{ $u->name }}</div>
                            <div class="text-xs text-slate-500">{{ $u->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($u->role === 'super_admin')
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-900 rounded-full text-xs font-bold uppercase tracking-wider">Super Admin</span>
                            @else
                                <span class="px-2.5 py-1 bg-sky-100 text-sky-900 rounded-full text-xs font-bold uppercase tracking-wider">Editor / Moderator</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $u->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button type="button" @click="activeUser = {{ json_encode($u) }}; editModal = true" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold">
                                Edit
                            </button>

                            <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete staff account {{ $u->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg text-xs font-semibold">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Create User Modal -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-6" @click.away="createModal = false">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <h3 class="font-serif text-xl font-bold text-slate-900">Create Staff Account</h3>
                <button type="button" @click="createModal = false" class="text-slate-400 font-bold hover:text-slate-600">&times;</button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Staff Name</label>
                    <input type="text" name="name" required placeholder="e.g. Sarah Cherono" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" required placeholder="sarah@obituaries.co.ke" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="Minimum 6 characters" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Role Permission</label>
                    <select name="role" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold">
                        <option value="editor">Editor (Approve & Manage Obituaries)</option>
                        <option value="super_admin">Super Admin (Full System Access)</option>
                    </select>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" @click="createModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs uppercase tracking-wider">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-6" @click.away="editModal = false">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <h3 class="font-serif text-xl font-bold text-slate-900">Edit Staff Account</h3>
                <button type="button" @click="editModal = false" class="text-slate-400 font-bold hover:text-slate-600">&times;</button>
            </div>

            <form :action="'/admin/users/' + activeUser.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Staff Name</label>
                    <input type="text" name="name" x-model="activeUser.name" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" x-model="activeUser.email" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">New Password (Leave blank to keep current)</label>
                    <input type="password" name="password" placeholder="Leave blank to keep existing password" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Role Permission</label>
                    <select name="role" x-model="activeUser.role" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold">
                        <option value="editor">Editor (Approve & Manage Obituaries)</option>
                        <option value="super_admin">Super Admin (Full System Access)</option>
                    </select>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" @click="editModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs uppercase tracking-wider">
                        Update Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
