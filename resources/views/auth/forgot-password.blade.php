<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - {{ config('app.name', 'Laravel') }}</title>
    
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
<body class="bg-slate-50 font-sans min-h-screen flex items-center justify-center py-10 px-4 relative">
    <!-- Abstract Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] rounded-full bg-blue-400/20 blur-[120px]"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] rounded-full bg-brand-500/20 blur-[120px]"></div>
    </div>

    <div class="bg-white/80 backdrop-blur-2xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white p-7 sm:p-9 w-full max-w-md relative z-10 my-auto">
        
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-brand-500/10 text-brand-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-1.5">Forgot Password?</h1>
            <p class="text-slate-500 text-sm">No problem. Enter your email address and we will email you a password reset link.</p>
        </div>

        @if (session('status'))
            <div class="mb-5 p-3.5 rounded-xl bg-green-50 text-green-700 text-sm font-medium border border-green-200/60 text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2.5 sm:py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-colors text-slate-900 bg-slate-50/50" placeholder="you@example.com">
                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-red-500 text-sm" />
            </div>

            <button type="submit" class="w-full mt-6 bg-slate-900 text-white font-semibold py-3 px-4 rounded-xl hover:bg-slate-800 transition-colors shadow-lg shadow-slate-900/20 active:scale-[0.98]">
                Email Password Reset Link
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Remember your password? 
            <a href="{{ route('login') }}" class="text-brand-600 font-semibold hover:text-brand-500">Back to sign in</a>
        </p>

    </div>
</body>
</html>
