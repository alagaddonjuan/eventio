            <div id="panel-stream" class="p-4 hidden">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4 px-2">Livestream Controls</h3>
                
                @if($event->stream_status === 'active')
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Active Stream</span>
                        </div>
                        <p class="text-sm font-medium text-slate-700 mb-1">Mux Playback ID</p>
                        <code class="block bg-slate-100 p-2 rounded text-xs break-all text-slate-600">{{ $event->mux_playback_id }}</code>
                        
                        <form action="{{ route('events.end-stream', $event) }}" method="POST" class="mt-4" onsubmit="return confirm('Are you sure you want to end this stream?')">
                            @csrf
                            @method('POST')
                            <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-red-700 transition">End Stream</button>
                        </form>
                    </div>
                @else
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 mb-4">
                        <form action="{{ route('events.start-stream', $event) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">YouTube Stream Key (Optional)</label>
                                <input type="text" name="youtube_stream_key" class="w-full text-sm rounded border-slate-300" placeholder="xxxx-xxxx-xxxx-xxxx">
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Facebook Stream Key (Optional)</label>
                                <input type="text" name="facebook_stream_key" class="w-full text-sm rounded border-slate-300" placeholder="xxxx-xxxx-xxxx-xxxx">
                            </div>
                            <button type="submit" class="w-full bg-brand-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-brand-700 transition">Start Mux Livestream</button>
                        </form>
                    </div>
                @endif
            </div>
