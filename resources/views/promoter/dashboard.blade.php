<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Promoter Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900 flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold mb-1">Your Promotional Links</h3>
                        <p class="text-sm text-gray-600">Share your unique links to earn commissions on ticket sales.</p>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-emerald-100 text-emerald-800 p-4 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($links->count() > 0)
                <div class="grid grid-cols-1 gap-6">
                    @foreach($links as $link)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-fuchsia-500 flex flex-col md:flex-row justify-between items-start md:items-center">
                            <div class="mb-4 md:mb-0">
                                <h4 class="text-lg font-bold text-gray-900">{{ $link->event->title }}</h4>
                                <p class="text-sm text-gray-500 mb-2">Commission: {{ $link->event->affiliateProgram->commission_percentage }}%</p>
                                <div class="flex items-center space-x-2 bg-gray-100 p-2 rounded-md">
                                    <code class="text-sm text-indigo-600 font-mono">{{ url('/ref/' . $link->unique_code) }}</code>
                                    <button onclick="copyLink('{{ url('/ref/' . $link->unique_code) }}')" class="text-gray-500 hover:text-indigo-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex space-x-6 text-center">
                                <div>
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Clicks</p>
                                    <p class="text-2xl font-black text-gray-900">{{ $link->clicks }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Sales</p>
                                    <p class="text-2xl font-black text-gray-900">{{ $link->sales_count }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Earned</p>
                                    <p class="text-2xl font-black text-emerald-600">₦{{ number_format($link->earnings, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">No Active Promotions</h3>
                    <p class="text-gray-500 max-w-sm mx-auto mb-6">You aren't promoting any events right now. Find an event with an active affiliate program to start earning!</p>
                    <a href="{{ route('discovery.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Find Events to Promote
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        function copyLink(url) {
            navigator.clipboard.writeText(url).then(function() {
                alert('Affiliate link copied to clipboard!');
            });
        }
    </script>
</x-app-layout>
