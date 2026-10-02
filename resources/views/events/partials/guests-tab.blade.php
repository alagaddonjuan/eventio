            <div id="panel-guests" class="p-4 block">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4 px-2">Guest Activity</h3>
                
                <ul id="guest-list" class="space-y-3">
                    @forelse($guests as $guest)
                    <li id="guest-item-{{ $guest->id }}" class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between transition hover:shadow-md">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center font-bold mr-3 shadow-sm border border-brand-200">
                                {{ substr($guest->name ?? 'G', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $guest->name ?? 'Unnamed Guest' }}</p>
                                <p class="guest-eta text-xs font-medium text-slate-500">Status: Waiting...</p>
                            </div>
                        </div>
                        <div class="guest-indicator w-3 h-3 rounded-full bg-slate-200 border border-slate-300"></div>
                    </li>
                    @empty
                    <div class="text-center py-8">
                        <p class="text-slate-400 text-sm">No guests have RSVP'd yet.</p>
                    </div>
                    @endforelse
                </ul>
            </div>
