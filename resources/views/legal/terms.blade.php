<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - Eventio</title>
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
        <h1 class="text-4xl font-extrabold text-slate-900 mb-4">Terms and Conditions</h1>
        <p class="text-slate-500 mb-8 font-medium">Last updated: {{ date('F j, Y') }}</p>
        
        <div class="prose prose-slate max-w-none bg-white p-10 rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.05)] border border-slate-100">
            <p class="lead text-lg text-slate-600 font-medium mb-8">Welcome to Eventio. By accessing and using our platform, you agree to be bound by these Terms and Conditions. Please read them carefully.</p>

            <h2 class="text-2xl font-bold text-slate-800 mt-8 mb-4 border-b border-slate-100 pb-2">1. The Platform</h2>
            <p class="mb-4">Eventio ("we", "us", "our") provides an event management and live guest tracking service. We allow Event Hosts ("Hosts") to create events, issue tickets (free or paid), and track the estimated arrival times of their Guests ("Guests") in real-time on the day of the event.</p>

            <h2 class="text-2xl font-bold text-slate-800 mt-8 mb-4 border-b border-slate-100 pb-2">2. Accounts and Responsibilities</h2>
            <p class="mb-4"><strong>Hosts:</strong> You are responsible for maintaining the confidentiality of your account credentials. You agree that all information provided regarding your event, including venue details, directions, and ticket prices, is accurate and lawful.</p>
            <p class="mb-4"><strong>Guests:</strong> You do not need an account to RSVP or use the tracking portal. However, you agree to provide accurate information (Name and Email) when securing a ticket. You agree to use the live tracking feature responsibly and solely for the purpose of communicating your arrival status to the Host.</p>

            <h2 class="text-2xl font-bold text-slate-800 mt-8 mb-4 border-b border-slate-100 pb-2">3. Ticketing and Payments</h2>
            <p class="mb-4">Eventio allows Hosts to issue digital tickets with unique Barcodes/QR Codes.</p>
            <ul class="list-disc pl-6 mb-4 space-y-2 text-slate-600">
                <li><strong>Free Tickets:</strong> Issued at no cost to the Guest.</li>
                <li><strong>Paid Tickets:</strong> When Premium features are enabled, Hosts may charge for tickets. Eventio acts as an intermediary technology platform. The Host is the ultimate seller of the ticket.</li>
                <li><strong>Platform Fees:</strong> Eventio may deduct a platform fee or payment processing fee from paid ticket transactions before funds are remitted to the Host.</li>
                <li><strong>Refunds:</strong> All refund requests must be directed to the Event Host. Eventio is not responsible for issuing refunds for canceled or rescheduled events, except where explicitly required by law.</li>
            </ul>

            <h2 class="text-2xl font-bold text-slate-800 mt-8 mb-4 border-b border-slate-100 pb-2">4. Event Cancellation and Changes</h2>
            <p class="mb-4">Hosts have the ability to edit event details (Date, Time, Venue). If crucial details change, Eventio will automatically attempt to notify registered Guests via email. However, Eventio is not liable for Guests failing to receive or read these notifications.</p>

            <h2 class="text-2xl font-bold text-slate-800 mt-8 mb-4 border-b border-slate-100 pb-2">5. Privacy and Location Data</h2>
            <p class="mb-4">The core feature of Eventio involves transmitting live GPS coordinates from the Guest's device to the Host's Command Center.</p>
            <ul class="list-disc pl-6 mb-4 space-y-2 text-slate-600">
                <li>Location tracking is <strong>strictly opt-in</strong> and only occurs when the Guest has the web portal open and active on the day of the event.</li>
                <li>Location data is streamed directly to the Host and is <strong>not permanently stored</strong> in our databases.</li>
            </ul>
            <p class="mb-4">For comprehensive details on how we handle your data, please review our <a href="{{ route('privacy') }}" class="text-brand-600 font-bold hover:underline">Privacy Policy</a>.</p>

            <h2 class="text-2xl font-bold text-slate-800 mt-8 mb-4 border-b border-slate-100 pb-2">6. Limitation of Liability</h2>
            <p class="mb-4">Eventio provides the platform "as is". We are not responsible for the actual execution, safety, or quality of the events created by Hosts. We are not liable for any delays, injuries, or losses incurred while Guests are traveling to an event, regardless of ETA estimates provided by our platform.</p>

            <h2 class="text-2xl font-bold text-slate-800 mt-8 mb-4 border-b border-slate-100 pb-2">7. Contact Information</h2>
            <p class="mb-4">Eventio is powered and managed by <strong>Q4I</strong>.</p>
            <p class="mb-4">If you have any questions about these Terms, please contact us at:</p>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-slate-600">
                <strong>Q4I Ltd.</strong><br>
                Website: <a href="https://q4iltd.com" target="_blank" class="text-brand-600 hover:underline">q4iltd.com</a><br>
                Email: legal@q4iltd.com
            </div>
        </div>
    </main>

    <footer class="mt-8 py-8 border-t border-slate-200 text-center text-sm text-slate-500 bg-white">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                &copy; {{ date('Y') }} Eventio. Powered by <a href="https://q4iltd.com" target="_blank" class="font-bold text-brand-600 hover:underline">Q4I</a>
            </div>
            <div class="flex items-center justify-center space-x-4">
                <a href="{{ route('changelog') }}" class="hover:text-brand-600 transition">Changelog</a>
                <span class="text-slate-300">|</span>
                <a href="{{ route('terms') }}" class="text-brand-600 font-bold hover:underline transition">Terms & Conditions</a>
                <span class="text-slate-300">|</span>
                <a href="{{ route('privacy') }}" class="hover:text-brand-600 transition">Privacy Policy</a>
            </div>
        </div>
    </footer>
</body>
</html>
