<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
                <a href="{{ route('marketplace.index') }}" class="text-indigo-400 hover:text-indigo-300 mr-2 flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Marketplace
                </a>
                <span class="text-slate-500 mx-2">/</span>
                {{ $vendorProfile->business_name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-6 py-4 rounded-2xl flex items-center shadow-lg shadow-green-500/5">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Cover Image & Basic Info -->
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl overflow-hidden shadow-2xl">
                        <div class="h-64 bg-slate-800 relative">
                            @if($vendorProfile->cover_image)
                                <img src="{{ Storage::url($vendorProfile->cover_image) }}" alt="{{ $vendorProfile->business_name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-indigo-900 to-purple-900 flex items-center justify-center text-indigo-300">
                                    <svg class="w-24 h-24 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                            @endif
                            <div class="absolute -bottom-6 left-8 bg-slate-900 border-4 border-slate-900 rounded-2xl p-3 shadow-xl">
                                <div class="w-16 h-16 bg-indigo-500/20 text-indigo-400 rounded-xl flex items-center justify-center">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-10 px-8 pb-8">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h1 class="text-3xl font-bold text-white mb-2">{{ $vendorProfile->business_name }}</h1>
                                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-400 text-sm font-medium border border-indigo-500/20">
                                        {{ $vendorProfile->category->name }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $vendorProfile->description }}</div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Quick Info -->
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-6 shadow-2xl">
                        <h3 class="text-lg font-semibold text-white mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Vendor Details
                        </h3>
                        
                        <div class="space-y-4">
                            @if($vendorProfile->location)
                            <div class="flex items-start">
                                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center mr-4 shrink-0">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-sm text-slate-500 mb-1">Location</div>
                                    <div class="text-slate-200">{{ $vendorProfile->location }}</div>
                                </div>
                            </div>
                            @endif

                            @if($vendorProfile->base_price)
                            <div class="flex items-start">
                                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center mr-4 shrink-0">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-sm text-slate-500 mb-1">Base Price</div>
                                    <div class="text-slate-200 text-lg font-semibold">${{ number_format($vendorProfile->base_price, 2) }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Book Vendor -->
                    <div class="bg-gradient-to-br from-indigo-500/10 to-purple-500/10 backdrop-blur-md border border-indigo-500/20 rounded-3xl p-6 shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/20 rounded-full blur-[50px] pointer-events-none"></div>
                        
                        <h3 class="text-xl font-bold text-white mb-6 relative z-10">Request to Book</h3>
                        
                        @if(auth()->id() === $vendorProfile->user_id)
                            <div class="p-4 bg-yellow-500/10 border border-yellow-500/20 text-yellow-400 rounded-xl text-sm relative z-10">
                                This is your own vendor profile.
                            </div>
                        @elseif($events->isEmpty())
                            <div class="p-4 bg-slate-800/50 border border-slate-700 text-slate-300 rounded-xl text-sm relative z-10">
                                You don't have any upcoming events. Create an event first to book vendors.
                                <a href="{{ route('events.store') }}" class="block mt-3 text-indigo-400 hover:text-indigo-300 font-medium">Create Event &rarr;</a>
                            </div>
                        @else
                            <form action="{{ route('marketplace.book', $vendorProfile) }}" method="POST" class="space-y-5 relative z-10">
                                @csrf
                                
                                <div>
                                    <label for="event_id" class="block text-sm font-medium text-slate-300 mb-2">Select Event</label>
                                    <select name="event_id" id="event_id" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3">
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}">{{ $event->title }} ({{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }})</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <label for="message" class="block text-sm font-medium text-slate-300 mb-2">Message to Vendor</label>
                                    <textarea name="message" id="message" rows="4" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3" placeholder="Describe what you need for your event..."></textarea>
                                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                                </div>

                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-indigo-500/30 transition-all duration-200">
                                    Send Booking Request
                                </button>
                                <p class="text-xs text-center text-slate-500 mt-3">You will negotiate final price directly with the vendor.</p>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
