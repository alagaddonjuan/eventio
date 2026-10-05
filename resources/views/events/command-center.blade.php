<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Command Center - ' . $event->title) }}
            </h2>
            <div class="flex space-x-1 bg-slate-100 p-1 rounded-xl">
                <button class="py-1.5 px-4 text-sm font-bold text-brand-700 bg-white rounded-lg shadow-sm border border-slate-200/60" id="tab-guests" onclick="switchTab('guests')">Guests</button>
                <button class="py-1.5 px-4 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 rounded-lg transition" id="tab-stream" onclick="switchTab('stream')">Stream</button>
                <button class="py-1.5 px-4 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 rounded-lg transition" id="tab-gallery" onclick="switchTab('gallery')">Gallery</button>
                <button class="py-1.5 px-4 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 rounded-lg transition" id="tab-blast" onclick="switchTab('blast')">Email</button>
                <button class="py-1.5 px-4 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 rounded-lg transition" id="tab-analytics" onclick="switchTab('analytics')">Analytics</button>
                <button class="py-1.5 px-4 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 rounded-lg transition" id="tab-registration" onclick="switchTab('registration')">Registration</button>
                <button class="py-1.5 px-4 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 rounded-lg transition" id="tab-team" onclick="switchTab('team')">Team</button>
                @if($event->payment_status === 'paid')
                <button class="py-1.5 px-4 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 rounded-lg transition" id="tab-promo" onclick="switchTab('promo')">Promos</button>
                @endif
            </div>
        </div>
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
            
            <!-- Tabs moved to map header -->

            <div class="mt-4">
                <a href="{{ route('support.index', $event) }}" class="flex items-center justify-between w-full p-3 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl transition border border-amber-200">
                    <span class="text-sm font-bold">Support Tickets</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto bg-slate-50/50">
            @include('events.partials.guests-tab')
            @include('events.partials.analytics-tab')
            @include('events.partials.stream-tab')
            @include('events.partials.gallery-tab')
            @include('events.partials.blast-tab')
            @include('events.partials.promo-tab')
            @include('events.partials.registration-tab')
            @include('events.partials.team-tab')
        </div>
    </div>

    <!-- Map Area -->
    <div class="flex-1 relative bg-slate-100">
        <div id="host-map" class="absolute inset-0 w-full h-full"></div>
        
        <!-- Map Overlay Controls -->
        <div class="absolute top-6 left-6 bg-white/90 backdrop-blur-md p-3.5 rounded-2xl shadow-lg border border-white/50 flex space-x-5 z-20">
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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ env('NODE_SERVER_URL', 'http://localhost:3000') }}/socket.io/socket.io.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ trim(config('services.google.maps_key', env('GOOGLE_MAPS_API_KEY'))) }}&callback=initHostMap" async defer></script>
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
        fetchGallery();
    };

    // Tab Switching Logic
    function switchTab(tabId) {
        const tabs = ['guests', 'analytics', 'stream', 'gallery', 'blast', 'promo', 'registration', 'team'];
        tabs.forEach(t => {
            const panel = document.getElementById('panel-' + t);
            const btn = document.getElementById('tab-' + t);
            if (panel && btn) {
                panel.classList.add('hidden');
                panel.classList.remove('block');
                
                btn.classList.remove('text-brand-700', 'bg-white', 'shadow-sm', 'border-slate-200/60');
                btn.classList.add('text-slate-500', 'hover:text-slate-700', 'hover:bg-slate-200/50', 'border-transparent');
            }
        });
        
        const activePanel = document.getElementById('panel-' + tabId);
        const activeBtn = document.getElementById('tab-' + tabId);
        
        if (activePanel && activeBtn) {
            activePanel.classList.remove('hidden');
            activePanel.classList.add('block');
            
            activeBtn.classList.remove('text-slate-500', 'hover:text-slate-700', 'hover:bg-slate-200/50', 'border-transparent');
            activeBtn.classList.add('text-brand-700', 'bg-white', 'shadow-sm', 'border-slate-200/60');
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
