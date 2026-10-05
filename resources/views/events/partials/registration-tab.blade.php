<div id="panel-registration" class="hidden p-6">
    <h3 class="text-lg font-bold text-slate-800 mb-6">Registration Settings</h3>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-6 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('events.registration.update', $event) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Waitlist Settings -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h4 class="text-md font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Waitlist & Auto-Approvals</h4>
            
            <div class="mb-4">
                <label class="flex items-center cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" name="allow_waitlist" value="1" class="sr-only" {{ $event->allow_waitlist ? 'checked' : '' }} onchange="document.getElementById('auto-approve-container').style.display = this.checked ? 'block' : 'none';">
                        <div class="block bg-slate-200 w-14 h-8 rounded-full"></div>
                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition transform {{ $event->allow_waitlist ? 'translate-x-6 bg-brand-500' : '' }}"></div>
                    </div>
                    <div class="ml-3 text-slate-700 font-semibold text-sm">
                        Enable Waitlist when Sold Out
                    </div>
                </label>
                <p class="text-xs text-slate-500 mt-2 ml-17">Guests can join a waitlist when event capacity is reached.</p>
            </div>

            <div id="auto-approve-container" style="display: {{ $event->allow_waitlist ? 'block' : 'none' }}">
                <label class="flex items-center cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" name="auto_approve_waitlist" value="1" class="sr-only" {{ $event->auto_approve_waitlist ? 'checked' : '' }}>
                        <div class="block bg-slate-200 w-14 h-8 rounded-full"></div>
                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition transform {{ $event->auto_approve_waitlist ? 'translate-x-6 bg-brand-500' : '' }}"></div>
                    </div>
                    <div class="ml-3 text-slate-700 font-semibold text-sm">
                        Auto-Approve Waitlist
                    </div>
                </label>
                <p class="text-xs text-slate-500 mt-2 ml-17">Automatically approve waitlist members when a spot opens up (e.g. if someone cancels).</p>
            </div>
        </div>

        <!-- Custom Questions -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h4 class="text-md font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Custom Registration Questions</h4>
            <p class="text-sm text-slate-500 mb-4">Ask guests for additional information like T-shirt size or dietary restrictions when they RSVP.</p>
            
            <div id="questions-container" class="space-y-4 mb-4">
                @foreach($event->customQuestions as $index => $question)
                <div class="question-entry border border-slate-200 p-4 rounded-xl relative bg-slate-50">
                    <button type="button" class="absolute top-4 right-4 text-red-500 hover:text-red-700" onclick="this.closest('.question-entry').remove()">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                    
                    <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question->id }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Question Text</label>
                            <input type="text" name="questions[{{ $index }}][text]" value="{{ $question->question_text }}" required class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Question Type</label>
                            <select name="questions[{{ $index }}][type]" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" onchange="toggleOptions(this, {{ $index }})">
                                <option value="text" {{ $question->question_type === 'text' ? 'selected' : '' }}>Text Input</option>
                                <option value="select" {{ $question->question_type === 'select' ? 'selected' : '' }}>Dropdown (Select)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="options-container mb-3" style="display: {{ $question->question_type === 'select' ? 'block' : 'none' }}">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Dropdown Options (comma separated)</label>
                        <input type="text" name="questions[{{ $index }}][options]" value="{{ $question->options ? implode(', ', $question->options) : '' }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="e.g. Small, Medium, Large">
                    </div>
                    
                    <div>
                        <label class="flex items-center text-sm text-slate-700 cursor-pointer">
                            <input type="checkbox" name="questions[{{ $index }}][is_required]" value="1" {{ $question->is_required ? 'checked' : '' }} class="mr-2">
                            Required Question
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
            
            <button type="button" onclick="addQuestion()" class="text-sm font-semibold text-brand-600 hover:text-brand-700 bg-brand-50 hover:bg-brand-100 px-4 py-2 rounded-lg transition">
                + Add Another Question
            </button>
        </div>

        <div>
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition">
                Save Registration Settings
            </button>
        </div>
    </form>
</div>

<script>
    let questionIndex = {{ $event->customQuestions->count() }};
    
    function addQuestion() {
        const container = document.getElementById('questions-container');
        const html = `
        <div class="question-entry border border-slate-200 p-4 rounded-xl relative bg-slate-50">
            <button type="button" class="absolute top-4 right-4 text-red-500 hover:text-red-700" onclick="this.closest('.question-entry').remove()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Question Text</label>
                    <input type="text" name="questions[${questionIndex}][text]" required class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="e.g. Dietary Restrictions">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Question Type</label>
                    <select name="questions[${questionIndex}][type]" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" onchange="toggleOptions(this, ${questionIndex})">
                        <option value="text">Text Input</option>
                        <option value="select">Dropdown (Select)</option>
                    </select>
                </div>
            </div>
            
            <div class="options-container mb-3" style="display: none;">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Dropdown Options (comma separated)</label>
                <input type="text" name="questions[${questionIndex}][options]" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="e.g. Small, Medium, Large">
            </div>
            
            <div>
                <label class="flex items-center text-sm text-slate-700 cursor-pointer">
                    <input type="checkbox" name="questions[${questionIndex}][is_required]" value="1" class="mr-2">
                    Required Question
                </label>
            </div>
        </div>`;
        
        container.insertAdjacentHTML('beforeend', html);
        questionIndex++;
    }
    
    function toggleOptions(select, index) {
        const container = select.closest('.question-entry').querySelector('.options-container');
        if (select.value === 'select') {
            container.style.display = 'block';
            container.querySelector('input').required = true;
        } else {
            container.style.display = 'none';
            container.querySelector('input').required = false;
        }
    }
    
    // Toggle switch styles
    document.querySelectorAll('input[type="checkbox"].sr-only').forEach(input => {
        input.addEventListener('change', function() {
            const dot = this.closest('label').querySelector('.dot');
            if (this.checked) {
                dot.classList.add('translate-x-6', 'bg-brand-500');
            } else {
                dot.classList.remove('translate-x-6', 'bg-brand-500');
            }
        });
    });
</script>
<style>
    .ml-17 { margin-left: 4.25rem; }
</style>
