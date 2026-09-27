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
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-grow">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 mt-auto py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                    <div class="mb-4 md:mb-0 text-center md:text-left">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Eventio') }}. Powered by <a href="https://q4iltd.com" target="_blank" class="font-bold text-indigo-600 hover:underline">Q4I</a>
                    </div>
                    <div class="flex items-center justify-center space-x-4">
                        <a href="{{ route('terms') }}" class="hover:text-indigo-600 transition">Terms & Conditions</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('privacy') }}" class="hover:text-indigo-600 transition">Privacy Policy</a>
                    </div>
                </div>
            </footer>
        </div>
        @stack('scripts')
    </body>
</html>
