<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Command Center - ' . $event->title) }}
        </h2>
    </x-slot>

<div class="h-[calc(100vh-73px)] flex">
    <!-- Sidebar -->
    <div class="w-96 bg-white border-r border-slate-200 flex flex-col shadow-[4px_0_24px_rgba(0,0,0,0.02)] z-10 relative">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-2xl font-bold text-slate-800">{{ $event->title }}</h2>
            <div class="flex items-center text-sm text-slate-500 mt-2">
                <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse mr-2"></div>
                Live Command Center
            </div>
            
            <div class="mt-6 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Event Stats</p>
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-3xl font-extrabold text-brand-600">{{ $guests->count() }}</p>
                        <p class="text-xs font-medium text-slate-500">Total RSVPs</p>
                    </div>
                    <div class="w-px h-10 bg-slate-200"></div>
                    <div>
                        <p id="active-trackers-count" class="text-3xl font-extrabold text-green-600">0</p>
                        <p class="text-xs font-medium text-slate-500">En Route</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 flex items-center justify-between text-xs font-medium text-slate-400">
                <span>Unique Link: <a href="{{ url('/rsvp/'.$event->tracking_access_token) }}" target="_blank" class="text-brand-500 hover:underline">/rsvp/...</a></span>
                <button onclick="navigator.clipboard.writeText('{{ url('/rsvp/'.$event->tracking_access_token) }}'); alert('Copied!')" class="hover:text-slate-700 transition">Copy</button>
            </div>
            
            <!-- Tabs -->
            <div class="mt-6 flex border-b border-slate-200 overflow-x-auto whitespace-nowrap">
                <button class="px-4 pb-2 text-sm font-semibold text-brand-600 border-b-2 border-brand-600" id="tab-guests" onclick="switchTab('guests')">Guests</button>
                <button class="px-4 pb-2 text-sm font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700" id="tab-stream" onclick="switchTab('stream')">Stream</button>
                <button class="px-4 pb-2 text-sm font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700" id="tab-gallery" onclick="switchTab('gallery')">Gallery</button>
                <button class="px-4 pb-2 text-sm font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700" id="tab-blast" onclick="switchTab('blast')">Email</button>
                @if($event->payment_status === 'paid')
                <button class="px-4 pb-2 text-sm font-semibold text-slate-500 border-b-2 border-transparent hover:text-slate-700" id="tab-promo" onclick="switchTab('promo')">Promo Codes</button>
                @endif
            </div>

            <div class="mt-4">
                <a href="{{ route('support.index', $event) }}" class="flex items-center justify-between w-full p-3 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl transition border border-amber-200">
                    <span class="text-sm font-bold">Support Tickets</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto bg-slate-50/50">
            <!-- Guests Tab -->
            <div id="panel-guests" class="p-4 block">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4 px-2">Guest Activity</h3>
                
                <ul id="guest-list" class="space-y-3">
                    @forelse($guests as $guest)
                    <li id="guest-item-{{ $guest->id }}" class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between transition hover:shadow-md">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center font-bold mr-3 shadow-sm border border-brand-200">
                                {{ substr($guest->name ?? 'G', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $guest->name ?? 'Unnamed Guest' }}</p>
                                <p class="guest-eta text-xs font-medium text-slate-500">Status: Waiting...</p>
                            </div>
                        </div>
                        <div class="guest-indicator w-3 h-3 rounded-full bg-slate-200 border border-slate-300"></div>
                    </li>
                    @empty
                    <div class="text-center py-8">
                        <p class="text-slate-400 text-sm">No guests have RSVP'd yet.</p>
                    </div>
                    @endforelse
                </ul>
            </div>

            <!-- Stream Tab -->
            <div id="panel-stream" class="p-4 hidden">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4 px-2">Livestream Controls</h3>
                
                @if($event->stream_status === 'active')
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Active Stream</span>
                        </div>
                        <p class="text-sm font-medium text-slate-700 mb-1">Mux Playback ID</p>
                        <code class="block bg-slate-100 p-2 rounded text-xs break-all text-slate-600">{{ $event->mux_playback_id }}</code>
                        
                        <form action="{{ route('events.end-stream', $event) }}" method="POST" class="mt-4" onsubmit="return confirm('Are you sure you want to end this stream?')">
                            @csrf
                            @method('POST')
                            <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-red-700 transition">End Stream</button>
                        </form>
                    </div>
                @else
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 mb-4">
                        <form action="{{ route('events.start-stream', $event) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">YouTube Stream Key (Optional)</label>
                                <input type="text" name="youtube_stream_key" class="w-full text-sm rounded border-slate-300" placeholder="xxxx-xxxx-xxxx-xxxx">
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Facebook Stream Key (Optional)</label>
                                <input type="text" name="facebook_stream_key" class="w-full text-sm rounded border-slate-300" placeholder="xxxx-xxxx-xxxx-xxxx">
                            </div>
                            <button type="submit" class="w-full bg-brand-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-brand-700 transition">Start Mux Livestream</button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Gallery Tab -->
            <div id="panel-gallery" class="p-4 hidden">
                <div class="flex items-center justify-between mb-4 px-2">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Photo Gallery</h3>
                    <button onclick="fetchGallery()" class="text-xs text-brand-600 font-semibold hover:underline">Refresh</button>
                </div>

                <div id="gallery-grid" class="grid grid-cols-2 gap-2">
                    <!-- Images loaded via JS -->
                </div>
            </div>

            <!-- Blast Tab -->
            <div id="panel-blast" class="p-4 hidden">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4 px-2">Send Announcement</h3>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 text-sm text-blue-700 rounded-r-lg">
                    <p class="font-bold mb-1">Note:</p>
                    <p>Do not use this to announce Date, Venue, or Direction changes. The system automatically sends an email to all guests if you update those details in your Event Settings. Use this tool for custom announcements like 'Parking is full' or 'Don't forget your ID'.</p>
                </div>
                <form action="{{ route('events.blast', $event) }}" method="POST" class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Subject Line</label>
                        <input type="text" name="subject" required class="w-full text-sm rounded border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="e.g. Important Parking Update!">
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Message</label>
                        <textarea name="message_body" required rows="4" class="w-full text-sm rounded border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="Type your announcement here..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-brand-600 text-white py-3 rounded-lg text-sm font-semibold hover:bg-brand-700 transition shadow-sm" onclick="return confirm('Send this email to ALL registered guests right now?')">Blast to All Guests</button>
                </form>
            </div>

            <!-- Promo Codes Tab -->
            @if($event->payment_status === 'paid')
            <div id="panel-promo" class="p-4 hidden">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4 px-2">Manage Promo Codes</h3>
                
                <form action="{{ route('promo-codes.store', $event) }}" method="POST" class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 mb-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Promo Code</label>
                            <input type="text" name="code" required class="w-full text-sm rounded border-slate-300 uppercase" placeholder="e.g. EARLYBIRD20">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Type</label>
                            <select name="discount_type" required class="w-full text-sm rounded border-slate-300">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Amount</label>
                            <input type="number" step="0.01" min="0.01" name="discount_amount" required class="w-full text-sm rounded border-slate-300" placeholder="e.g. 20">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Usage Limit</label>
                            <input type="number" min="1" name="usage_limit" class="w-full text-sm rounded border-slate-300" placeholder="Optional">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Expiry Date</label>
                            <input type="date" name="expires_at" class="w-full text-sm rounded border-slate-300">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-brand-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-brand-700 transition shadow-sm">Create Promo Code</button>
                </form>

                <div class="space-y-3">
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-2">Active Codes</h4>
                    @forelse($event->promoCodes as $promo)
                        <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-100 flex justify-between items-center">
                            <div>
                                <p class="font-bold text-slate-800 font-mono">{{ $promo->code }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ $promo->discount_type === 'percentage' ? intval($promo->discount_amount).'%' : '₦'.number_format($promo->discount_amount, 2) }} OFF
                                    @if($promo->usage_limit)
                                        &middot; {{ $promo->times_used }}/{{ $promo->usage_limit }} used
                                    @else
                                        &middot; {{ $promo->times_used }} used
                                    @endif
                                </p>
                            </div>
                            <form action="{{ route('promo-codes.destroy', [$event, $promo]) }}" method="POST" onsubmit="return confirm('Delete this promo code?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 px-2 italic">No promo codes created yet.</p>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Map Area -->
    <div class="flex-1 relative bg-slate-100">
        <div id="host-map" class="absolute inset-0 w-full h-full"></div>
        
        <!-- Map Overlay Controls -->
        <div class="absolute top-6 left-6 bg-white/90 backdrop-blur-md p-3.5 rounded-2xl shadow-lg border border-white/50 flex space-x-5">
            <div class="flex items-center">
                <span class="w-3.5 h-3.5 rounded-full bg-brand-500 mr-2.5 shadow-sm border border-white"></span>
                <span class="text-sm font-semibold text-slate-700">Guests en Route</span>
            </div>
            <div class="flex items-center">
                <span class="w-3.5 h-3.5 rounded-full bg-slate-900 mr-2.5 shadow-sm border border-white"></span>
                <span class="text-sm font-semibold text-slate-700">Venue</span>
            </div>
            <div class="flex items-center">
                <span class="w-3.5 h-3.5 rounded-full bg-green-500 mr-2.5 shadow-sm border border-white"></span>
                <span class="text-sm font-semibold text-slate-700">Arrived</span>
            </div>
        </div>
    </div>
</div>

    @push('scripts')
    <script src="{{ $nodeServerUrl }}/socket.io/socket.io.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key', env('GOOGLE_MAPS_API_KEY')) }}"></script>
<script>
    const eventLat = {{ $event->latitude }};
    const eventLng = {{ $event->longitude }};
    const eventToken = "{{ $event->tracking_access_token }}";
    
    let map;
    let venueMarker;
    let guestMarkers = {};
    let activeTrackers = 0;

    function initHostMap() {
        if(typeof google === 'undefined') {
            console.warn("Google Maps not loaded.");
            return;
        }

        map = new google.maps.Map(document.getElementById('host-map'), {
            center: { lat: eventLat, lng: eventLng },
            zoom: 14,
            styles: [
                {
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#f5f5f5"
                        }
                    ]
                },
                {
                    "elementType": "labels.icon",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "elementType": "labels.text.fill",
                    "stylers": [
                        {
                            "color": "#616161"
                        }
                    ]
                },
                {
                    "elementType": "labels.text.stroke",
                    "stylers": [
                        {
                            "color": "#f5f5f5"
                        }
                    ]
                },
                {
                    "featureType": "administrative.land_parcel",
                    "elementType": "labels.text.fill",
                    "stylers": [
                        {
                            "color": "#bdbdbd"
                        }
                    ]
                },
                {
                    "featureType": "poi",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#eeeeee"
                        }
                    ]
                },
                {
                    "featureType": "poi",
                    "elementType": "labels.text.fill",
                    "stylers": [
                        {
                            "color": "#757575"
                        }
                    ]
                },
                {
                    "featureType": "poi.park",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#e5e5e5"
                        }
                    ]
                },
                {
                    "featureType": "poi.park",
                    "elementType": "labels.text.fill",
                    "stylers": [
                        {
                            "color": "#9e9e9e"
                        }
                    ]
                },
                {
                    "featureType": "road",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#ffffff"
                        }
                    ]
                },
                {
                    "featureType": "road.arterial",
                    "elementType": "labels.text.fill",
                    "stylers": [
                        {
                            "color": "#757575"
                        }
                    ]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#dadada"
                        }
                    ]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "labels.text.fill",
                    "stylers": [
                        {
                            "color": "#616161"
                        }
                    ]
                },
                {
                    "featureType": "road.local",
                    "elementType": "labels.text.fill",
                    "stylers": [
                        {
                            "color": "#9e9e9e"
                        }
                    ]
                },
                {
                    "featureType": "transit.line",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#e5e5e5"
                        }
                    ]
                },
                {
                    "featureType": "transit.station",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#eeeeee"
                        }
                    ]
                },
                {
                    "featureType": "water",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "color": "#c9c9c9"
                        }
                    ]
                },
                {
                    "featureType": "water",
                    "elementType": "labels.text.fill",
                    "stylers": [
                        {
                            "color": "#9e9e9e"
                        }
                    ]
                }
            ],
            disableDefaultUI: true,
            zoomControl: true,
        });

        venueMarker = new google.maps.Marker({
            position: { lat: eventLat, lng: eventLng },
            map: map,
            title: '{{ $event->venue_name }}',
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 12,
                fillColor: "#0f172a", // slate-900
                fillOpacity: 1,
                strokeWeight: 3,
                strokeColor: "#ffffff"
            }
        });
    }

    // Connect to Node.js Socket Server
    const socket = io('{{ $nodeServerUrl }}', {
        auth: {
            role: 'host',
            token: eventToken
        }
    });

    socket.on('connect', () => {
        console.log('Connected to Command Center');
    });

    socket.on('location_update', (data) => {
        const { guest_id, guest_name, lat, lng, etaText } = data;
        
        const pos = { lat, lng };
        if (!guestMarkers[guest_id]) {
            guestMarkers[guest_id] = new google.maps.Marker({
                position: pos,
                map: map,
                title: guest_name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 9,
                    fillColor: "#0ea5e9", // brand-500
                    fillOpacity: 1,
                    strokeWeight: 2,
                    strokeColor: "#ffffff"
                }
            });
            activeTrackers++;
            document.getElementById('active-trackers-count').innerText = activeTrackers;
        } else {
            guestMarkers[guest_id].setPosition(pos);
        }

        const listItem = document.getElementById(`guest-item-${guest_id}`);
        if (listItem) {
            listItem.querySelector('.guest-eta').innerText = `ETA: ${etaText}`;
            listItem.querySelector('.guest-indicator').classList.remove('bg-slate-200', 'border-slate-300');
            listItem.querySelector('.guest-indicator').classList.add('bg-green-500', 'border-green-600', 'animate-pulse');
        }
    });

    socket.on('guest_checked_in', (data) => {
         const listItem = document.getElementById(`guest-item-${data.guest_id}`);
         if (listItem) {
             listItem.querySelector('.guest-eta').innerText = `Arrived! 🎉`;
             listItem.querySelector('.guest-indicator').className = 'guest-indicator w-3 h-3 rounded-full bg-brand-500 border border-brand-600';
         }
         
         if (guestMarkers[data.guest_id]) {
             guestMarkers[data.guest_id].setIcon({
                path: google.maps.SymbolPath.CIRCLE,
                scale: 11,
                fillColor: "#10b981", // green-500
                fillOpacity: 1,
                strokeWeight: 2,
                strokeColor: "#ffffff"
             });
         }
    });

    window.onload = function() {
        initHostMap();
        fetchGallery();
    };

    // Tab Switching Logic
    function switchTab(tabId) {
        const tabs = ['guests', 'stream', 'gallery', 'blast', 'promo'];
        tabs.forEach(t => {
            const panel = document.getElementById('panel-' + t);
            const btn = document.getElementById('tab-' + t);
            if (panel && btn) {
                panel.classList.add('hidden');
                panel.classList.remove('block');
                
                btn.classList.remove('text-brand-600', 'border-brand-600');
                btn.classList.add('text-slate-500', 'border-transparent');
            }
        });
        
        const activePanel = document.getElementById('panel-' + tabId);
        const activeBtn = document.getElementById('tab-' + tabId);
        
        if (activePanel && activeBtn) {
            activePanel.classList.remove('hidden');
            activePanel.classList.add('block');
            
            activeBtn.classList.remove('text-slate-500', 'border-transparent');
            activeBtn.classList.add('text-brand-600', 'border-brand-600');
        }
    }

    // Gallery Fetch Logic
    function fetchGallery() {
        fetch(`/api/events/${eventToken}/gallery`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const grid = document.getElementById('gallery-grid');
                    grid.innerHTML = '';
                    if (data.data.length === 0) {
                        grid.innerHTML = '<p class="col-span-2 text-center text-sm text-slate-400 py-4">No photos yet.</p>';
                    } else {
                        data.data.forEach(media => {
                            grid.innerHTML += `
                                <div class="rounded-lg overflow-hidden bg-slate-200 aspect-square">
                                    <img src="${media.media_url}" class="w-full h-full object-cover">
                                </div>
                            `;
                        });
                    }
                }
            });
    }
</script>
    @endpush
</x-app-layout>
