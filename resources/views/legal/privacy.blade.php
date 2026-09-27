<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Eventio</title>
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
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-200 selection:text-brand-900 flex flex-col min-h-screen">
    
    <!-- Navigation -->
    <nav class="bg-white/70 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-extrabold tracking-tight text-slate-900">Event<span class="text-brand-600">io</span></a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-slate-600 hover:text-brand-600 font-semibold transition">Back to Home</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-4xl mx-auto px-6 py-16 w-full">
        <h1 class="text-4xl font-extrabold text-slate-900 mb-4">Privacy Policy</h1>
        <p class="text-slate-500 mb-8 font-medium">Last updated: {{ date('F j, Y') }}</p>
        
        <div class="prose prose-slate max-w-none bg-white p-10 rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.05)] border border-slate-100">
            <p class="lead text-lg text-slate-600 font-medium mb-8">At Eventio, protecting your privacy and security is our highest priority. This Privacy Policy explains how we collect, use, and safeguard your personal information and location data.</p>

            <h2 class="text-2xl font-bold text-slate-800 mt-8 mb-4 border-b border-slate-100 pb-2">1. Information We Collect</h2>
            <p class="mb-4">We collect different types of information depending on how you use our platform:</p>
            
            <h3 class="text-xl font-bold text-slate-700 mt-6 mb-3">For Event Hosts</h3>
            <ul class="list-disc pl-6 mb-4 space-y-2 text-slate-600">
                <li><strong>Account Information:</strong> Name, email address, and encrypted password.</li>
                <li><strong>Event Data:</strong> Event names, dates, physical venue addresses, and manually entered directions.</li>
                <li><strong>Authentication Data:</strong> If you sign up using Google, we collect your Google email and basic profile information.</li>
            </ul>

            <h3 class="text-xl font-bold text-slate-700 mt-6 mb-3">For Guests</h3>
            <ul class="list-disc pl-6 mb-4 space-y-2 text-slate-600">
                <li><strong>RSVP Information:</strong> Name and email address required to issue a ticket and send event updates.</li>
                <li><strong>Live Location Data:</strong> Precise GPS coordinates (latitude and longitude) collected <strong>only</strong> when you actively grant permission and have the tracking portal open on the day of the event.</li>
            </ul>

            <h2 class="text-2xl font-bold text-slate-800 mt-8 mb-4 border-b border-slate-100 pb-2">2. How We Handle Location Data</h2>
            <p class="mb-4">Because our service involves live tracking, we adhere to strict data minimization principles:</p>
            <ul class="list-disc pl-6 mb-4 space-y-2 text-slate-600">
                <li><strong>Opt-In Only:</strong> We will never track your location without your explicit browser/device permission.</li>
                <li><strong>Ephemeral Streaming:</strong> Your location is streamed via a secure WebSocket connection directly to the Host's Command Center.</li>
                <li><strong>No Historical Storage:</strong> We <strong>do not</strong> save your location history to our database. Once you close the portal or arrive at the event, tracking stops completely, and previous coordinates are permanently discarded.</li>
            </ul>

            <h2 class="text-2xl font-bold text-slate-800 mt-8 mb-4 border-b border-slate-100 pb-2">3. How We Use Your Information</h2>
            <p class="mb-4">We use the collected information for the following purposes:</p>
            <ul class="list-disc pl-6 mb-4 space-y-2 text-slate-600">
                <li>To provide, maintain, and improve the Eventio platform.</li>
                <li>To process RSVP requests and generate unique ticket barcodes.</li>
                <li>To calculate Estimated Time of Arrival (ETA) for Hosts (using third-party routing APIs).</li>
                <li>To send automated emails regarding event updates, changes in venue, or cancellations.</li>
            </ul>

            <h2 class="text-2xl font-bold text-slate-800 mt-8 mb-4 border-b border-slate-100 pb-2">4. Third-Party Services</h2>
            <p class="mb-4">We rely on certain third-party services to operate:</p>
            <ul class="list-disc pl-6 mb-4 space-y-2 text-slate-600">
                <li><strong>Google Maps API:</strong> Used to render maps and calculate distances/ETAs based on your location.</li>
                <li><strong>Payment Processors:</strong> If purchasing a paid ticket, your payment details are handled securely by third-party processors (e.g., Stripe, Paystack). Eventio does not store full credit card numbers.</li>
            </ul>

            <h2 class="text-2xl font-bold text-slate-800 mt-8 mb-4 border-b border-slate-100 pb-2">5. Data Retention and Deletion</h2>
            <p class="mb-4">You have the right to request the deletion of your personal data at any time. Hosts can delete their events and accounts directly from the dashboard. Guests can contact us to have their RSVP data removed.</p>

            <h2 class="text-2xl font-bold text-slate-800 mt-8 mb-4 border-b border-slate-100 pb-2">6. Contact Us</h2>
            <p class="mb-4">Eventio is powered and managed by <strong>Q4I</strong>. If you have concerns about your privacy or this policy, please contact our Data Protection Officer:</p>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-slate-600">
                <strong>Q4I Ltd.</strong><br>
                Email: privacy@q4iltd.com
            </div>
        </div>
    </main>

    <footer class="mt-8 py-8 border-t border-slate-200 text-center text-sm text-slate-500 bg-white">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                &copy; {{ date('Y') }} Eventio. Powered by <a href="https://q4iltd.com" target="_blank" class="font-bold text-brand-600 hover:underline">Q4I</a>
            </div>
            <div class="flex items-center justify-center space-x-4">
                <a href="{{ route('terms') }}" class="hover:text-brand-600 transition">Terms & Conditions</a>
                <span class="text-slate-300">|</span>
                <a href="{{ route('privacy') }}" class="text-brand-600 font-bold hover:underline transition">Privacy Policy</a>
            </div>
        </div>
    </footer>
</body>
</html>
