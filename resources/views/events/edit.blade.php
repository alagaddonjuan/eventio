<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:underline">&larr; Back to Dashboard</a>
            <span class="mx-2 text-gray-300">|</span>
            Edit Event: {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-8">
                
                <div class="mb-8 border-l-4 border-amber-400 bg-amber-50 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700 font-medium">
                                If you change the Date, Venue, or Directions, an email will automatically be sent to all guests who have already RSVP'd.
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('events.update', $event) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Event Title</label>
                        <input type="text" name="title" value="{{ old('title', $event->title) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Venue Name</label>
                        <input type="text" name="venue_name" value="{{ old('venue_name', $event->venue_name) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition" required>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Latitude</label>
                            <input type="number" step="any" name="latitude" value="{{ old('latitude', $event->latitude) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Longitude</label>
                            <input type="number" step="any" name="longitude" value="{{ old('longitude', $event->longitude) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition" required>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Date & Time</label>
                            <input type="datetime-local" name="event_date" value="{{ old('event_date', \Carbon\Carbon::parse($event->event_date)->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Theme Color</label>
                            <div class="flex items-center space-x-3">
                                <input type="color" name="theme_color" value="{{ old('theme_color', $event->theme_color) }}" class="w-12 h-12 rounded-lg cursor-pointer border-0 p-0">
                                <span class="text-sm text-slate-500">Pick your brand color</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Event Banner Image (Optional)</label>
                        @if($event->banner_image)
                            <div class="mb-3">
                                <p class="text-sm text-slate-500 mb-2">Current Banner:</p>
                                <img src="{{ asset('storage/' . $event->banner_image) }}" alt="Event Banner" class="w-full h-32 object-cover rounded-xl border border-slate-200">
                            </div>
                        @endif
                        <input type="file" name="banner_image" accept="image/*" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition">
                        <p class="text-xs text-slate-500 mt-1">Leave empty to keep current banner. Recommended size: 1200x400px (Max 5MB).</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Manual Directions (Optional)</label>
                        <textarea name="manual_directions" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition">{{ old('manual_directions', $event->manual_directions) }}</textarea>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
