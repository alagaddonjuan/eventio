<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ticket Verification - {{ $guest->event->title ?? 'Event' }}</title>
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
                        theme: '{{ $guest->event->theme_color ?? "#0ea5e9" }}'
                    }
                }
            }
        }
    </script>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col font-sans" style="background-color: {{ $guest->event->theme_color ?? '#0ea5e9' }}15;">
    
    <main class="relative z-10 flex flex-col min-h-screen justify-center py-8 px-4">
        <div class="bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.1)] p-8 w-full max-w-md mx-auto relative overflow-hidden border border-white text-center">
            <div class="absolute top-0 left-0 w-full h-2.5" style="background-color: {{ $guest->event->theme_color ?? '#0ea5e9' }}"></div>
            
            <div class="mb-6">
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 mb-2">Ticket Verification</h1>
                <p class="text-slate-500 font-medium text-sm">{{ $guest->event->title ?? 'Event Name' }}</p>
            </div>

            <div class="bg-slate-50/80 rounded-2xl p-6 mb-6 border border-slate-100 text-left">
                <div class="mb-4">
                    <p class="text-xs text-slate-400 uppercase tracking-widest font-bold mb-1">Guest Name</p>
                    <p class="text-xl font-bold text-slate-900">{{ $guest->name }}</p>
                </div>
                
                <div class="mb-4">
                    <p class="text-xs text-slate-400 uppercase tracking-widest font-bold mb-1">Email</p>
                    <p class="text-base font-medium text-slate-700">{{ $guest->email }}</p>
                </div>

                @if($guest->ticket)
                <div class="mb-4 pt-4 border-t border-slate-200">
                    <p class="text-xs text-slate-400 uppercase tracking-widest font-bold mb-1">Ticket Details</p>
                    <p class="text-lg font-bold text-slate-900">{{ $guest->ticket->name }} <span class="text-sm font-medium text-slate-500 uppercase ml-2">({{ $guest->ticket->type }})</span></p>
                </div>
                @endif
                
                <div class="pt-4 border-t border-slate-200">
                    <p class="text-xs text-slate-400 uppercase tracking-widest font-bold mb-1">Status</p>
                    @if($guest->check_in_status === 'arrived')
                        <div class="inline-flex items-center justify-center bg-green-50 px-3 py-1.5 rounded-full border border-green-100 mt-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 mr-2 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                            <span class="text-sm font-bold text-green-700">Already Checked In</span>
                        </div>
                    @else
                        <div class="inline-flex items-center justify-center bg-amber-50 px-3 py-1.5 rounded-full border border-amber-100 mt-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-2 shadow-[0_0_8px_rgba(245,158,11,0.6)]"></span>
                            <span class="text-sm font-bold text-amber-700">Pending Arrival</span>
                        </div>
                    @endif
                </div>
            </div>

            @if($guest->check_in_status !== 'arrived')
            <button id="check-in-btn" class="w-full text-white font-bold text-lg py-4 px-6 rounded-2xl transition-transform hover:scale-[1.02] active:scale-[0.98] shadow-lg flex items-center justify-center space-x-2" style="background-color: {{ $guest->event->theme_color ?? '#0ea5e9' }};">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Mark as Checked In</span>
            </button>
            <p id="check-in-status" class="text-sm font-bold text-green-600 mt-4 hidden"></p>
            @endif
        </div>
    </main>

    <script>
        const btn = document.getElementById('check-in-btn');
        const statusEl = document.getElementById('check-in-status');
        
        if (btn) {
            btn.addEventListener('click', async () => {
                btn.disabled = true;
                btn.style.opacity = '0.7';
                btn.innerHTML = '<span class="animate-pulse">Checking in...</span>';
                
                try {
                    const res = await fetch(`/api/guest/{{ $guest->unique_token }}/check-in`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await res.json();
                    
                    if (data.status === 'success') {
                        btn.style.display = 'none';
                        statusEl.innerText = "Guest successfully checked in! 🎉";
                        statusEl.classList.remove('hidden');
                        
                        // Reload page after a short delay to show updated status
                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        btn.disabled = false;
                        btn.style.opacity = '1';
                        btn.innerText = 'Try Again';
                        alert(data.message || 'Check-in failed');
                    }
                } catch (err) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.innerText = 'Try Again';
                    alert('Network error. Please try again.');
                }
            });
        }
    </script>
</body>
</html>
