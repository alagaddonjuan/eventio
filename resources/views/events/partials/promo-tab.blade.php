            @if($event->payment_status === 'paid')
            <div id="panel-promo" class="p-4 hidden">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4 px-2">Manage Promo Codes</h3>
                
                <form action="{{ route('promo-codes.store', $event) }}" method="POST" class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 mb-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Promo Code</label>
                            <input type="text" name="code" required class="w-full text-sm rounded border-slate-300 uppercase" placeholder="e.g. EARLYBIRD20">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Type</label>
                            <select name="discount_type" required class="w-full text-sm rounded border-slate-300">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Amount</label>
                            <input type="number" step="0.01" min="0.01" name="discount_amount" required class="w-full text-sm rounded border-slate-300" placeholder="e.g. 20">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Usage Limit</label>
                            <input type="number" min="1" name="usage_limit" class="w-full text-sm rounded border-slate-300" placeholder="Optional">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Expiry Date</label>
                            <input type="date" name="expires_at" class="w-full text-sm rounded border-slate-300">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-brand-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-brand-700 transition shadow-sm">Create Promo Code</button>
                </form>

                <div class="space-y-3">
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-2">Active Codes</h4>
                    @forelse($event->promoCodes as $promo)
                        <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-100 flex justify-between items-center">
                            <div>
                                <p class="font-bold text-slate-800 font-mono">{{ $promo->code }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ $promo->discount_type === 'percentage' ? intval($promo->discount_amount).'%' : '₦'.number_format($promo->discount_amount, 2) }} OFF
                                    @if($promo->usage_limit)
                                        &middot; {{ $promo->times_used }}/{{ $promo->usage_limit }} used
                                    @else
                                        &middot; {{ $promo->times_used }} used
                                    @endif
                                </p>
                            </div>
                            <form action="{{ route('promo-codes.destroy', [$event, $promo]) }}" method="POST" onsubmit="return confirm('Delete this promo code?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 px-2 italic">No promo codes created yet.</p>
                    @endforelse
                </div>
            </div>
            @endif
