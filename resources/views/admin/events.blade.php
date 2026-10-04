<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-3">
                <svg class="w-8 h-8 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ __('Manage Events') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="text-indigo-400 hover:text-white transition font-medium">Dashboard</a>
                <span class="text-slate-600">|</span>
                <a href="{{ route('admin.users') }}" class="text-indigo-400 hover:text-white transition font-medium">Users</a>
                <span class="text-slate-600">|</span>
                <span class="text-white font-medium">Events</span>
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

            <div class="bg-slate-900 overflow-hidden shadow-2xl rounded-2xl border border-slate-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-900/80 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-800">
                                <th class="px-6 py-4 font-semibold">Event Details</th>
                                <th class="px-6 py-4 font-semibold">Host</th>
                                <th class="px-6 py-4 font-semibold text-center">Guests</th>
                                <th class="px-6 py-4 font-semibold">Date</th>
                                <th class="px-6 py-4 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-sm">
                            @forelse($events as $event)
                                <tr class="hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-white">{{ $event->title }}</div>
                                        <div class="text-slate-400 mt-0.5 text-xs flex gap-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $event->payment_status === 'premium' ? 'bg-fuchsia-500/10 text-fuchsia-400 border border-fuchsia-500/20' : 'bg-slate-800 text-slate-300' }}">
                                                {{ ucfirst($event->payment_status) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-white">{{ $event->host->name ?? 'Unknown Host' }}</div>
                                        <div class="text-slate-400 mt-0.5 text-xs">{{ $event->host->email ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-800 text-slate-300 font-bold border border-slate-700">
                                            {{ $event->guests_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-400">
                                        {{ \Carbon\Carbon::parse($event->event_date)->format('M j, Y g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                        <form action="{{ route('admin.events.suspend', $event) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to {{ $event->is_suspended ? 'unsuspend' : 'suspend' }} this event?');">
                                            @csrf
                                            <button type="submit" class="{{ $event->is_suspended ? 'text-amber-400 hover:text-amber-300 bg-amber-500/10 hover:bg-amber-500/20 border-amber-500/20' : 'text-orange-400 hover:text-orange-300 bg-orange-500/10 hover:bg-orange-500/20 border-orange-500/20' }} px-3 py-1.5 rounded border transition-colors">
                                                {{ $event->is_suspended ? 'Unsuspend' : 'Suspend' }}
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 bg-red-500/10 hover:bg-red-500/20 px-3 py-1.5 rounded border border-red-500/20 transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">No events found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-800">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
