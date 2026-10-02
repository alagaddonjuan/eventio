<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Support Tickets - ' . $event->title) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4">
                <a href="{{ route('events.command-center', $event) }}" class="text-brand-600 hover:underline font-semibold text-sm">&larr; Back to Command Center</a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-6">Guest Inquiries</h3>

                    @if($tickets->isEmpty())
                        <div class="text-center py-8 text-slate-500">
                            No support tickets have been submitted by guests yet.
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($tickets as $ticket)
                                <div class="bg-slate-50 rounded-xl border border-slate-200 overflow-hidden">
                                    <div class="bg-white p-4 border-b border-slate-200 flex justify-between items-center">
                                        <div>
                                            <h4 class="font-bold text-slate-800 text-lg">{{ $ticket->subject }}</h4>
                                            <p class="text-sm text-slate-500">From: <span class="font-semibold">{{ $ticket->guest->name }}</span> ({{ $ticket->guest->email }})</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-bold px-3 py-1 rounded-full {{ $ticket->status === 'open' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }}">
                                                {{ strtoupper($ticket->status) }}
                                            </span>
                                            @if($ticket->status === 'open')
                                            <form action="{{ route('support.close', $ticket->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-xs font-bold px-3 py-1 bg-slate-200 text-slate-700 rounded-full hover:bg-slate-300 transition">Close Ticket</button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
                                        @foreach($ticket->messages as $msg)
                                            <div class="flex {{ $msg->sender_type === 'host' ? 'justify-end' : 'justify-start' }}">
                                                <div class="max-w-[75%] rounded-2xl px-5 py-3 {{ $msg->sender_type === 'host' ? 'bg-brand-600 text-white rounded-tr-sm' : 'bg-white border border-slate-200 text-slate-800 rounded-tl-sm' }}">
                                                    <p class="text-sm whitespace-pre-wrap">{{ $msg->message }}</p>
                                                    <div class="text-[10px] mt-2 {{ $msg->sender_type === 'host' ? 'text-brand-100' : 'text-slate-400' }}">
                                                        {{ $msg->sender_type === 'host' ? 'You' : $ticket->guest->name }} &bull; {{ $msg->created_at->format('M d, g:i A') }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($ticket->status === 'open')
                                    <div class="p-4 bg-white border-t border-slate-200">
                                        <form action="{{ route('support.reply', $ticket->id) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                                            @csrf
                                            <input type="hidden" name="host_reply" value="1">
                                            <input type="text" name="message" required placeholder="Type your reply to {{ $ticket->guest->name }}..." class="flex-1 w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500 text-sm">
                                            <button type="submit" class="w-full sm:w-auto bg-brand-600 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-brand-700 transition shadow-sm">Send Reply</button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
