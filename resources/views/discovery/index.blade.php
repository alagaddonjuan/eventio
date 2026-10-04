<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Explore Events') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Search Bar -->
            <div class="mb-8">
                <form action="{{ route('discovery.index') }}" method="GET" class="relative max-w-2xl mx-auto">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-4 border border-gray-300 rounded-full leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-lg shadow-sm transition-shadow hover:shadow-md" placeholder="Search for events by title or location...">
                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <!-- Event Grid -->
            @if($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($events as $event)
                        <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group border border-gray-100 flex flex-col">
                            <div class="relative h-48 overflow-hidden bg-gray-200">
                                @if($event->banner_image)
                                    <img src="{{ Storage::url($event->banner_image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400" style="background-color: {{ $event->theme_color ?? '#e5e7eb' }}40;">
                                        <svg class="w-16 h-16 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                
                                <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-gray-800 shadow-sm">
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                                </div>
                            </div>
                            
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-1">{{ $event->title }}</h3>
                                <p class="text-gray-500 text-sm mb-4 line-clamp-2 flex-grow">{{ strip_tags($event->description ?? 'Join us for this amazing event!') }}</p>
                                
                                <div class="flex items-center text-gray-500 text-sm mb-4">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="truncate">{{ $event->venue_name }}</span>
                                </div>
                                
                                @if($event->affiliateProgram && $event->affiliateProgram->is_active)
                                    <div class="mb-4 inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Earn {{ $event->affiliateProgram->commission_percentage }}% Commission
                                    </div>
                                @endif
                                
                                <div class="mt-auto space-y-2">
                                    <a href="{{ route('guest.rsvp', $event->tracking_access_token) }}" class="block w-full text-center bg-gray-50 hover:bg-gray-100 text-gray-900 font-semibold py-2.5 px-4 rounded-xl transition-colors border border-gray-200">
                                        View Details
                                    </a>
                                    @if($event->affiliateProgram && $event->affiliateProgram->is_active && auth()->check())
                                        <form action="{{ route('promoter.join', $event) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-colors shadow-sm text-sm">
                                                Become a Promoter
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-10">
                    {{ $events->links() }}
                </div>
            @else
                <div class="text-center py-20 bg-white rounded-3xl shadow-sm border border-gray-100">
                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No events found</h3>
                    <p class="text-gray-500 max-w-md mx-auto">We couldn't find any upcoming events matching your criteria right now. Check back later!</p>
                    @if(request('search'))
                        <a href="{{ route('discovery.index') }}" class="inline-block mt-6 text-indigo-600 font-semibold hover:text-indigo-800">Clear Search</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
