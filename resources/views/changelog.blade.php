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
                        <!-- Phase 1 -->
                        <div class="group">
                            <h3 class="text-xl font-semibold text-indigo-300 mb-4 flex items-center">
                                <span class="w-8 h-px bg-indigo-500/50 mr-4"></span>
                                Phase 1: Core Upgrades
                            </h3>
                            <ul class="space-y-3 pl-12 text-slate-300">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-indigo-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span><strong class="text-white font-semibold">Financial & Payout Controls:</strong> Withdrawal request system and dynamic platform fees.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-indigo-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span><strong class="text-white font-semibold">User Management:</strong> User suspension and admin impersonation for support.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-indigo-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span><strong class="text-white font-semibold">Advanced Event Controls:</strong> Event approval workflows and featured events section.</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Phase 2 -->
                        <div class="group">
                            <h3 class="text-xl font-semibold text-purple-300 mb-4 flex items-center">
                                <span class="w-8 h-px bg-purple-500/50 mr-4"></span>
                                Phase 2: The Experience Layer
                            </h3>
                            <ul class="space-y-3 pl-12 text-slate-300">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-purple-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    <span><strong class="text-white font-semibold">Rich Analytics:</strong> Beautiful charts for ticket sales and conversions.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-purple-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                    <span><strong class="text-white font-semibold">Custom Themes:</strong> Let hosts personalize event landing pages (Dark Mode, custom colors).</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-purple-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span><strong class="text-white font-semibold">Waitlists & Co-Hosts:</strong> Automated waitlist management and multi-user event access.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-purple-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    <span><strong class="text-white font-semibold">Digital Wallets:</strong> Apple Wallet & Google Wallet ticket integration.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-purple-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                    <span><strong class="text-white font-semibold">Premium UI:</strong> Micro-interactions, glassmorphism, and skeleton loaders.</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Phase 3 -->
                        <div class="group">
                            <h3 class="text-xl font-semibold text-fuchsia-300 mb-4 flex items-center">
                                <span class="w-8 h-px bg-fuchsia-500/50 mr-4"></span>
                                Phase 3: The Ecosystem
                            </h3>
                            <ul class="space-y-3 pl-12 text-slate-300">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-fuchsia-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    <span><strong class="text-white font-semibold">Marketplaces:</strong> Discover and book venues, talent, and vendors directly.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-fuchsia-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span><strong class="text-white font-semibold">Promoter Network:</strong> Affiliate system with automated sales commissions.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-fuchsia-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                    <span><strong class="text-white font-semibold">Event Discovery:</strong> Location-based event feed for users.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-fuchsia-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span><strong class="text-white font-semibold">Post-Event Echo:</strong> Shared media gallery for photos and videos after the event.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Glassmorphism Card (History) -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 md:p-12 shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 pb-6 border-b border-white/10 gap-4">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 text-slate-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                            v1.0.0
                        </h2>
                        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 text-sm font-medium rounded-full border border-indigo-500/30 whitespace-nowrap">
                            Current Release
                        </span>
                    </div>
                    
                    <p class="text-sm text-slate-400 mb-6 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Released on {{ date('F j, Y') }}
                    </p>
                    
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

            <!-- Footer -->
            <footer class="mt-16 text-center text-sm text-slate-500">
                &copy; {{ date('Y') }} Eventio. All rights reserved.
            </footer>
        </div>
    </body>
</html>
