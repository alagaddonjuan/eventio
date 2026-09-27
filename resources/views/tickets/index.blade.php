<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:underline flex items-center inline-flex">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>
            <span class="mx-3 text-gray-300">|</span>
            Ticket Management <span class="text-gray-400 text-sm font-normal ml-2">for {{ $event->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-8">
            
            <!-- List of Tickets -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl border border-gray-100 p-8">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h3 class="text-2xl font-extrabold text-slate-900">Ticket Tiers</h3>
                            <p class="text-slate-500 mt-1">Manage the ticket options available for your guests.</p>
                        </div>
                        <div class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-xl font-bold text-sm">
                            Total Tiers: {{ $tickets->count() }}
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50/80 border border-green-100 text-green-700 rounded-2xl flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($tickets->count() > 0)
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($tickets as $ticket)
                            <div class="group flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 bg-white border border-slate-200 rounded-2xl hover:border-indigo-300 hover:shadow-lg hover:shadow-indigo-500/10 transition-all duration-300">
                                <div>
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h4 class="font-extrabold text-slate-900 text-xl">{{ $ticket->name }}</h4>
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                            {{ $ticket->type === 'free' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $ticket->type }}
                                        </span>
                                    </div>
                                    <div class="flex items-center text-sm font-medium text-slate-500 space-x-4">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            @if($ticket->type === 'paid')
                                                ${{ number_format($ticket->price, 2) }}
                                            @else
                                                $0.00
                                            @endif
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            {{ $ticket->capacity ? $ticket->capacity . ' total' : 'Unlimited' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 sm:mt-0 w-full sm:w-auto">
                                    <form action="{{ route('tickets.destroy', [$event, $ticket]) }}" method="POST" onsubmit="return confirm('Delete this ticket type?');" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full sm:w-auto text-red-500 hover:text-white bg-red-50 hover:bg-red-500 px-4 py-2.5 rounded-xl text-sm font-bold transition-colors duration-200 flex justify-center items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-16 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                            <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                            </div>
                            <p class="text-lg font-bold text-slate-700">No tickets created yet</p>
                            <p class="text-sm text-slate-500 mt-2 max-w-sm mx-auto">Create your first ticket tier to allow guests to RSVP. You can offer both free and paid options.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Create Ticket Form -->
            <div class="w-full lg:w-1/3">
                <div class="bg-indigo-600 rounded-3xl shadow-xl overflow-hidden relative p-1">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
                    
                    <div class="bg-white/95 backdrop-blur-sm rounded-[22px] p-7">
                        <h3 class="text-xl font-extrabold text-slate-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Create New Tier
                        </h3>
                        
                        <form action="{{ route('tickets.store', $event) }}" method="POST">
                            @csrf
                            
                            <div class="mb-5">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Ticket Name</label>
                                <input type="text" name="name" required class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200 px-4 py-3" placeholder="e.g. VIP Access">
                            </div>

                            <div class="mb-5">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Type</label>
                                <div class="grid grid-cols-2 gap-3 relative bg-slate-100 p-1 rounded-xl">
                                    <input type="radio" name="type" id="type_free" value="free" class="peer/free sr-only" checked onchange="togglePriceField()">
                                    <label for="type_free" class="text-center py-2 px-3 rounded-lg cursor-pointer text-sm font-bold text-slate-600 peer-checked/free:bg-white peer-checked/free:text-indigo-700 peer-checked/free:shadow-sm transition-all duration-200">
                                        Free
                                    </label>
                                    
                                    <input type="radio" name="type" id="type_paid" value="paid" class="peer/paid sr-only" onchange="togglePriceField()">
                                    <label for="type_paid" class="text-center py-2 px-3 rounded-lg cursor-pointer text-sm font-bold text-slate-600 peer-checked/paid:bg-white peer-checked/paid:text-indigo-700 peer-checked/paid:shadow-sm transition-all duration-200">
                                        Paid
                                    </label>
                                </div>
                            </div>

                            <div class="mb-5 hidden opacity-0 transition-opacity duration-300" id="priceContainer">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Price (USD)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-500 font-bold">$</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" name="price" id="priceField" class="w-full pl-8 rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200 px-4 py-3" placeholder="25.00">
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Capacity</label>
                                <input type="number" min="1" name="capacity" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200 px-4 py-3" placeholder="e.g. 100 (Leave blank for unlimited)">
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-indigo-600/30 transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0">
                                Add Ticket Tier
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePriceField() {
            const isPaid = document.getElementById('type_paid').checked;
            const container = document.getElementById('priceContainer');
            const field = document.getElementById('priceField');
            
            if (isPaid) {
                container.classList.remove('hidden');
                // Small delay to allow display:block to apply before changing opacity for transition
                setTimeout(() => container.classList.remove('opacity-0'), 10);
                field.required = true;
            } else {
                container.classList.add('opacity-0');
                setTimeout(() => container.classList.add('hidden'), 300);
                field.required = false;
                field.value = '';
            }
        }
    </script>
</x-app-layout>
