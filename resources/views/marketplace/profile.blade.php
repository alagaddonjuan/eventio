<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <svg class="w-6 h-6 mr-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            {{ __('Vendor Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-6 py-4 rounded-2xl flex items-center shadow-lg shadow-green-500/5">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Profile Settings -->
                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-8 shadow-2xl">
                    <h3 class="text-xl font-bold text-white mb-6">Store Setup</h3>
                    
                    <form action="{{ route('marketplace.update-profile') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="business_name" class="block text-sm font-medium text-slate-300 mb-2">Business Name</label>
                            <input type="text" name="business_name" id="business_name" value="{{ old('business_name', $profile->business_name ?? '') }}" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3">
                            <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                        </div>

                        <div>
                            <label for="vendor_category_id" class="block text-sm font-medium text-slate-300 mb-2">Category</label>
                            <select name="vendor_category_id" id="vendor_category_id" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3">
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('vendor_category_id', $profile->vendor_category_id ?? '') == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('vendor_category_id')" class="mt-2" />
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-medium text-slate-300 mb-2">Location / Service Area</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $profile->location ?? '') }}" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3" placeholder="e.g. New York, NY or Worldwide">
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <div>
                            <label for="base_price" class="block text-sm font-medium text-slate-300 mb-2">Starting Price ($)</label>
                            <input type="number" step="0.01" name="base_price" id="base_price" value="{{ old('base_price', $profile->base_price ?? '') }}" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3" placeholder="0.00">
                            <x-input-error :messages="$errors->get('base_price')" class="mt-2" />
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-slate-300 mb-2">About Your Business</label>
                            <textarea name="description" id="description" rows="5" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3">{{ old('description', $profile->description ?? '') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-indigo-500/30 transition-all duration-200">
                                {{ $profile ? 'Save Changes' : 'Create Vendor Profile' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Booking Requests -->
                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-8 shadow-2xl flex flex-col h-full">
                    <h3 class="text-xl font-bold text-white mb-6">Booking Requests</h3>
                    
                    @if(!$profile)
                        <div class="flex-grow flex flex-col items-center justify-center text-center p-8 bg-slate-800/30 rounded-2xl border border-dashed border-slate-700">
                            <svg class="w-16 h-16 text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <h4 class="text-lg font-medium text-slate-300 mb-2">No profile yet</h4>
                            <p class="text-slate-500 text-sm max-w-sm">Create your vendor profile to start receiving booking requests from event hosts.</p>
                        </div>
                    @else
                        @if(count($bookings) > 0)
                            <div class="space-y-4 overflow-y-auto pr-2" style="max-height: 600px;">
                                @foreach($bookings as $booking)
                                    <div class="bg-slate-800/80 border border-slate-700 rounded-2xl p-5 hover:border-indigo-500/30 transition-colors">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-bold text-white">{{ $booking->event->title }}</h4>
                                                <div class="text-xs text-slate-400 mt-1">
                                                    {{ \Carbon\Carbon::parse($booking->event->event_date)->format('M d, Y') }} • Host: {{ $booking->event->user->name }}
                                                </div>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-lg 
                                                {{ $booking->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                                {{ $booking->status === 'accepted' ? 'bg-green-500/20 text-green-400' : '' }}
                                                {{ $booking->status === 'rejected' ? 'bg-red-500/20 text-red-400' : '' }}
                                                {{ $booking->status === 'completed' ? 'bg-indigo-500/20 text-indigo-400' : '' }}
                                            ">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="bg-slate-900/50 p-3 rounded-xl text-sm text-slate-300 border border-white/5 italic">
                                            "{{ $booking->message }}"
                                        </div>
                                        
                                        <div class="mt-4 flex items-center justify-between text-sm">
                                            <div class="text-slate-500">{{ $booking->created_at->diffForHumans() }}</div>
                                            <!-- Action buttons for vendor would go here (Accept/Reject/Message) -->
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex-grow flex flex-col items-center justify-center text-center p-8 bg-slate-800/30 rounded-2xl border border-dashed border-slate-700">
                                <svg class="w-16 h-16 text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <h4 class="text-lg font-medium text-slate-300 mb-2">No bookings yet</h4>
                                <p class="text-slate-500 text-sm">When hosts request your services, they will appear here.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
