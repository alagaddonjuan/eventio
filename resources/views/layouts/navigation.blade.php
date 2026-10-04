<nav x-data="{ open: false }" class="bg-white/5 backdrop-blur-xl border-b border-white/10 sticky top-0 z-50 shadow-lg">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400 tracking-tighter">
                        EVENTIO
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('dashboard') ? 'border-indigo-400 text-white' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-600' }}">
                        {{ __('Dashboard') }}
                    </a>
                    
                    <a href="{{ route('withdrawals.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('withdrawals.index') ? 'border-indigo-400 text-white' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-600' }}">
                        {{ __('Earnings & Payouts') }}
                    </a>

                    <a href="{{ route('discovery.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('discovery.*') ? 'border-amber-400 text-white' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-600' }}">
                        <svg class="w-4 h-4 mr-1.5 {{ request()->routeIs('discovery.*') ? 'text-amber-400' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        {{ __('Explore') }}
                    </a>

                    <a href="{{ route('marketplace.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('marketplace.*') ? 'border-fuchsia-400 text-white' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-600' }}">
                        <svg class="w-4 h-4 mr-1.5 {{ request()->routeIs('marketplace.*') ? 'text-fuchsia-400' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        {{ __('Marketplace') }}
                    </a>

                    <a href="{{ route('promoter.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('promoter.*') ? 'border-emerald-400 text-white' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-600' }}">
                        <svg class="w-4 h-4 mr-1.5 {{ request()->routeIs('promoter.*') ? 'text-emerald-400' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        {{ __('Promoter Hub') }}
                    </a>

                    @if(auth()->check() && auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('admin.*') ? 'border-indigo-400 text-white' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-600' }}">
                        {{ __('Admin Dashboard') }}
                    </a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-white/10 text-sm leading-4 font-medium rounded-xl text-slate-200 bg-white/5 hover:bg-white/10 focus:outline-none transition ease-in-out duration-150 backdrop-blur-md">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="bg-slate-800 border border-slate-700 rounded-md shadow-lg overflow-hidden">
                            <x-dropdown-link :href="route('profile.edit')" class="text-slate-300 hover:bg-slate-700 hover:text-white">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();" class="text-slate-300 hover:bg-slate-700 hover:text-white">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-300 hover:bg-white/10 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-slate-900 border-b border-white/10">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block ps-3 pe-4 py-2 border-l-4 text-base font-medium transition-colors duration-150 {{ request()->routeIs('dashboard') ? 'border-indigo-400 text-indigo-300 bg-indigo-500/10' : 'border-transparent text-slate-400 hover:text-slate-200 hover:bg-white/5 hover:border-slate-600' }}">
                {{ __('Dashboard') }}
            </a>
            <a href="{{ route('withdrawals.index') }}" class="block ps-3 pe-4 py-2 border-l-4 text-base font-medium transition-colors duration-150 {{ request()->routeIs('withdrawals.index') ? 'border-indigo-400 text-indigo-300 bg-indigo-500/10' : 'border-transparent text-slate-400 hover:text-slate-200 hover:bg-white/5 hover:border-slate-600' }}">
                {{ __('Earnings & Payouts') }}
            </a>
            <a href="{{ route('discovery.index') }}" class="block ps-3 pe-4 py-2 border-l-4 text-base font-medium transition-colors duration-150 {{ request()->routeIs('discovery.*') ? 'border-amber-400 text-amber-300 bg-amber-500/10' : 'border-transparent text-slate-400 hover:text-slate-200 hover:bg-white/5 hover:border-slate-600' }}">
                {{ __('Explore Events') }}
            </a>
            <a href="{{ route('marketplace.index') }}" class="block ps-3 pe-4 py-2 border-l-4 text-base font-medium transition-colors duration-150 {{ request()->routeIs('marketplace.*') ? 'border-fuchsia-400 text-fuchsia-300 bg-fuchsia-500/10' : 'border-transparent text-slate-400 hover:text-slate-200 hover:bg-white/5 hover:border-slate-600' }}">
                {{ __('Marketplace') }}
            </a>
            <a href="{{ route('promoter.dashboard') }}" class="block ps-3 pe-4 py-2 border-l-4 text-base font-medium transition-colors duration-150 {{ request()->routeIs('promoter.*') ? 'border-emerald-400 text-emerald-300 bg-emerald-500/10' : 'border-transparent text-slate-400 hover:text-slate-200 hover:bg-white/5 hover:border-slate-600' }}">
                {{ __('Promoter Hub') }}
            </a>
            @if(auth()->check() && auth()->user()->is_admin)
            <a href="{{ route('admin.dashboard') }}" class="block ps-3 pe-4 py-2 border-l-4 text-base font-medium transition-colors duration-150 {{ request()->routeIs('admin.*') ? 'border-indigo-400 text-indigo-300 bg-indigo-500/10' : 'border-transparent text-slate-400 hover:text-slate-200 hover:bg-white/5 hover:border-slate-600' }}">
                {{ __('Admin Dashboard') }}
            </a>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-white/10">
            <div class="px-4">
                <div class="font-medium text-base text-slate-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-slate-400 hover:text-slate-200 hover:bg-white/5 hover:border-slate-600 transition-colors duration-150">
                    {{ __('Profile') }}
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="block ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-slate-400 hover:text-slate-200 hover:bg-white/5 hover:border-slate-600 transition-colors duration-150">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
