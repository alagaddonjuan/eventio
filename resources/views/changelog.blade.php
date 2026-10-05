<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Changelog - Eventio</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-900 text-slate-100 min-h-screen relative overflow-x-hidden selection:bg-indigo-500 selection:text-white">
        
        <!-- Background Elements for Premium Feel -->
        <div class="fixed top-[-10%] left-[-10%] w-96 h-96 bg-indigo-500/30 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="fixed bottom-[-10%] right-[-10%] w-96 h-96 bg-purple-500/30 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="fixed top-[40%] left-[60%] w-64 h-64 bg-fuchsia-500/20 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative z-10 max-w-4xl mx-auto px-6 py-20">
            <!-- Back to Home -->
            <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-slate-400 hover:text-white transition-colors mb-10 group">
                <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Home
            </a>

            <!-- Header -->
            <div class="text-center mb-16">
                <h1 class="text-5xl md:text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400 tracking-tight mb-4">Eventio Roadmap</h1>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto">The future of event management. See what we've shipped and what's coming next on our journey to build the ultimate event ecosystem.</p>
            </div>

            <!-- Glassmorphism Card (Roadmap) -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 md:p-12 shadow-2xl mb-12 relative overflow-hidden">
                <!-- Subtle inner glow -->
                <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
                
                <div class="relative z-10">
                    <h2 class="text-3xl font-bold text-white mb-8 flex items-center">
                        <span class="bg-indigo-500/20 text-indigo-300 p-2 rounded-xl mr-4 border border-indigo-500/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </span>
                        Upcoming Features
                    </h2>
                    
                    <div class="space-y-12">
                        <!-- Phase 4 (New) -->
                        <div class="group">
                            <h3 class="text-xl font-semibold text-amber-300 mb-4 flex items-center">
                                <span class="w-8 h-px bg-amber-500/50 mr-4"></span>
                                Phase 4: AI & Global Expansion
                            </h3>
                            <ul class="space-y-3 pl-12 text-slate-300">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    <span><strong class="text-white font-semibold">AI Event Assistant:</strong> Automated event planning and schedule generation.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                    <span><strong class="text-white font-semibold">Multi-currency & Localization:</strong> Host events worldwide with native language and currency support.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span><strong class="text-white font-semibold">Advanced Seating Charts:</strong> Drag-and-drop interactive 3D seat mapping for large venues.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Glassmorphism Card (History) -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 md:p-12 shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                        <!-- New v2.0.0 Release -->
                        <div class="mb-12">
                            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 pb-6 border-b border-white/10 gap-4">
                                <h2 class="text-2xl font-bold text-white flex items-center">
                                    <svg class="w-6 h-6 text-emerald-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    v2.0.0 - The Ecosystem Update
                                </h2>
                                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 text-sm font-medium rounded-full border border-emerald-500/30 whitespace-nowrap">
                                    Current Release
                                </span>
                            </div>
                            
                            <p class="text-sm text-slate-400 mb-6 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Released on {{ date('F j, Y') }}
                            </p>
                            
                            <ul class="space-y-4 pl-4 text-slate-300">
                                <li class="flex items-start">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 mr-3 shrink-0"></span>
                                    <span><strong class="text-white">Core Upgrades:</strong> Financial & Payout Controls, dynamic platform fees, and advanced User Management.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 mr-3 shrink-0"></span>
                                    <span><strong class="text-white">The Experience Layer:</strong> 3D digital tickets, waitlists, custom RSVP questions, and co-host management.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 mr-3 shrink-0"></span>
                                    <span><strong class="text-white">Digital Wallets & Calendars:</strong> Apple Wallet, Google Wallet, and automated calendar sync integration.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 mr-3 shrink-0"></span>
                                    <span><strong class="text-white">Premium UI:</strong> Micro-interactions, glassmorphism, animated skeleton loaders, and social "See Who's Going" features.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 mr-3 shrink-0"></span>
                                    <span><strong class="text-white">The Ecosystem:</strong> Live Marketplaces (Venues, Talent, Vendors), Promoter Affiliate Network, and Event Discovery feed.</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Older v1.0.0 Release -->
                        <div>
                            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 pb-6 border-b border-white/10 gap-4">
                                <h2 class="text-2xl font-bold text-white flex items-center">
                                    <svg class="w-6 h-6 text-slate-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    v1.0.0
                                </h2>
                                <span class="px-3 py-1 bg-slate-500/20 text-slate-300 text-sm font-medium rounded-full border border-slate-500/30 whitespace-nowrap">
                                    Past Release
                                </span>
                            </div>
                            
                            <ul class="space-y-4 pl-4 text-slate-300">
                                <li class="flex items-start">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mt-2 mr-3 shrink-0"></span>
                                    <span>Launched the core Eventio ticketing platform.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mt-2 mr-3 shrink-0"></span>
                                    <span>Master Admin dashboard for managing users and events.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mt-2 mr-3 shrink-0"></span>
                                    <span>Host dashboard for creating events, managing tickets, and RSVPs.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mt-2 mr-3 shrink-0"></span>
                                    <span>Guest portal for easy checkout and ticket management.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mt-2 mr-3 shrink-0"></span>
                                    <span>Live streaming integration and command center.</span>
                                </li>
                            </ul>
                        </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="mt-16 text-center text-sm text-slate-500">
                &copy; {{ date('Y') }} Eventio. All rights reserved.
            </footer>
        </div>
    </body>
</html>
