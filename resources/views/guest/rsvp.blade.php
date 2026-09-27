<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>RSVP - {{ $event->title }}</title>
    
    <!-- Standard SEO Meta Tags -->
    <meta name="description" content="You've been invited to {{ $event->title }}. RSVP now and view event details.">
    <meta name="keywords" content="rsvp, event invitation, {{ $event->title }}, attend">
    <meta name="author" content="{{ config('app.name', 'Laravel') }}">

    <!-- Open Graph (Facebook/LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="RSVP - {{ $event->title }}">
    <meta property="og:description" content="You've been invited to {{ $event->title }}. RSVP now and view event details.">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="RSVP - {{ $event->title }}">
    <meta name="twitter:description" content="You've been invited to {{ $event->title }}. RSVP now and view event details.">
    <meta name="twitter:image" content="{{ asset('images/og-image.jpg') }}">
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
                        theme: '{{ $event->theme_color ?? "#0ea5e9" }}'
                    }
                }
            }
        }
    </script>
    <style>
        .btn-theme {
            background-color: var(--theme-color, {{ $event->theme_color ?? "#0ea5e9" }});
            box-shadow: 0 10px 25px -5px var(--theme-color, {{ $event->theme_color ?? "#0ea5e9" }}80);
        }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col font-sans items-center justify-center p-4" style="background-color: {{ $event->theme_color ?? '#0ea5e9' }}15;">
    
    <div class="bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.1)] p-8 w-full max-w-md mx-auto relative overflow-hidden border border-white">
        <div class="absolute top-0 left-0 w-full h-2.5" style="background-color: {{ $event->theme_color ?? '#0ea5e9' }}"></div>
        
        <div class="text-center mb-8 mt-4">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 mb-2">{{ $event->title }}</h1>
            <div class="flex items-center justify-center text-slate-500 font-medium text-sm mb-1">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ $event->venue_name }}
            </div>
            <div class="flex items-center justify-center text-slate-500 font-medium text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ \Carbon\Carbon::parse($event->event_date)->format('l, F j, Y g:i A') }}
            </div>
            @if($event->manual_directions)
            <div class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-100 text-left">
                <div class="flex items-center text-slate-700 font-semibold mb-2 text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    Directions
                </div>
                <p class="text-sm text-slate-600 leading-relaxed">{{ $event->manual_directions }}</p>
            </div>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded-lg text-center mb-6 font-semibold">
                {{ session('success') }}
            </div>
        @else
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <strong class="font-bold">Oops!</strong>
                    <ul class="list-disc pl-5 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('guest.store', $event->tracking_access_token) }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Your Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-theme/50 focus:border-theme transition-colors text-slate-900 bg-slate-50" placeholder="e.g. John Doe">
                </div>

                <div class="mb-5">
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-theme/50 focus:border-theme transition-colors text-slate-900 bg-slate-50" placeholder="john@example.com">
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Are you attending?</label>
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="rsvp_status" value="attending" class="peer sr-only" required>
                            <div class="text-center py-3 px-4 rounded-xl border border-slate-200 peer-checked:border-theme peer-checked:bg-theme/10 peer-checked:text-theme font-semibold transition-all">
                                Yes, I'll be there
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="rsvp_status" value="declined" class="peer sr-only" required>
                            <div class="text-center py-3 px-4 rounded-xl border border-slate-200 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-600 font-semibold transition-all">
                                No, I can't make it
                            </div>
                        </label>
                </div>

                @if($event->tickets->count() > 0)
                <div class="mb-8" id="ticketSelectionContainer">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Select a Ticket</label>
                    <div class="space-y-3">
                        @foreach($event->tickets as $ticket)
                        <label class="flex items-center p-4 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition peer-checked:border-theme peer-checked:bg-theme/5">
                            <input type="radio" name="ticket_id" value="{{ $ticket->id }}" class="mr-3 h-4 w-4 text-theme focus:ring-theme" required>
                            <div class="flex-1 flex justify-between items-center">
                                <span class="font-bold text-slate-800">{{ $ticket->name }}</span>
                                <span class="font-semibold text-sm {{ $ticket->type === 'free' ? 'text-green-600' : 'text-slate-600' }}">
                                    @if($ticket->type === 'free')
                                        Free
                                    @else
                                        ${{ number_format($ticket->price, 2) }}
                                    @endif
                                </span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <button type="submit" class="w-full text-white font-bold text-lg py-4 px-6 rounded-2xl btn-theme transition-transform hover:scale-[1.02] active:scale-[0.98]">
                    Send RSVP
                </button>
            </form>
        @endif
    </div>

    <script>
        // Only show ticket selection if they select "attending"
        document.querySelectorAll('input[name="rsvp_status"]').forEach((elem) => {
            elem.addEventListener("change", function(event) {
                var item = event.target.value;
                var container = document.getElementById('ticketSelectionContainer');
                var ticketInputs = document.querySelectorAll('input[name="ticket_id"]');
                if(container) {
                    if(item === 'attending') {
                        container.style.display = 'block';
                        ticketInputs.forEach(input => input.required = true);
                    } else {
                        container.style.display = 'none';
                        ticketInputs.forEach(input => input.required = false);
                    }
                }
            });
        });
    </script>
    
    <div class="mt-8 text-center text-xs font-medium" style="color: {{ $event->theme_color ?? '#0ea5e9' }}90;">
        &copy; {{ date('Y') }} {{ config('app.name', 'Eventio') }}. Powered by <a href="https://q4iltd.com" target="_blank" class="font-bold hover:underline">Q4I</a><br>
        <div class="mt-2 flex items-center justify-center space-x-3">
            <a href="{{ route('terms') }}" class="hover:underline">Terms & Conditions</a>
            <span style="opacity: 0.5;">|</span>
            <a href="{{ route('privacy') }}" class="hover:underline">Privacy Policy</a>
        </div>
    </div>
</body>
</html>
