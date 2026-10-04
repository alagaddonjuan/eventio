<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white leading-tight">
            {{ __('Earnings & Payouts') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-emerald-500/10 text-emerald-400 rounded-2xl border border-emerald-500/20 backdrop-blur-md">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-500/10 text-red-400 rounded-2xl border border-red-500/20 backdrop-blur-md">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="p-4 bg-red-500/10 text-red-400 rounded-2xl border border-red-500/20 backdrop-blur-md">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Balance Card -->
                <div class="relative overflow-hidden bg-slate-900 rounded-2xl border border-slate-800 p-6 shadow-2xl">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/20 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold text-slate-300 mb-2">Available Balance</h3>
                        <div class="text-4xl font-black text-white mb-6">₦{{ number_format($availableBalance, 2) }}</div>
                        
                        @if($bankAccount)
                            <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800 mb-6">
                                <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Payout Account</div>
                                <div class="text-sm font-medium text-slate-300">{{ $bankAccount->bank_name }}</div>
                                <div class="text-lg text-white font-mono">{{ $bankAccount->account_number }}</div>
                                <div class="text-xs text-slate-400 mt-1">{{ $bankAccount->account_name }}</div>
                            </div>
                        @else
                            <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-4 mb-6">
                                <p class="text-sm text-amber-400">Please add a bank account in your profile to request payouts.</p>
                                <a href="{{ route('profile.edit') }}" class="text-amber-300 hover:text-amber-200 text-sm font-medium underline mt-2 inline-block">Update Profile</a>
                            </div>
                        @endif

                        <form action="{{ route('withdrawals.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-400 mb-2">Withdrawal Amount (₦)</label>
                                <input type="number" step="0.01" min="100" max="{{ $availableBalance }}" name="amount" class="block w-full rounded-xl bg-slate-950 border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500/20" placeholder="e.g. 5000" required @if(!$bankAccount || $availableBalance < 100) disabled @endif>
                            </div>
                            <button type="submit" class="w-full px-4 py-3 rounded-xl font-bold transition-all duration-300 bg-indigo-500 hover:bg-indigo-600 text-white shadow-lg shadow-indigo-500/25 disabled:opacity-50 disabled:cursor-not-allowed" @if(!$bankAccount || $availableBalance < 100) disabled @endif>
                                Request Payout
                            </button>
                        </form>
                    </div>
                </div>

                <!-- History Card -->
                <div class="relative overflow-hidden bg-slate-900 rounded-2xl border border-slate-800 p-6 shadow-2xl">
                    <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold text-slate-300 mb-4">Payout History</h3>
                        <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                            @forelse($withdrawals as $withdrawal)
                                <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800 flex justify-between items-center">
                                    <div>
                                        <div class="text-lg font-bold text-white">₦{{ number_format($withdrawal->amount, 2) }}</div>
                                        <div class="text-xs text-slate-400 mt-1">{{ $withdrawal->created_at->format('M d, Y h:i A') }}</div>
                                    </div>
                                    <div class="text-right">
                                        @if($withdrawal->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">Pending</span>
                                        @elseif($withdrawal->status === 'approved' || $withdrawal->status === 'paid')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Paid</span>
                                        @elseif($withdrawal->status === 'processing')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">Processing</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">Rejected</span>
                                        @endif
                                        
                                        @if($withdrawal->admin_notes)
                                            <div class="text-xs text-slate-500 mt-2 max-w-[150px] truncate" title="{{ $withdrawal->admin_notes }}">Note: {{ $withdrawal->admin_notes }}</div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-slate-500 text-sm">
                                    No payout requests yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
