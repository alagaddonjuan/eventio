<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} - Live Guest Tracking</title>
    <!-- SEO Meta Tags -->
    <meta name="description" content="Manage and track your events like a pro. Set up events, invite guests, and track RSVPs in real-time.">
    <meta name="keywords" content="events, event management, rsvp, real-time tracking, host">
    <meta property="og:title" content="{{ config('app.name', 'Laravel') }} - Live Guest Tracking">
    <meta property="og:description" content="Manage and track your events like a pro. Set up events, invite guests, and track RSVPs in real-time.">
    <meta property="og:type" content="website">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-200 selection:text-brand-900">
    
    <!-- Navigation -->
    <nav class="bg-white/70 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-2xl font-extrabold tracking-tight text-slate-900">Event<span class="text-brand-600">io</span></span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-brand-600 font-semibold transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-slate-600 hover:text-brand-600 font-semibold transition">Log in</a>
                        <a href="{{ route('register') }}" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <!-- Hero Text -->
            <div>
                <span class="inline-block px-4 py-1.5 bg-brand-100 text-brand-700 text-sm font-semibold rounded-full mb-6 shadow-sm border border-brand-200">Launch Your Event Tracking</span>
                <h1 class="text-5xl font-extrabold text-slate-900 leading-tight mb-6">
                    Never ask <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-indigo-600">"Where are you?"</span> again.
                </h1>
                <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                    {{ config('app.name', 'Laravel') }} gives you a Live Command Center to track your guests' arrivals in real-time, providing accurate ETAs right to your dashboard. Perfect for weddings, VIP events, and exclusive gatherings.
                </p>
                <div class="flex flex-wrap gap-4 text-sm font-medium text-slate-600 mb-10">
                    <div class="flex items-center bg-white px-3 py-1.5 rounded-lg shadow-sm border border-slate-100"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Live Map Tracking</div>
                    <div class="flex items-center bg-white px-3 py-1.5 rounded-lg shadow-sm border border-slate-100"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Auto Check-in</div>
                    <div class="flex items-center bg-white px-3 py-1.5 rounded-lg shadow-sm border border-slate-100"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Battery Optimized</div>
                </div>
            </div>

            <!-- Create Event Card -->
            <div class="bg-white p-8 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-brand-400 to-indigo-500"></div>
                
                <h3 class="text-2xl font-bold text-slate-900 mb-6 mt-2">Create New Event</h3>
                
                @auth
                <form action="{{ route('events.store') }}" method="POST" class="space-y-5" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Event Title</label>
                        <input type="text" name="title" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition" placeholder="e.g. Sarah's Wedding" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Venue Name</label>
                        <input type="text" name="venue_name" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition" placeholder="e.g. The Grand Hotel" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Latitude</label>
                            <input type="number" step="any" name="latitude" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition" placeholder="40.7128" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Longitude</label>
                            <input type="number" step="any" name="longitude" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition" placeholder="-74.0060" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Date & Time</label>
                            <input type="datetime-local" name="event_date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Theme Color</label>
                            <div class="flex items-center space-x-3">
                                <input type="color" name="theme_color" value="#0ea5e9" class="w-12 h-12 rounded-lg cursor-pointer border-0 p-0">
                                <span class="text-sm text-slate-500">Pick your brand color</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Event Banner Image (Optional)</label>
                        <input type="file" name="banner_image" accept="image/*" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition">
                        <p class="text-xs text-slate-500 mt-1">Recommended size: 1200x400px (Max 5MB). Used on RSVP page.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Manual Directions (Optional)</label>
                        <textarea name="manual_directions" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition" placeholder="e.g. If using public transport, take bus 42. If driving, park at the rear entrance."></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-brand-500/30 transition-transform hover:-translate-y-0.5 active:translate-y-0 mt-4">
                        Create Event & Get Tracking Link
                    </button>
                </form>
                @else
                <div class="text-center py-10">
                    <p class="text-slate-600 mb-6 font-medium">You need an account to create and manage events.</p>
                    <a href="{{ route('register') }}" class="inline-block bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-brand-500/30 transition-transform hover:-translate-y-0.5 active:translate-y-0">
                        Create Free Account
                    </a>
                    <p class="mt-4 text-sm text-slate-500">Already have an account? <a href="{{ route('login') }}" class="text-brand-600 hover:underline">Log in</a></p>
                </div>
                @endauth
            </div>
        </div>
    </div>
    <footer class="mt-16 py-8 border-t border-slate-200 text-center text-sm text-slate-500">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                &copy; {{ date('Y') }} Eventio. Powered by <a href="https://q4iltd.com" target="_blank" class="font-bold text-brand-600 hover:underline">Q4I</a>
            </div>
            <div class="flex items-center justify-center space-x-4">
                <a href="{{ route('terms') }}" class="hover:text-brand-600 transition">Terms & Conditions</a>
                <span class="text-slate-300">|</span>
                <a href="{{ route('privacy') }}" class="hover:text-brand-600 transition">Privacy Policy</a>
            </div>
        </div>
    </footer>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const venueInput = document.querySelector('input[name="venue_name"]');
            const latInput = document.querySelector('input[name="latitude"]');
            const lngInput = document.querySelector('input[name="longitude"]');
            
            if(venueInput && latInput && lngInput && typeof google !== 'undefined') {
                const autocomplete = new google.maps.places.Autocomplete(venueInput);
                
                // Prevent form submission when pressing enter on autocomplete
                venueInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                    }
                });

                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    
                    if (!place.geometry) {
                        return;
                    }
                    
                    latInput.value = place.geometry.location.lat();
                    lngInput.value = place.geometry.location.lng();
                });
            }
        });
    </script>
</body>
</html>
