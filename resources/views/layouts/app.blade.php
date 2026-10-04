<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Standard SEO Meta Tags -->
        <meta name="description" content="Manage and track your events like a pro. Set up events, invite guests, and track RSVPs in real-time.">
        <meta name="keywords" content="events, event management, rsvp, real-time tracking, host">
        <meta name="author" content="{{ config('app.name', 'Laravel') }}">

        <!-- Open Graph (Facebook/LinkedIn) -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ config('app.name', 'Laravel') }}">
        <meta property="og:description" content="Manage and track your events like a pro. Set up events, invite guests, and track RSVPs in real-time.">
        <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="{{ config('app.name', 'Laravel') }}">
        <meta name="twitter:description" content="Manage and track your events like a pro. Set up events, invite guests, and track RSVPs in real-time.">
        <meta name="twitter:image" content="{{ asset('images/og-image.jpg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-950 text-slate-100 selection:bg-indigo-500 selection:text-white">
        
        <!-- Background Elements for Premium Feel -->
        <div class="fixed top-[-10%] left-[-10%] w-96 h-96 bg-indigo-500/20 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="fixed bottom-[-10%] right-[-10%] w-96 h-96 bg-purple-500/20 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="min-h-screen flex flex-col relative z-10">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/5 backdrop-blur-xl border-b border-white/10 shadow-lg">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-grow pb-12">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white/5 backdrop-blur-xl border-t border-white/10 mt-auto py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center text-sm text-slate-400">
                    <div class="mb-4 md:mb-0 text-center md:text-left">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Eventio') }}. Powered by <a href="https://q4iltd.com" target="_blank" class="font-bold text-indigo-400 hover:text-indigo-300 transition-colors">Q4I</a>
                    </div>
                    <div class="flex flex-wrap items-center justify-center space-x-6">
                        <a href="{{ route('changelog') }}" class="hover:text-white transition-colors flex items-center">
                            <span class="w-2 h-2 rounded-full bg-indigo-500 mr-2"></span>
                            Changelog
                        </a>
                        <a href="{{ route('terms') }}" class="hover:text-white transition-colors">Terms & Conditions</a>
                        <a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a>
                    </div>
                </div>
            </footer>
        </div>
        @stack('scripts')
    </body>
</html>
