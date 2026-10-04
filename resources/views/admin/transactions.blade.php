<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-3">
                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                </svg>
                {{ __('Transaction Logs') }}
            </h2>
            <div class="flex space-x-4 text-sm md:text-base">
                <a href="{{ route('admin.dashboard') }}" class="text-indigo-400 hover:text-white transition font-medium">Dashboard</a>
                <span class="text-slate-600">|</span>
                <span class="text-white font-medium">Transactions</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-950 min-h-screen -mt-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-slate-900 overflow-hidden shadow-2xl rounded-2xl border border-slate-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-900/80 text-slate-400 text-xs uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">Reference</th>
                                <th class="px-6 py-4 font-semibold">Guest & Event</th>
                                <th class="px-6 py-4 font-semibold text-right">Amount</th>
                                <th class="px-6 py-4 font-semibold text-right">Platform Fee</th>
                                <th class="px-6 py-4 font-semibold text-right">Host Payout</th>
                                <th class="px-6 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 font-semibold text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50 text-sm">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-mono text-xs text-slate-300">{{ $transaction->reference }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-white">{{ $transaction->guest->name ?? 'Guest' }}</div>
                                        <div class="text-slate-400 mt-0.5 text-xs truncate max-w-[200px]">{{ $transaction->event->title ?? 'Unknown Event' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="font-black text-white text-lg">₦{{ number_format($transaction->amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="font-medium text-amber-400">₦{{ number_format($transaction->platform_fee, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="font-medium text-emerald-400">₦{{ number_format($transaction->host_payout, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($transaction->status === 'successful')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Success</span>
                                        @elseif($transaction->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">Pending</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">Failed</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap text-slate-500 text-xs">
                                        {{ $transaction->created_at->format('M d, Y h:i A') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        No transactions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                    <div class="p-4 border-t border-slate-800 bg-slate-900/50">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
