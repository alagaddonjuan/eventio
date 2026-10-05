<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Services\MuxService;

class EventController extends Controller
{
    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'venue_name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'event_date' => 'required|date',
            'theme_color' => 'nullable|string|max:7',
            'manual_directions' => 'nullable|string',
            'banner_image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('banners', 'public');
        }

        $event = Auth::user()->events()->create(array_merge($validated, [
            'tracking_access_token' => Str::random(60),
            'payment_status' => 'paid', // Bypassed for now
        ]));

        \Illuminate\Support\Facades\Mail::to(Auth::user()->email)->send(new \App\Mail\EventCreatedEmail($event));

        return redirect()->route('events.command-center', $event)->with('success', 'Event created successfully.');
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        if (!$event->canBeManagedBy(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        if (!$event->canBeManagedBy(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'venue_name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'event_date' => 'required|date',
            'theme_color' => 'nullable|string|max:7',
            'manual_directions' => 'nullable|string',
            'banner_image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('banners', 'public');
        }

        $originalVenue = $event->venue_name;
        $originalDate = $event->event_date;
        $originalDirections = $event->manual_directions;

        $event->update($validated);

        $changes = [];
        if ($originalVenue !== $event->venue_name) {
            $changes['venue_name'] = $originalVenue;
        }
        // Use string comparison for dates or Carbon to be safer, but string is usually fine if format is consistent
        if (substr((string)$originalDate, 0, 16) !== substr((string)$event->event_date, 0, 16)) {
            $changes['event_date'] = $originalDate;
        }
        if ($originalDirections !== $event->manual_directions) {
            $changes['manual_directions'] = $originalDirections;
        }

        if (count($changes) > 0) {
            $guestEmails = $event->guests()->whereNotNull('email')->pluck('email')->toArray();
            
            if (count($guestEmails) > 0) {
                \Illuminate\Support\Facades\Mail::bcc($guestEmails)->send(new \App\Mail\EventUpdatedEmail($event, $changes));
            }
        }

        return redirect()->route('dashboard')->with('success', 'Event updated successfully.');
    }

    /**
     * Show the host dashboard listing their events.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        $ownedEvents = $user->events;
        $cohostedEvents = $user->cohostedEvents()->with('event')->get()->pluck('event');
        
        $events = $ownedEvents->concat($cohostedEvents)->sortByDesc('created_at')->values();
        
        $eventIds = $events->pluck('id');
        
        $totalSales = \App\Models\Payment::whereIn('event_id', $eventIds)
            ->where('status', 'successful')
            ->sum('amount');
            
        $totalPayout = \App\Models\Payment::whereIn('event_id', $eventIds)
            ->where('status', 'successful')
            ->sum('host_payout');

        return view('dashboard', compact('events', 'totalSales', 'totalPayout'));
    }

    /**
     * Show the Live Command Center (protected by CheckEventSubscription).
     */
    public function commandCenter(Event $event)
    {

        // Must authorize that the authenticated user owns this event or is a co-host
        if (!$event->canBeManagedBy(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        // Load guests to prepopulate the command center sidebar
        $guests = $event->guests;

        // Analytics Data
        $guestsOverTime = $event->guests()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $ticketSales = $event->guests()
            ->select('ticket_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->with('ticket')
            ->groupBy('ticket_id')
            ->get();
            
        $conversionRate = $event->page_views > 0 
            ? round(($guests->count() / $event->page_views) * 100, 1) 
            : 0;

        return view('events.command-center', [
            'event' => $event,
            'guests' => $guests,
            'guestsOverTime' => $guestsOverTime,
            'ticketSales' => $ticketSales,
            'conversionRate' => $conversionRate,
            'nodeServerUrl' => env('NODE_SERVER_URL', 'http://localhost:3000')
        ]);
    }

    /**
     * Delete an event.
     */
    public function destroy(Event $event)
    {
        // Must authorize that the authenticated user owns this event
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $event->delete();

        return redirect()->route('dashboard')->with('success', 'Event deleted successfully.');
    }

    /**
     * Send a manual blast email to all guests.
     */
    public function sendBlast(Request $request, Event $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message_body' => 'required|string',
        ]);

        $guestEmails = $event->guests()->whereNotNull('email')->pluck('email')->toArray();

        if (count($guestEmails) > 0) {
            \Illuminate\Support\Facades\Mail::bcc($guestEmails)->send(
                new \App\Mail\EventBlastEmail($event, $validated['subject'], $validated['message_body'])
            );
            return back()->with('success', 'Announcement email sent successfully to ' . count($guestEmails) . ' guests.');
        }

        return back()->with('error', 'No guests with email addresses found to send to.');
    }

    /**
     * Start Livestream
     */
    public function startStream(Request $request, Event $event, MuxService $mux)
    {
        if ($event->user_id !== Auth::id()) abort(403);

        // Simulcast setup
        $simulcast = [];
        if ($request->filled('youtube_stream_key')) {
            $simulcast[] = [
                'url' => 'rtmp://a.rtmp.youtube.com/live2',
                'stream_key' => $request->youtube_stream_key
            ];
        }
        if ($request->filled('facebook_stream_key')) {
            $simulcast[] = [
                'url' => 'rtmps://live-api-s.facebook.com:443/rtmp/',
                'stream_key' => $request->facebook_stream_key
            ];
        }

        try {
            $streamData = $mux->createStream($simulcast);
            
            // The Mux SDK structure: $streamData->getData() -> playbackIds -> [0] -> id
            $playbackId = $streamData->getPlaybackIds()[0]->getId();
            $streamId = $streamData->getId();
            $streamKey = $streamData->getStreamKey();

            $event->update([
                'stream_status' => 'active',
                'mux_stream_id' => $streamId,
                'mux_playback_id' => $playbackId,
                'simulcast_targets' => $simulcast
            ]);

            return back()->with('success', 'Livestream created! Your stream key is: ' . $streamKey);

        } catch (\Exception $e) {
            return back()->with('error', 'Could not create livestream: ' . $e->getMessage());
        }
    }

    /**
     * End Livestream
     */
    public function endStream(Event $event, MuxService $mux)
    {
        if ($event->user_id !== Auth::id()) abort(403);

        if ($event->mux_stream_id) {
            $mux->deleteStream($event->mux_stream_id);
        }

        $event->update([
            'stream_status' => 'finished'
        ]);

        return back()->with('success', 'Livestream ended successfully.');
    }
}
