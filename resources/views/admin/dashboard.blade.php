<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-3">
                <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ __('Master Admin Command Center') }}
            </h2>
            <div class="flex space-x-4 text-sm md:text-base">
                <span class="text-white font-medium">Dashboard</span>
                <span class="text-slate-600">|</span>
                <a href="{{ route('admin.users') }}" class="text-indigo-400 hover:text-white transition font-medium">Users</a>
                <span class="text-slate-600">|</span>
                <a href="{{ route('admin.events') }}" class="text-indigo-400 hover:text-white transition font-medium">Events</a>
            </div>
        </div>
    </x-slot>

    <!-- Wrap the main content in a dark wrapper to ensure full dark mode for this page -->
    <div class="min-h-screen bg-slate-950 py-12 -mt-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-8 p-4 bg-emerald-500/10 text-emerald-400 rounded-2xl border border-emerald-500/20 backdrop-blur-md">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Premium Toggle -->
            <div class="relative overflow-hidden bg-slate-900 rounded-2xl border border-slate-800 mb-8 p-6 flex flex-col md:flex-row items-start md:items-center justify-between shadow-2xl">
                <!-- Decorative glow -->
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl"></div>
                
                <div class="relative z-10 mb-4 md:mb-0">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        Global Premium Toggle
                        @if($isPremiumActive)
                            <span class="flex h-3 w-3 relative ml-2">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                        @endif
                    </h3>
                    <p class="text-sm text-slate-400 mt-1">Enable or disable premium/payment features across the entire Eventio platform.</p>
                </div>
                <div class="relative z-10">
                    <form action="{{ route('admin.toggle-premium') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-8 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg flex items-center gap-2 {{ $isPremiumActive ? 'bg-red-500/10 text-red-500 border border-red-500/50 hover:bg-red-500 hover:text-white hover:shadow-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/50 hover:bg-emerald-500 hover:text-white hover:shadow-emerald-500/20' }}">
                            @if($isPremiumActive)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Disable Premium
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Enable Premium
                            @endif
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Users -->
                <div class="relative bg-slate-900 overflow-hidden shadow-2xl rounded-2xl border border-slate-800 p-6 group hover:border-indigo-500/50 transition-colors">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <div class="text-sm font-semibold text-indigo-400 tracking-wider uppercase mb-2">Total Users</div>
                        <div class="text-4xl font-black text-white">{{ number_format($totalUsers) }}</div>
                    </div>
                </div>

                <!-- Total Events -->
                <div class="relative bg-slate-900 overflow-hidden shadow-2xl rounded-2xl border border-slate-800 p-6 group hover:border-fuchsia-500/50 transition-colors">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <div class="text-sm font-semibold text-fuchsia-400 tracking-wider uppercase mb-2">Total Events</div>
                        <div class="text-4xl font-black text-white">{{ number_format($totalEvents) }}</div>
                    </div>
                </div>

                <!-- Total Guests -->
                <div class="relative bg-slate-900 overflow-hidden shadow-2xl rounded-2xl border border-slate-800 p-6 group hover:border-cyan-500/50 transition-colors">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <div class="text-sm font-semibold text-cyan-400 tracking-wider uppercase mb-2">Total Guests</div>
                        <div class="text-4xl font-black text-white">{{ number_format($totalGuests) }}</div>
                    </div>
                </div>
            </div>

            <!-- Financial Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Total Gross Revenue -->
                <div class="relative bg-slate-900 overflow-hidden shadow-2xl rounded-2xl border border-slate-800 p-6 group hover:border-emerald-500/50 transition-colors">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <div class="text-sm font-semibold text-emerald-400 tracking-wider uppercase mb-2">Total Volume (Gross)</div>
                        <div class="text-4xl font-black text-white">₦{{ number_format($totalRevenue, 2) }}</div>
                    </div>
                </div>

                <!-- Platform Revenue -->
                <div class="relative bg-slate-900 overflow-hidden shadow-2xl rounded-2xl border border-slate-800 p-6 group hover:border-amber-500/50 transition-colors">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <div class="text-sm font-semibold text-amber-400 tracking-wider uppercase mb-2">Platform Fees (Revenue)</div>
                        <div class="text-4xl font-black text-white">₦{{ number_format($platformRevenue, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Users -->
                <div class="bg-slate-900 overflow-hidden shadow-2xl rounded-2xl border border-slate-800">
                    <div class="p-6 border-b border-slate-800 bg-slate-900/50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            Recent Signups
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-900/80 text-slate-400 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-semibold">User</th>
                                    <th class="px-6 py-4 font-semibold text-right">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50 text-sm">
                                @forelse($recentSignups as $user)
                                    <tr class="hover:bg-slate-800/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-white">{{ $user->name }}</div>
                                            <div class="text-slate-400 mt-0.5">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap text-slate-500">
                                            {{ $user->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-8 text-center text-slate-500">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Events -->
                <div class="bg-slate-900 overflow-hidden shadow-2xl rounded-2xl border border-slate-800">
                    <div class="p-6 border-b border-slate-800 bg-slate-900/50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Recent Events
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-900/80 text-slate-400 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-semibold">Event</th>
                                    <th class="px-6 py-4 font-semibold">Host</th>
                                    <th class="px-6 py-4 font-semibold text-right">Created</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50 text-sm">
                                @forelse($recentEvents as $event)
                                    <tr class="hover:bg-slate-800/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-white">{{ $event->title }}</div>
                                            <div class="text-slate-400 mt-0.5 text-xs">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-800 text-slate-300">
                                                    {{ ucfirst($event->payment_status) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-white">{{ $event->host->name ?? 'Unknown Host' }}</div>
                                            <div class="text-slate-400 mt-0.5 text-xs">{{ $event->host->email ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap text-slate-500">
                                            {{ $event->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-slate-500">No events found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
