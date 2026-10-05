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
                        <a href="{{ route('discovery.index') }}" class="text-slate-600 hover:text-brand-600 font-semibold transition">Explore Events</a>
                        <a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-brand-600 font-semibold transition">Dashboard</a>
                    @else
                        <a href="{{ route('discovery.index') }}" class="text-slate-600 hover:text-brand-600 font-semibold transition">Explore Events</a>
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

    <!-- How It Works Section -->
    <div class="bg-white py-20 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">How It Works</h2>
                <p class="mt-4 max-w-2xl text-lg text-slate-600 mx-auto">From creating your event to managing guests at the door, Eventio simplifies everything.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center relative">
                    <div class="w-16 h-16 bg-brand-100 text-brand-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm border border-brand-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">1. Create Event</h3>
                    <p class="text-slate-600 text-sm">Set up your event details, location, and ticket options (free or paid) in seconds.</p>
                </div>
                
                <!-- Step 2 -->
                <div class="text-center relative">
                    <div class="w-16 h-16 bg-brand-100 text-brand-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm border border-brand-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">2. Share Link</h3>
                    <p class="text-slate-600 text-sm">Send your unique RSVP link. Guests book tickets and get access to a beautiful digital portal.</p>
                </div>
                
                <!-- Step 3 -->
                <div class="text-center relative">
                    <div class="w-16 h-16 bg-brand-100 text-brand-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm border border-brand-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">3. Track Arrivals</h3>
                    <p class="text-slate-600 text-sm">On event day, guests click "Start Journey". You track their ETA live on a map.</p>
                </div>
                
                <!-- Step 4 -->
                <div class="text-center relative">
                    <div class="w-16 h-16 bg-brand-100 text-brand-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm border border-brand-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">4. Scan & Check-in</h3>
                    <p class="text-slate-600 text-sm">Guests present their QR ticket at the door. Scan with any phone to mark them as arrived!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-slate-50 py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">Everything you need for a flawless event</h2>
                <p class="mt-4 max-w-2xl text-lg text-slate-600 mx-auto">Eventio provides a suite of premium tools to give your guests a VIP experience while making your life easier.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Live Map Tracking</h4>
                    <p class="text-slate-600 text-sm">See exactly where your guests are on a real-time map with accurate ETAs on event day.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">QR Code Ticketing</h4>
                    <p class="text-slate-600 text-sm">Every guest gets a unique QR ticket. Scan it at the door using any smartphone camera to check them in.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Seamless Payments</h4>
                    <p class="text-slate-600 text-sm">Sell tickets effortlessly. Integrated with RexPay for secure, instant payments and checkout.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Custom Guest Portals</h4>
                    <p class="text-slate-600 text-sm">Each guest gets a beautifully branded live portal matching your event's theme colors.</p>
                </div>
                
                <!-- Feature 5 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Live Photo Gallery</h4>
                    <p class="text-slate-600 text-sm">Guests can upload photos straight from their portal during the event for a shared digital album.</p>
                </div>
                
                <!-- Feature 6 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Live Streaming</h4>
                    <p class="text-slate-600 text-sm">Stream your event live via Mux integration so virtual attendees never miss a moment.</p>
                </div>
                
                <!-- Feature 7 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-pink-50 text-pink-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Waitlists & Custom RSVPs</h4>
                    <p class="text-slate-600 text-sm">Manage demand effortlessly with automated waitlists and custom registration questions.</p>
                </div>
                
                <!-- Feature 8 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Digital Wallets & Calendars</h4>
                    <p class="text-slate-600 text-sm">Let guests save 3D holographic tickets to Apple/Google Wallet and sync to their calendar.</p>
                </div>
                
                <!-- Feature 9 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Integrated Marketplaces</h4>
                    <p class="text-slate-600 text-sm">Discover and book venues, vendors, and talent directly from the ecosystem.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Events Section -->
    @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
    <div class="bg-slate-100 py-16 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-6">
            <h3 class="text-2xl font-extrabold text-slate-900 mb-8 text-center">Discover Upcoming Events</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($upcomingEvents as $event)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition group flex flex-col">
                    <div class="h-48 bg-slate-200 relative">
                        @if($event->banner_image)
                        <img src="{{ Storage::url($event->banner_image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center bg-brand-50 text-brand-300">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        @endif
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-slate-800 shadow-sm">
                            {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <h4 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-brand-600 transition">{{ $event->title }}</h4>
                        <p class="text-slate-500 text-sm mb-4 line-clamp-2 flex-1">{{ $event->description ?? 'Join us for this amazing event at ' . $event->venue_name }}</p>
                        
                        @php
                            $totalCapacity = $event->tickets ? $event->tickets->sum('capacity') : 0;
                            $soldTickets = $event->guests ? $event->guests->count() : 0;
                            $availableTickets = max(0, $totalCapacity - $soldTickets);
                        @endphp
                        
                        <div class="flex items-center text-xs text-slate-500 font-medium mb-4 bg-slate-50 p-2 rounded-lg border border-slate-100">
                            <svg class="w-4 h-4 mr-1.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                            @if($totalCapacity > 0)
                                {{ $availableTickets }} / {{ $totalCapacity }} tickets left
                            @else
                                Open Registration
                            @endif
                        </div>
                        
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            <div class="text-sm font-semibold text-slate-700">
                                @if($event->tickets && $event->tickets->count() > 0)
                                    From ₦{{ number_format($event->tickets->min('price'), 2) }}
                                @else
                                    Free Entry
                                @endif
                            </div>
                            <a href="{{ route('guest.rsvp', $event->tracking_access_token) }}" class="bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold py-2 px-4 rounded-lg transition text-sm">Get Tickets</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <footer class="mt-16 py-8 border-t border-slate-200 text-center text-sm text-slate-500">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                &copy; {{ date('Y') }} Eventio. Powered by <a href="https://q4iltd.com" target="_blank" class="font-bold text-brand-600 hover:underline">Q4I</a>
            </div>
            <div class="flex items-center justify-center space-x-4">
                <a href="{{ route('changelog') }}" class="hover:text-brand-600 transition">Changelog</a>
                <span class="text-slate-300">|</span>
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
