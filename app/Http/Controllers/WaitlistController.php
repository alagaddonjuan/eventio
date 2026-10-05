<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Waitlist;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function store(Request $request, $eventToken)
    {
        $event = Event::where('tracking_access_token', $eventToken)->firstOrFail();

        if (!$event->allow_waitlist) {
            abort(403, 'Waitlist is not available for this event.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'ticket_id' => 'nullable|exists:tickets,id',
        ]);

        // Check if already on waitlist
        $existing = Waitlist::where('event_id', $event->id)
            ->where('email', $validated['email'])
            ->first();

        if ($existing) {
            return back()->with('success', 'You are already on the waitlist for this event!');
        }

        $waitlist = new Waitlist($validated);
        $waitlist->event_id = $event->id;
        
        // Auto-approve logic could go here, but waitlists usually mean it's full. 
        // If it's auto-approve waitlist, maybe we approve when space frees up via a cron job.
        // For now, it stays 'pending'.
        
        $waitlist->save();

        return back()->with('success', 'You have been added to the waitlist. We will notify you if a spot opens up!');
    }
}
