<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $guest->event->title ?? 'Event' }} - RSVP</title>
    <!-- SEO Meta Tags -->
    <meta name="description" content="Access your live event portal for {{ $guest->event->title ?? 'the event' }}. View details, track your arrival, and share photos.">
    <meta name="keywords" content="event portal, live event, {{ $guest->event->title ?? 'event' }}, eventio">
    <meta property="og:title" content="{{ $guest->event->title ?? 'Event' }} - Live Portal">
    <meta property="og:description" content="Access your live event portal for {{ $guest->event->title ?? 'the event' }}. View details, track your arrival, and share photos.">
    <meta property="og:type" content="website">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Mux Player -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/@mux/mux-player"></script>
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
        .glass-panel {
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid rgba(255, 255, 255, 0.8);
        }
        .btn-theme {
            background-color: var(--theme-color, {{ $guest->event->theme_color ?? "#0ea5e9" }});
            box-shadow: 0 10px 25px -5px var(--theme-color, {{ $guest->event->theme_color ?? "#0ea5e9" }}80);
        }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col font-sans" style="background-color: {{ $guest->event->theme_color ?? '#0ea5e9' }}15;">
    <!-- Map Container -->
    <div id="map" class="absolute inset-0 z-0 hidden w-full h-full"></div>

    <main class="relative z-10 flex flex-col min-h-screen justify-center py-8 px-4">
        
        <!-- Welcome Card (Phase A & B) -->
        <div id="welcome-card" class="bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.1)] p-8 transition-transform duration-700 ease-[cubic-bezier(0.34,1.56,0.64,1)] transform translate-y-0 w-full max-w-md mx-auto relative overflow-hidden border border-white">
            <div class="absolute top-0 left-0 w-full h-2.5" style="background-color: {{ $guest->event->theme_color ?? '#0ea5e9' }}"></div>
            
            <div class="text-center mb-8 mt-4">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full mb-5" style="background-color: {{ $guest->event->theme_color ?? '#0ea5e9' }}20; color: {{ $guest->event->theme_color ?? '#0ea5e9' }}">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 mb-2">{{ $guest->event->title ?? 'Event Name' }}</h1>
                <div class="flex items-center justify-center text-slate-500 font-medium text-sm mb-1">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    {{ $guest->event->venue_name ?? 'Location' }}
                </div>
                <div class="flex items-center justify-center text-slate-500 font-medium text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $guest->event->event_date ? $guest->event->event_date->format('l, F j, Y g:i A') : 'TBD' }}
                </div>
                @if($guest->event->manual_directions)
                <div class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-100 text-left">
                    <div class="flex items-center text-slate-700 font-semibold mb-2 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        Directions
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $guest->event->manual_directions }}</p>
                </div>
                @endif
            </div>

            <div class="bg-slate-50/80 rounded-2xl p-5 mb-6 text-center border border-slate-100">
                <p class="text-slate-600 text-[15px] leading-relaxed">
                    Hello <span class="font-bold text-slate-900">{{ $guest->name ?? 'Guest' }}</span>! 
                    @if($phase === 'A')
                        Your event is coming up! Check back on the day of the event to start your live journey.
                    @elseif($phase === 'B')
                        It's event day! Tap below when you're heading to the venue so the host can ensure everything is ready for your arrival.
                    @elseif($phase === 'D')
                        The event has concluded! Thanks for joining us.
                    @endif
                </p>
            </div>

            @if($guest->ticket)
            <div class="mb-8 border-t border-dashed border-slate-300 pt-6 text-center">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Your Ticket</h3>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm mx-auto inline-block text-left relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full" style="background-color: {{ $guest->event->theme_color ?? '#0ea5e9' }}"></div>
                    <div class="pl-4">
                        <div class="text-lg font-bold text-slate-900">{{ $guest->ticket->name }}</div>
                        <div class="text-xs text-slate-500 uppercase">{{ $guest->ticket->type }}</div>
                        <div class="mt-4 flex justify-center bg-white p-2 rounded">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate(route('guest.verify', $guest->unique_token)) !!}
                        </div>
                        <div class="text-[10px] text-center text-slate-400 mt-1 font-mono tracking-widest">{{ substr($guest->barcode_data ?? $guest->unique_token, 0, 12) }}...</div>
                    </div>
                </div>
            </div>
            @endif

            @if($phase === 'B' || $phase === 'A') <!-- Allow in A for testing if needed -->
            <button id="start-journey-btn" class="w-full text-white font-bold text-lg py-4 px-6 rounded-2xl btn-theme transition-transform hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center space-x-2 mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>Start My Journey</span>
            </button>
            @endif

            <a href="{{ route('guest.support', $guest->unique_token) }}" class="w-full bg-slate-100 text-slate-800 font-bold text-lg py-4 px-6 rounded-2xl transition-transform hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center space-x-2 mb-4 border border-slate-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <span>Message Host</span>
            </a>

            <!-- Event Media Section -->
            <div class="mt-6 pt-6 border-t border-slate-200">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Event Media</h3>
                
                @if($guest->event->stream_status === 'active' && $guest->event->mux_playback_id)
                <div class="mb-6 rounded-xl overflow-hidden shadow-lg border border-slate-200">
                    <div class="bg-red-600 text-white text-xs font-bold uppercase px-3 py-1 text-center animate-pulse">Live Now</div>
                    <mux-player
                        playback-id="{{ $guest->event->mux_playback_id }}"
                        metadata-video-title="{{ $guest->event->title }}"
                        stream-type="live"
                        class="w-full aspect-video"
                    ></mux-player>
                </div>
                @endif

                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
                    <p class="text-sm font-medium text-slate-700 mb-3">Share your photos with the host and other guests!</p>
                    <input type="file" id="photo-upload" accept="image/*" class="hidden">
                    <button onclick="document.getElementById('photo-upload').click()" class="w-full bg-slate-800 text-white font-semibold py-3 px-4 rounded-lg flex justify-center items-center hover:bg-slate-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Upload a Photo
                    </button>
                    <p id="upload-status" class="text-xs text-brand-600 mt-2 hidden font-semibold"></p>
                </div>

                <!-- Guest Gallery Display -->
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-slate-700">Recent Uploads</h4>
                        <button onclick="fetchGuestGallery()" class="text-xs font-semibold hover:underline" style="color: {{ $guest->event->theme_color ?? '#0ea5e9' }}">Refresh</button>
                    </div>
                    <div id="guest-gallery-grid" class="grid grid-cols-3 gap-2">
                        <!-- Loaded via JS -->
                        <div class="col-span-3 text-center text-xs text-slate-400 py-4">Loading gallery...</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Tracking Card (Phase C) -->
        <div id="tracking-card" class="glass-panel rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.15)] p-7 transition-transform duration-700 ease-[cubic-bezier(0.34,1.56,0.64,1)] transform translate-y-[150%] hidden w-full max-w-md mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900">En Route to Venue</h2>
                    <p class="text-sm font-medium text-slate-500">Host is tracking your arrival</p>
                </div>
                <div class="h-14 w-14 rounded-full flex items-center justify-center relative shadow-sm border border-white" style="background-color: {{ $guest->event->theme_color ?? '#0ea5e9' }}20; color: {{ $guest->event->theme_color ?? '#0ea5e9' }}">
                    <div class="absolute inset-0 rounded-full animate-ping opacity-75" style="background-color: {{ $guest->event->theme_color ?? '#0ea5e9' }}40"></div>
                    <svg class="w-7 h-7 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            
            <div class="flex items-center justify-between bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <div class="text-center flex-1">
                    <p class="text-[11px] text-slate-400 uppercase tracking-widest font-bold mb-1.5">ETA</p>
                    <p id="eta-display" class="text-3xl font-extrabold text-slate-800">...</p>
                </div>
                <div class="w-px h-16 bg-slate-100"></div>
                <div class="text-center flex-1">
                    <p class="text-[11px] text-slate-400 uppercase tracking-widest font-bold mb-1.5">Status</p>
                    <div class="inline-flex items-center justify-center bg-green-50 px-3 py-1.5 rounded-full border border-green-100">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 mr-2 animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                        <span id="status-display" class="text-sm font-bold text-green-700">Tracking</span>
                    </div>
                </div>
            </div>
            
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $guest->event->latitude }},{{ $guest->event->longitude }}" target="_blank" class="mt-5 w-full block text-center py-3.5 rounded-xl text-white font-bold text-[15px] shadow-lg hover:-translate-y-0.5 transition-all duration-200" style="background-color: {{ $guest->event->theme_color ?? '#0ea5e9' }}">
                Open Maps Navigation
            </a>
            
            <p class="text-[13px] font-medium text-center text-slate-400 mt-4">Keep this screen open for accurate tracking.</p>
        </div>

        <div class="mt-8 text-center text-[11px] font-medium" style="color: {{ $guest->event->theme_color ?? '#0ea5e9' }}90; text-shadow: 0 1px 2px rgba(255,255,255,0.8);">
            &copy; {{ date('Y') }} {{ config('app.name', 'Eventio') }}. Powered by <a href="https://q4iltd.com" target="_blank" class="font-bold hover:underline">Q4I</a><br>
            <div class="mt-1 flex items-center justify-center space-x-3">
                <a href="{{ route('terms') }}" class="hover:underline">Terms</a>
                <span style="opacity: 0.5;">|</span>
                <a href="{{ route('privacy') }}" class="hover:underline">Privacy</a>
            </div>
        </div>
    </main>

    <!-- Configuration for guest-tracker.js -->
    <script>
        window.EVENT_CONFIG = {
            eventId: {{ $guest->event->id ?? 0 }},
            guestId: {{ $guest->id ?? 0 }},
            guestName: "{{ $guest->name ?? 'Guest' }}",
            uniqueToken: "{{ $guest->unique_token ?? '' }}",
            eventToken: "{{ $guest->event->tracking_access_token ?? '' }}",
            nodeServerUrl: "{{ env('NODE_SERVER_URL', 'http://localhost:3000') }}",
            venueLat: {{ $guest->event->latitude ?? 0 }},
            venueLng: {{ $guest->event->longitude ?? 0 }}
        };
    </script>
    
    <!-- Load Socket.io and Google Maps -->
    <script src="{{ env('NODE_SERVER_URL', 'http://localhost:3000') }}/socket.io/socket.io.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ trim(config('services.google.maps_key', env('GOOGLE_MAPS_API_KEY'))) }}&libraries=geometry"></script>
    
    <!-- Include the tracker logic -->
    <script src="/js/guest-tracker.js?v={{ time() }}"></script>

    <script>
        // Simple UI transition logic
        document.getElementById('start-journey-btn')?.addEventListener('click', () => {
            const welcomeCard = document.getElementById('welcome-card');
            welcomeCard.classList.add('translate-y-[150%]', 'opacity-0');
            
            setTimeout(() => {
                welcomeCard.classList.add('hidden');
                
                const trackingCard = document.getElementById('tracking-card');
                trackingCard.classList.remove('hidden');
                document.getElementById('map').classList.remove('hidden');
                
                // small delay to allow display block to apply before animating transform
                setTimeout(() => {
                    trackingCard.classList.remove('translate-y-[150%]');
                }, 50);
                
                // Call the initialization function from guest-tracker.js
                if(typeof startJourney === 'function') {
                    startJourney(
                        window.EVENT_CONFIG.venueLat, 
                        window.EVENT_CONFIG.venueLng, 
                        window.EVENT_CONFIG.nodeServerUrl, 
                        window.EVENT_CONFIG.uniqueToken,
                        `/api/guest/${window.EVENT_CONFIG.uniqueToken}/check-in`
                    );
                } else if(typeof initGuestTracker === 'function') {
                    initGuestTracker();
                }
            }, 500);
        });

        // Photo Upload Logic
        const photoInput = document.getElementById('photo-upload');
        const statusEl = document.getElementById('upload-status');
        
        photoInput?.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('image', file);
            formData.append('uploader_type', 'guest');
            formData.append('uploader_id', window.EVENT_CONFIG.guestId);
            
            statusEl.innerText = "Uploading...";
            statusEl.classList.remove('hidden', 'text-red-600', 'text-green-600');
            statusEl.classList.add('text-brand-600');
            
            try {
                const res = await fetch(`/api/events/${window.EVENT_CONFIG.eventToken}/gallery`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch(parseErr) {
                    throw new Error(`Status ${res.status}: ${text.substring(0, 60)}`);
                }
                
                if(data.status === 'success') {
                    statusEl.innerText = "Photo uploaded successfully! 🎉";
                    statusEl.classList.add('text-green-600');
                } else {
                    statusEl.innerText = "Upload failed: " + (data.message || "Unknown error");
                    statusEl.classList.add('text-red-600');
                }
            } catch (err) {
                statusEl.innerText = "Error: " + err.message;
                statusEl.classList.add('text-red-600');
            }
            
            // Reset input so they can upload again
            photoInput.value = '';
            
            setTimeout(() => {
                statusEl.classList.add('hidden');
            }, 5000);
        });

        // Guest Gallery Logic
        function fetchGuestGallery() {
            const grid = document.getElementById('guest-gallery-grid');
            if(!grid) return;

            fetch(`/api/events/${window.EVENT_CONFIG.eventToken}/gallery`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        grid.innerHTML = '';
                        if (data.data.length === 0) {
                            grid.innerHTML = '<div class="col-span-3 text-center text-xs text-slate-400 py-4">No photos yet. Be the first!</div>';
                        } else {
                            data.data.forEach(media => {
                                grid.innerHTML += `
                                    <a href="${media.media_url}" target="_blank" class="block rounded-lg overflow-hidden bg-slate-200 aspect-square border border-slate-200 shadow-sm hover:shadow-md transition">
                                        <img src="${media.media_url}" class="w-full h-full object-cover">
                                    </a>
                                `;
                            });
                        }
                    }
                })
                .catch(err => {
                    grid.innerHTML = '<div class="col-span-3 text-center text-xs text-red-400 py-4">Failed to load gallery.</div>';
                });
        }

        // Load gallery on page load
        document.addEventListener('DOMContentLoaded', () => {
            fetchGuestGallery();
        });
    </script>
</body>
</html>
