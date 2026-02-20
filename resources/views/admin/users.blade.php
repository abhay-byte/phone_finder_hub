@extends('admin.layout')

@section('title', 'Manage Users & Roles')

@section('admin-content')
<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Manage Users & Roles</h1>
        <p class="text-gray-500 dark:text-gray-400">View registered users and assign the 'Author' role to allow them to publish blogs.</p>
    </div>
    
    <form action="{{ route('admin.users.index') }}" method="GET" class="relative max-w-sm w-full">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, handle, or email..." 
               class="w-full bg-white dark:bg-[#121212] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-teal-500 focus:border-teal-500 block pl-10 p-2.5 shadow-sm">
        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        @if(request('search'))
            <a href="{{ route('admin.users.index') }}" class="absolute right-3 top-3 text-gray-400 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </a>
        @endif
    </form>
</div>

<div class="bg-white dark:bg-[#121212] border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50/50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10">
                <tr>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider">User</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider border-l border-gray-200 dark:border-white/5">Joined</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider border-l border-gray-200 dark:border-white/5">Current Role</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="bg-white dark:bg-transparent border-b border-gray-50 dark:border-white/5 hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm shadow-indigo-500/20">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="max-w-[180px]">
                                    <div class="font-bold text-gray-900 dark:text-white truncate" title="{{ $user->name }}">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-400 truncate">@<span>{{ $user->username }}</span> • {{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 align-middle font-medium border-l border-gray-100 dark:border-white/5">
                            {{ $user->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 align-middle border-l border-gray-100 dark:border-white/5">
                            @if($user->isSuperAdmin())
                                <span class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 text-xs font-bold px-2.5 py-1 rounded-full border border-red-200 dark:border-red-800/50">Super Admin</span>
                            @elseif($user->isMaintainer())
                                <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 text-xs font-bold px-2.5 py-1 rounded-full border border-blue-200 dark:border-blue-800/50">Maintainer</span>
                            @elseif($user->isModerator())
                                <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-400 text-xs font-bold px-2.5 py-1 rounded-full border border-orange-200 dark:border-orange-800/50">Moderator</span>
                            @elseif($user->isAuthor())
                                <span class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-400 text-xs font-bold px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-800/50">Author</span>
                            @else
                                <span class="bg-gray-100 dark:bg-white/10 text-gray-800 dark:text-gray-300 text-xs font-bold px-2.5 py-1 rounded-full border border-gray-200 dark:border-white/10">Standard User</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle text-right">
                            <form action="{{ route('admin.users.role.update', $user) }}" method="POST" class="inline-flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select name="role" class="bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-100 text-xs rounded-lg focus:ring-teal-500 focus:border-teal-500 block p-1.5 cursor-pointer font-medium hover:border-teal-500/50 transition-colors">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Standard User</option>
                                    <option value="author" {{ $user->role === 'author' ? 'selected' : '' }}>Author</option>
                                    <option value="maintainer" {{ $user->role === 'maintainer' ? 'selected' : '' }}>Maintainer</option>
                                    <option value="moderator" {{ $user->role === 'moderator' ? 'selected' : '' }}>Moderator</option>
                                    @if(auth()->user()->isSuperAdmin())
                                        <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                    @endif
                                </select>
                                <button type="submit" 
                                    class="text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-900/20 hover:bg-teal-100 dark:hover:bg-teal-900/40 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm"
                                    @if(auth()->id() === $user->id) disabled title="You cannot modify your own role" class="opacity-50 cursor-not-allowed" @endif>
                                    Save
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-white/5">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <p class="font-medium text-lg text-gray-900 dark:text-white mb-1">No users found</p>
                            <p class="text-sm">Try adjusting your search query.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
