<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Payment - {{ config('app.name', 'Laravel') }}</title>
    <!-- SEO Meta Tags -->
    <meta name="description" content="Secure ticket payment gateway. Complete your RSVP by purchasing your ticket safely.">
    <meta name="keywords" content="events, ticket payment, secure checkout, rsvp">
    <meta property="og:title" content="Ticket Payment - {{ config('app.name', 'Laravel') }}">
    <meta property="og:description" content="Secure ticket payment gateway. Complete your RSVP by purchasing your ticket safely.">
    <meta property="og:type" content="website">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            500: '#8b5cf6', // Violet
                            600: '#7c3aed',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Abstract Background -->
    <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] rounded-full bg-blue-400/20 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] rounded-full bg-brand-500/20 blur-[120px] pointer-events-none"></div>

    <div class="bg-white/80 backdrop-blur-2xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white p-8 sm:p-10 w-full max-w-lg relative z-10">
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Complete Your Payment</h1>
            <p class="text-slate-500">Secure payment for your event ticket.</p>
        </div>

        @if (session('error'))
            <div class="mb-4 font-medium text-sm text-red-600 bg-red-50 p-3 rounded-lg text-center">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 text-sm text-red-600 bg-red-50 p-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-8">
            <div class="flex justify-between items-center mb-2">
                <span class="text-slate-500 font-medium">Ticket Type</span>
                <span class="text-slate-900 font-semibold">{{ $ticket->name }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-500 font-medium">Total Amount</span>
                <span class="text-2xl font-bold text-brand-600">₦{{ number_format($ticket->price, 2) }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('guest.payment.initiate') }}">
            @csrf

            <div class="space-y-5">
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl mb-4">
                    <label for="promo_code" class="block text-sm font-semibold text-slate-700 mb-2">Promo Code (Optional)</label>
                    <input id="promo_code" type="text" name="promo_code" value="{{ old('promo_code') }}" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-colors text-slate-900 bg-white uppercase" placeholder="Enter code">
                </div>

                <div>
                    <label for="card_number" class="block text-sm font-semibold text-slate-700 mb-2">Card Number</label>
                    <input id="card_number" type="text" name="card_number" value="{{ old('card_number') }}" required autofocus
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-colors text-slate-900 bg-slate-50/50" placeholder="0000 0000 0000 0000">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="expiry_month" class="block text-sm font-semibold text-slate-700 mb-2">Expiry Month</label>
                        <input id="expiry_month" type="text" name="expiry_month" value="{{ old('expiry_month') }}" required maxlength="2"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-colors text-slate-900 bg-slate-50/50" placeholder="MM">
                    </div>
                    <div>
                        <label for="expiry_year" class="block text-sm font-semibold text-slate-700 mb-2">Expiry Year</label>
                        <input id="expiry_year" type="text" name="expiry_year" value="{{ old('expiry_year') }}" required maxlength="2"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-colors text-slate-900 bg-slate-50/50" placeholder="YY">
                    </div>
                </div>

                <div>
                    <label for="cvv" class="block text-sm font-semibold text-slate-700 mb-2">CVV</label>
                    <input id="cvv" type="text" name="cvv" required maxlength="4"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-colors text-slate-900 bg-slate-50/50" placeholder="123">
                </div>
            </div>

            <button type="submit" class="w-full mt-8 bg-slate-900 text-white font-semibold py-3.5 px-4 rounded-xl hover:bg-slate-800 transition-colors shadow-lg shadow-slate-900/20 active:scale-[0.98] flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Pay Securely
            </button>
            
            <p class="text-center text-xs text-slate-400 mt-4 flex items-center justify-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Secured by RexPay
            </p>
        </form>

    </div>
</body>
</html>
