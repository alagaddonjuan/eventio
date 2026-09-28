<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter OTP - {{ config('app.name', 'Laravel') }}</title>
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

    <div class="bg-white/80 backdrop-blur-2xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white p-8 sm:p-10 w-full max-w-sm relative z-10">
        
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-brand-100 rounded-full flex items-center justify-center mx-auto mb-4 text-brand-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 mb-2">Verification</h1>
            <p class="text-slate-500 text-sm">Please enter the One-Time Password (OTP) sent to your phone or email.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg text-center">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 text-sm text-red-600 bg-red-50 p-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('guest.payment.authorize') }}">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="otp" class="block text-sm font-semibold text-slate-700 mb-2 text-center">Enter OTP</label>
                    <input id="otp" type="text" name="otp" required autofocus autocomplete="one-time-code"
                        class="w-full px-4 py-3 text-center tracking-widest text-2xl font-bold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-colors text-slate-900 bg-slate-50/50" placeholder="••••••">
                </div>
            </div>

            <button type="submit" class="w-full mt-8 bg-brand-600 text-white font-semibold py-3.5 px-4 rounded-xl hover:bg-brand-700 transition-colors shadow-lg shadow-brand-600/30 active:scale-[0.98]">
                Verify & Pay
            </button>
            
        </form>

    </div>
</body>
</html>
