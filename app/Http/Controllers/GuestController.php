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
            'quantity' => 'required|integer|min:1|max:10',
            'names' => 'required|array|min:1',
            'names.*' => 'required|string|max:255',
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email|max:255',
            'rsvp_status' => 'required|in:attending,declined',
            'g-recaptcha-response' => ['required', new \App\Rules\Recaptcha()],
        ];

        if ($event->tickets()->count() > 0 && $request->input('rsvp_status') === 'attending') {
            $rules['ticket_id'] = 'required|exists:tickets,id';
        }

        $validated = $request->validate($rules);
        $qty = $validated['quantity'];
        
        // Ensure arrays match quantity
        if (count($validated['names']) != $qty || count($validated['emails']) != $qty) {
            return back()->withErrors(['names' => 'Guest details do not match the selected quantity.'])->withInput();
        }

        // Check if any of these emails already registered for this event
        $emails = array_map(function($e) { return strtolower(trim($e)); }, $validated['emails']);
        $existingCount = $event->guests()->whereIn('email', $emails)->count();
        if ($existingCount > 0) {
            return back()->withErrors(['emails' => 'One or more of these email addresses have already registered for this event.'])->withInput();
        }

        // Ensure ticket belongs to the event and check capacity
        if (isset($validated['ticket_id'])) {
            $ticket = $event->tickets()->where('id', $validated['ticket_id'])->firstOrFail();
            
            if ($ticket->capacity !== null) {
                $sold = $ticket->guests()->count();
                if (($sold + $qty) > $ticket->capacity) {
                    return back()->withErrors(['ticket_id' => 'Not enough tickets available. Only ' . max(0, $ticket->capacity - $sold) . ' left.']);
                }
            }
        }

        // Check if ticket requires payment
        if (isset($validated['ticket_id'])) {
            if ($ticket->price > 0 && $validated['rsvp_status'] === 'attending') {
                session([
                    'pending_rsvp_data' => $validated,
                    'pending_event_token' => $eventToken
                ]);
                return redirect()->route('guest.payment.form');
            }
        }

        // If free or declined, process immediately
        $primaryGuestToken = null;
        
        for ($i = 0; $i < $qty; $i++) {
            $uniqueToken = Str::random(40);
            if ($i === 0) $primaryGuestToken = $uniqueToken; // Store first guest's token for redirect
            
            $guest = $event->guests()->create([
                'name' => $validated['names'][$i],
                'email' => $validated['emails'][$i],
                'rsvp_status' => $validated['rsvp_status'],
                'unique_token' => $uniqueToken,
                'check_in_status' => 'pending',
                'ticket_id' => $validated['ticket_id'] ?? null,
                'barcode_data' => $uniqueToken,
            ]);
            
            // Only send confirmation if attending
            if ($validated['rsvp_status'] === 'attending') {
                \Illuminate\Support\Facades\Mail::to($guest->email)->send(new \App\Mail\GuestRsvpConfirmationEmail($guest));
            }
        }
        
        // Notify host for every 10th guest across the whole event
        $totalGuests = $event->guests()->count();
        if ($totalGuests > 0 && $totalGuests % 10 === 0) {
            \Illuminate\Support\Facades\Mail::to($event->user->email)->send(new \App\Mail\HostBatchRsvpNotificationEmail($event, $totalGuests));
        }

        if ($validated['rsvp_status'] === 'declined') {
            return back()->with('success', 'Thank you for letting us know!');
        }

        // Redirect primary contact to their portal
        return redirect()->route('guest.portal', $primaryGuestToken);
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
