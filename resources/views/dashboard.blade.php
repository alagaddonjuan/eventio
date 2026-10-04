<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Host Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Financial Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Gross Ticket Sales</p>
                    <p class="mt-2 text-3xl font-extrabold text-gray-900">₦{{ number_format($totalSales, 2) }}</p>
                    <p class="mt-1 text-sm text-gray-500">Total revenue generated from all events.</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-emerald-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Your Payouts</p>
                            <p class="mt-2 text-3xl font-extrabold text-gray-900">₦{{ number_format($totalPayout, 2) }}</p>
                            <p class="mt-1 text-sm text-gray-500">Expected payout to your bank account.</p>
                        </div>
                        @if(!auth()->user()->bankAccount || !auth()->user()->bankAccount->account_number)
                        <a href="{{ route('profile.edit') }}" class="text-xs font-bold text-red-600 bg-red-50 py-1 px-3 rounded-full hover:bg-red-100">Setup Bank details →</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold">Your Events</h3>
                        <a href="{{ route('home') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow transition">
                            + Create New Event
                        </a>
                    </div>

                    @if($events->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stats</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($events as $event)
                                    @php
                                        $eventSales = \App\Models\Payment::where('event_id', $event->id)->where('status', 'successful')->sum('amount');
                                        $eventPayout = \App\Models\Payment::where('event_id', $event->id)->where('status', 'successful')->sum('host_payout');
                                    @endphp
                                    <tr class="hover:bg-indigo-50 transition-colors duration-200 group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">{{ $event->title }}</div>
                                            <div class="text-sm text-gray-500">{{ $event->venue_name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y h:i A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 mb-1">
                                                {{ $event->guests->count() }} guests
                                            </span>
                                            @if($event->payment_status === 'paid')
                                            <div class="text-xs text-slate-500 mt-1">
                                                Gross: ₦{{ number_format($eventSales, 2) }}<br>
                                                Payout: <span class="font-bold text-emerald-600">₦{{ number_format($eventPayout, 2) }}</span>
                                            </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                            <a href="{{ route('events.edit', $event) }}" class="text-amber-600 hover:text-amber-900">Edit</a>
                                            <a href="{{ route('tickets.index', $event) }}" class="text-emerald-600 hover:text-emerald-900 font-semibold">Manage Tickets</a>
                                            <a href="{{ route('affiliates.manage', $event) }}" class="text-fuchsia-600 hover:text-fuchsia-900">Affiliate Program</a>
                                            <a href="{{ route('events.command-center', $event) }}" class="text-indigo-600 hover:text-indigo-900">Command Center</a>
                                            <button onclick="copyLink('{{ route('guest.rsvp', $event->tracking_access_token) }}')" class="text-blue-600 hover:text-blue-900">Copy Link</button>
                                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-300 flex flex-col items-center justify-center">
                            <svg class="w-32 h-32 text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="text-xl font-bold text-gray-700 mb-2">No events found</h3>
                            <p class="text-gray-500 mb-6 max-w-sm mx-auto">You haven't created any events yet. Get started by creating your first event and start tracking guests!</p>
                            <a href="{{ route('home') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition transform hover:-translate-y-1">Create your first event</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Script to copy to clipboard -->
    <script>
        function copyLink(url) {
            navigator.clipboard.writeText(url).then(function() {
                alert('Guest Tracking Link copied to clipboard!\nShare this with your guests.');
            }, function(err) {
                console.error('Async: Could not copy text: ', err);
            });
        }
    </script>
</x-app-layout>
