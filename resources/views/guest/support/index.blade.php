<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Message Host - {{ $guest->event->title ?? 'Event' }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        theme: '{{ $guest->event->theme_color ?? "#0ea5e9" }}'
                    }
                }
            }
        }
    </script>
    <style>
        .btn-theme {
            background-color: var(--theme-color, {{ $guest->event->theme_color ?? "#0ea5e9" }});
            box-shadow: 0 10px 25px -5px var(--theme-color, {{ $guest->event->theme_color ?? "#0ea5e9" }}80);
        }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col font-sans" style="background-color: {{ $guest->event->theme_color ?? '#0ea5e9' }}15;">
    
    <div class="max-w-2xl w-full mx-auto p-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('guest.portal', $guest->unique_token) }}" class="text-theme font-semibold text-sm hover:underline mb-2 inline-block">&larr; Back to Portal</a>
                <h1 class="text-2xl font-bold">Message the Host</h1>
                <p class="text-slate-500 text-sm">Have a question about {{ $guest->event->title }}?</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 font-medium">
                {{ session('success') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 font-medium">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-8">
            <!-- Existing Tickets -->
            @foreach($tickets as $ticket)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="bg-slate-50 p-4 border-b border-slate-100 flex justify-between items-center">
                    <h2 class="font-bold text-slate-800">{{ $ticket->subject }}</h2>
                    <span class="text-xs font-bold px-2 py-1 rounded {{ $ticket->status === 'open' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700' }}">
                        {{ strtoupper($ticket->status) }}
                    </span>
                </div>
                
                <div class="p-4 space-y-4 max-h-96 overflow-y-auto">
                    @foreach($ticket->messages as $msg)
                    <div class="flex {{ $msg->sender_type === 'guest' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] rounded-2xl px-4 py-2 {{ $msg->sender_type === 'guest' ? 'bg-theme text-white rounded-tr-sm' : 'bg-slate-100 text-slate-800 rounded-tl-sm' }}">
                            <p class="text-sm whitespace-pre-wrap">{{ $msg->message }}</p>
                            <div class="text-[10px] mt-1 {{ $msg->sender_type === 'guest' ? 'text-white/70' : 'text-slate-400' }}">
                                {{ $msg->created_at->format('M d, g:i A') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($ticket->status === 'open')
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    <form action="{{ route('guest.support.reply', $ticket->id) }}" method="POST">
                        @csrf
                        <div class="flex gap-2">
                            <input type="text" name="message" required placeholder="Type a reply..." class="flex-1 px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-theme/50 text-sm">
                            <button type="submit" class="btn-theme text-white px-4 py-2 rounded-xl font-bold text-sm">Send</button>
                        </div>
                    </form>
                </div>
                @else
                <div class="p-4 border-t border-slate-100 bg-slate-50 text-center text-sm text-slate-500 font-medium">
                    This thread is closed.
                </div>
                @endif
            </div>
            @endforeach

            <!-- Create New Ticket -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h2 class="font-bold text-lg mb-4">Start a new conversation</h2>
                <form action="{{ route('guest.support.store', $guest->unique_token) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Subject</label>
                        <input type="text" name="subject" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-theme/50 focus:border-theme bg-slate-50 text-sm" placeholder="What is this regarding?">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Message</label>
                        <textarea name="message" required rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-theme/50 focus:border-theme bg-slate-50 text-sm" placeholder="Type your message here..."></textarea>
                    </div>
                    <button type="submit" class="w-full text-white font-bold py-3 px-6 rounded-xl btn-theme">
                        Send Message
                    </button>
                </form>
            </div>
            
        </div>
    </div>
</body>
</html>
