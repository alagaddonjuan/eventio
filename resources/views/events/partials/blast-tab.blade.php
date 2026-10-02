            <div id="panel-blast" class="p-4 hidden">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4 px-2">Send Announcement</h3>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 text-sm text-blue-700 rounded-r-lg">
                    <p class="font-bold mb-1">Note:</p>
                    <p>Do not use this to announce Date, Venue, or Direction changes. The system automatically sends an email to all guests if you update those details in your Event Settings. Use this tool for custom announcements like 'Parking is full' or 'Don't forget your ID'.</p>
                </div>
                <form action="{{ route('events.blast', $event) }}" method="POST" class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Subject Line</label>
                        <input type="text" name="subject" required class="w-full text-sm rounded border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="e.g. Important Parking Update!">
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Message</label>
                        <textarea name="message_body" required rows="4" class="w-full text-sm rounded border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="Type your announcement here..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-brand-600 text-white py-3 rounded-lg text-sm font-semibold hover:bg-brand-700 transition shadow-sm" onclick="return confirm('Send this email to ALL registered guests right now?')">Blast to All Guests</button>
                </form>
            </div>
