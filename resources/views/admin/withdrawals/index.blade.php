<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-3">
                <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('Payout Management') }}
            </h2>
            <div class="flex space-x-4 text-sm md:text-base">
                <a href="{{ route('admin.dashboard') }}" class="text-indigo-400 hover:text-white transition font-medium">Dashboard</a>
                <span class="text-slate-600">|</span>
                <span class="text-white font-medium">Withdrawals</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-950 min-h-screen -mt-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-emerald-500/10 text-emerald-400 rounded-2xl border border-emerald-500/20 backdrop-blur-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-slate-900 overflow-hidden shadow-2xl rounded-2xl border border-slate-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-900/80 text-slate-400 text-xs uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">User</th>
                                <th class="px-6 py-4 font-semibold">Bank Details</th>
                                <th class="px-6 py-4 font-semibold">Amount</th>
                                <th class="px-6 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50 text-sm">
                            @forelse($withdrawals as $withdrawal)
                                <tr class="hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-white">{{ $withdrawal->user->name }}</div>
                                        <div class="text-slate-400 mt-0.5 text-xs">{{ $withdrawal->user->email }}</div>
                                        <div class="text-slate-500 mt-1 text-[10px]">{{ $withdrawal->created_at->format('M d, Y h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($withdrawal->user->bankAccount)
                                            <div class="font-medium text-slate-200">{{ $withdrawal->user->bankAccount->bank_name }}</div>
                                            <div class="text-slate-300 font-mono text-xs mt-0.5">{{ $withdrawal->user->bankAccount->account_number }}</div>
                                            <div class="text-slate-400 text-xs mt-0.5">{{ $withdrawal->user->bankAccount->account_name }}</div>
                                        @else
                                            <span class="text-slate-500 italic">No account provided</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-black text-white text-lg">₦{{ number_format($withdrawal->amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($withdrawal->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">Pending</span>
                                        @elseif($withdrawal->status === 'approved' || $withdrawal->status === 'paid')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Paid</span>
                                        @elseif($withdrawal->status === 'processing')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">Processing</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($withdrawal->status === 'pending' || $withdrawal->status === 'processing')
                                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                                <button @click="open = !open" class="bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-white px-4 py-2 rounded-lg font-medium text-xs border border-indigo-500/50 transition-colors flex items-center gap-2">
                                                    Update <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                </button>
                                                
                                                <div x-show="open" @click.away="open = false" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-72 rounded-xl shadow-2xl bg-slate-800 border border-slate-700 z-50 overflow-hidden">
                                                    <div class="p-4">
                                                        <form action="{{ route('admin.withdrawals.update-status', $withdrawal) }}" method="POST">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label class="block text-xs font-medium text-slate-400 mb-1">Status</label>
                                                                <select name="status" class="block w-full rounded-lg bg-slate-900 border-slate-700 text-white text-sm focus:border-indigo-500 focus:ring-indigo-500/20">
                                                                    <option value="pending" {{ $withdrawal->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                                    <option value="processing" {{ $withdrawal->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                                                    <option value="approved" {{ $withdrawal->status === 'approved' ? 'selected' : '' }}>Approved / Paid</option>
                                                                    <option value="rejected" {{ $withdrawal->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-4">
                                                                <label class="block text-xs font-medium text-slate-400 mb-1">Admin Notes (Optional)</label>
                                                                <textarea name="admin_notes" rows="2" class="block w-full rounded-lg bg-slate-900 border-slate-700 text-white text-sm focus:border-indigo-500 focus:ring-indigo-500/20" placeholder="Reason for rejection, transaction ref...">{{ $withdrawal->admin_notes }}</textarea>
                                                            </div>
                                                            <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                                                                Save Changes
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-500 text-xs italic">Resolved</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        No withdrawal requests found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($withdrawals->hasPages())
                    <div class="p-4 border-t border-slate-800 bg-slate-900/50">
                        {{ $withdrawals->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
