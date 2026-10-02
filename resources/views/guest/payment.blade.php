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

        <form id="paymentForm" method="POST" action="{{ route('guest.payment.initiate') }}" target="_blank">
            @csrf
            <input type="hidden" name="payment_reference" id="payment_reference" value="{{ $paymentReference ?? '' }}">

            <div class="space-y-5">
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl mb-4">
                    <label for="promo_code" class="block text-sm font-semibold text-slate-700 mb-2">Promo Code (Optional)</label>
                    <input id="promo_code" type="text" name="promo_code" value="{{ old('promo_code') }}" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-colors text-slate-900 bg-white uppercase" placeholder="Enter code">
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

    <!-- Polling Overlay -->
    <div id="pollingOverlay" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 hidden flex-col items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full text-center shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-brand-500 overflow-hidden">
                <div class="w-1/2 h-full bg-brand-400 animate-[bounce_2s_infinite]"></div>
            </div>
            
            <div class="w-20 h-20 mx-auto mb-6 bg-brand-50 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-brand-500 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Awaiting Payment</h2>
            <p class="text-slate-500 mb-6">A new tab has opened for you to complete your payment. Please do not close this window.</p>
            
            <div class="bg-amber-50 text-amber-800 text-sm p-4 rounded-xl text-left border border-amber-200">
                <p class="font-semibold mb-1 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Important:
                </p>
                <p>Once you complete the payment in the other tab, this page will automatically redirect you to your ticket.</p>
            </div>
            
            <button type="button" id="cancelPollingBtn" class="mt-6 text-sm text-slate-500 hover:text-slate-700 underline underline-offset-4">Cancel and return</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('paymentForm');
            const overlay = document.getElementById('pollingOverlay');
            const cancelBtn = document.getElementById('cancelPollingBtn');
            let pollInterval = null;
            
            // Give the backend a couple of seconds to redirect the new tab to RexPay,
            // otherwise our first poll might not find the reference in the session yet.
            form.addEventListener('submit', function(e) {
                // Show overlay
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
                
                // Start polling after a short delay
                setTimeout(startPolling, 3000);
            });
            
            cancelBtn.addEventListener('click', function() {
                stopPolling();
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            });
            
            function startPolling() {
                if (pollInterval) clearInterval(pollInterval);
                
                pollInterval = setInterval(function() {
                    let ref = document.getElementById('payment_reference').value;
                    let url = '{{ route('guest.payment.status') }}';
                    if (ref) {
                        url += '?ref=' + encodeURIComponent(ref);
                    }
                    
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'successful' && data.redirect) {
                                stopPolling();
                                window.location.href = data.redirect;
                            }
                            if (data.debug) {
                                let debugDiv = document.getElementById('debug-info');
                                if (!debugDiv) {
                                    debugDiv = document.createElement('pre');
                                    debugDiv.id = 'debug-info';
                                    debugDiv.style.marginTop = '20px';
                                    debugDiv.style.fontSize = '10px';
                                    debugDiv.style.whiteSpace = 'pre-wrap';
                                    debugDiv.style.wordBreak = 'break-all';
                                    let warningBox = document.querySelector('.bg-amber-50');
                                    if (warningBox) warningBox.after(debugDiv);
                                }
                                debugDiv.textContent = 'Debug: ' + (typeof data.debug === 'object' ? JSON.stringify(data.debug) : data.debug);
                            }
                        })
                        .catch(err => console.error('Polling error:', err));
                }, 3000); // Check every 3 seconds
            }
            
            function stopPolling() {
                if (pollInterval) {
                    clearInterval(pollInterval);
                    pollInterval = null;
                }
            }
        });
    </script>
</body>
</html>
