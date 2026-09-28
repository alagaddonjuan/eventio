<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-3">
                <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                {{ __('Manage Users') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="text-indigo-400 hover:text-white transition font-medium">Dashboard</a>
                <span class="text-slate-600">|</span>
                <span class="text-white font-medium">Users</span>
                <span class="text-slate-600">|</span>
                <a href="{{ route('admin.events') }}" class="text-indigo-400 hover:text-white transition font-medium">Events</a>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-950 py-12 -mt-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-8 p-4 bg-emerald-500/10 text-emerald-400 rounded-2xl border border-emerald-500/20 backdrop-blur-md">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-8 p-4 bg-red-500/10 text-red-400 rounded-2xl border border-red-500/20 backdrop-blur-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-slate-900 overflow-hidden shadow-2xl rounded-2xl border border-slate-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-900/80 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-800">
                                <th class="px-6 py-4 font-semibold">User Details</th>
                                <th class="px-6 py-4 font-semibold text-center">Events Hosted</th>
                                <th class="px-6 py-4 font-semibold">Joined Date</th>
                                <th class="px-6 py-4 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-sm">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 bg-indigo-500/20 text-indigo-400 rounded-full flex items-center justify-center font-bold border border-indigo-500/30">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="font-medium text-white flex items-center gap-2">
                                                    {{ $user->name }}
                                                    @if($user->is_admin)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Admin</span>
                                                    @endif
                                                </div>
                                                <div class="text-slate-400 mt-0.5">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-800 text-slate-300 font-bold border border-slate-700">
                                            {{ $user->events_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-400">
                                        {{ $user->created_at->format('M j, Y') }}
                                        <div class="text-xs text-slate-500">{{ $user->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone and will delete all their events.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 bg-red-500/10 hover:bg-red-500/20 px-3 py-1.5 rounded border border-red-500/20 transition-colors" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-800">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
