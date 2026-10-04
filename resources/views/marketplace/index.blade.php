<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <svg class="w-6 h-6 mr-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            {{ __('Eventio Marketplace') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex flex-wrap gap-4 items-center justify-between">
                <div class="flex space-x-2 overflow-x-auto pb-2">
                    <a href="{{ route('marketplace.index') }}" class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !request('category_id') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700' }}">All Categories</a>
                    
                    @foreach($categories as $category)
                        <a href="{{ route('marketplace.index', ['category_id' => $category->id]) }}" class="px-4 py-2 rounded-full text-sm font-medium transition-colors whitespace-nowrap {{ request('category_id') == $category->id ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>

                <a href="{{ route('marketplace.profile') }}" class="px-5 py-2.5 bg-gradient-to-r from-fuchsia-500 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-fuchsia-400 hover:to-purple-500 transition-all shadow-lg shadow-purple-500/30 whitespace-nowrap">
                    Become a Vendor
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($vendors as $vendor)
                    <a href="{{ route('marketplace.show', $vendor) }}" class="group bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden hover:border-indigo-500/50 hover:shadow-2xl hover:shadow-indigo-500/20 transition-all duration-300 flex flex-col">
                        <div class="h-48 bg-slate-800 relative overflow-hidden">
                            @if($vendor->cover_image)
                                <img src="{{ Storage::url($vendor->cover_image) }}" alt="{{ $vendor->business_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-indigo-900/50 to-purple-900/50 flex items-center justify-center text-indigo-300 group-hover:scale-105 transition-transform duration-500">
                                    <svg class="w-16 h-16 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                            @endif
                            <div class="absolute top-3 right-3 bg-indigo-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                {{ $vendor->category->name }}
                            </div>
                        </div>
                        
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-xl font-bold text-white mb-2 group-hover:text-indigo-300 transition-colors">{{ $vendor->business_name }}</h3>
                            <div class="text-slate-400 text-sm mb-4 line-clamp-2">{{ $vendor->description }}</div>
                            
                            <div class="mt-auto space-y-2">
                                @if($vendor->location)
                                <div class="flex items-center text-slate-400 text-sm">
                                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $vendor->location }}
                                </div>
                                @endif
                                
                                @if($vendor->base_price)
                                <div class="flex items-center text-slate-300 font-medium">
                                    <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Starts at ${{ number_format($vendor->base_price, 2) }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-16 text-center bg-white/5 backdrop-blur-md rounded-2xl border border-white/10">
                        <svg class="mx-auto h-16 w-16 text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h3 class="text-xl font-bold text-slate-300 mb-2">No vendors found</h3>
                        <p class="text-slate-500">There are no vendors matching your criteria right now.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $vendors->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
