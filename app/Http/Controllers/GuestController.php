<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    /**
     * Display the RSVP form.
     */
    public function rsvp($eventToken)
    {
        $event = Event::where('tracking_access_token', $eventToken)->firstOrFail();
        return view('guest.rsvp', compact('event'));
    }

    /**
     * Store a new guest from the RSVP form.
     */
    public function storeGuest(Request $request, $eventToken)
    {
        $event = Event::where('tracking_access_token', $eventToken)->firstOrFail();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'rsvp_status' => 'required|in:attending,declined',
        ];

        if ($event->tickets()->count() > 0 && $request->input('rsvp_status') === 'attending') {
            $rules['ticket_id'] = 'required|exists:tickets,id';
        }

        $validated = $request->validate($rules);

        // Prevent double filling / duplicate RSVPs by checking name or email
        $existingGuest = $event->guests()
            ->where(function ($query) use ($validated) {
                $query->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($validated['name']))])
                      ->orWhere('email', strtolower(trim($validated['email'])));
            })
            ->first();

        if ($existingGuest) {
            return back()->withErrors(['email' => 'An RSVP with this name or email has already been submitted for this event.'])->withInput();
        }

        // Ensure ticket belongs to the event
        if (isset($validated['ticket_id'])) {
            $ticket = $event->tickets()->where('id', $validated['ticket_id'])->firstOrFail();
            
            // Check capacity (very basic implementation)
            if ($ticket->capacity !== null) {
                $sold = $ticket->guests()->count();
                if ($sold >= $ticket->capacity) {
                    return back()->withErrors(['ticket_id' => 'This ticket is sold out.']);
                }
            }
        }

        $uniqueToken = Str::random(40);
        $barcodeData = $uniqueToken;

        $guest = $event->guests()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'rsvp_status' => $validated['rsvp_status'],
            'unique_token' => $uniqueToken,
            'check_in_status' => 'pending',
            'ticket_id' => $validated['ticket_id'] ?? null,
            'barcode_data' => $barcodeData,
        ]);
        
        \Illuminate\Support\Facades\Mail::to($guest->email)->send(new \App\Mail\GuestRsvpConfirmationEmail($guest));
        
        // Notify host for every 10th guest
        $totalGuests = $event->guests()->count();
        if ($totalGuests > 0 && $totalGuests % 10 === 0) {
            \Illuminate\Support\Facades\Mail::to($event->user->email)->send(new \App\Mail\HostBatchRsvpNotificationEmail($event, $totalGuests));
        }

        if ($validated['rsvp_status'] === 'declined') {
            return back()->with('success', 'Thank you for letting us know!');
        }

        // Redirect to their personal tracking portal
        return redirect()->route('guest.portal', $guest->unique_token);
    }

    /**
     * Display the Guest Portal (No login required).
     */
    public function portal($token)
    {
        $guest = Guest::with('event')->where('unique_token', $token)->firstOrFail();
        $event = $guest->event;

        $now = now();
        $eventDate = $event->event_date;
        
        // Determine phase based on time
        // Phase A: Pre-event (more than 24 hours before)
        // Phase B/C: Event day (within 24 hours before to 12 hours after)
        // Phase D: Post-event (more than 12 hours after)
        
        $phase = 'A';
        if ($now->greaterThan($eventDate->copy()->subHours(24)) && $now->lessThan($eventDate->copy()->addHours(12))) {
            $phase = 'B'; // Or C once they start tracking
        } elseif ($now->greaterThan($eventDate->copy()->addHours(12))) {
            $phase = 'D';
        }

        return view('guest.portal', [
            'guest' => $guest,
            'event' => $event,
            'phase' => $phase,
            'nodeServerUrl' => config('services.node.url', 'http://localhost:3000')
        ]);
    }

    /**
     * Webhook triggered when the JS Geolocation determines the guest is within 100m.
     */
    public function checkIn(Request $request, $token)
    {
        $guest = Guest::where('unique_token', $token)->firstOrFail();

        // Simple validation to ensure they are close enough (could re-verify lat/lng here)
        $guest->update([
            'check_in_status' => 'arrived'
        ]);

        // Send email to host
        \Illuminate\Support\Facades\Mail::to($guest->event->user->email)->send(new \App\Mail\HostGuestArrivedNotificationEmail($guest));

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in successful.'
        ]);
    }
}
