<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Event;
use App\Models\GuestAnswer;
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
        
        if ($event->is_suspended) {
            abort(404, 'This event is currently unavailable.');
        }
        
        // Increment page views
        $event->increment('page_views');
        
        $isSoldOut = false;
        $hasCapacityConfigured = false;
        $totalRemaining = 0;

        if ($event->tickets->count() > 0) {
            foreach ($event->tickets as $ticket) {
                if ($ticket->capacity !== null) {
                    $hasCapacityConfigured = true;
                    $sold = $ticket->guests()->count();
                    $remaining = max(0, $ticket->capacity - $sold);
                    $totalRemaining += $remaining;
                }
            }
        }

        if ($hasCapacityConfigured && $totalRemaining === 0) {
            $isSoldOut = true;
        }
        
        // Pass custom questions if any
        $customQuestions = $event->customQuestions;
        
        return view('guest.rsvp', compact('event', 'isSoldOut', 'customQuestions'));
    }

    /**
     * Store a new guest from the RSVP form.
     */
    public function storeGuest(Request $request, $eventToken)
    {
        $event = Event::where('tracking_access_token', $eventToken)->firstOrFail();
        
        if ($event->is_suspended) {
            abort(404, 'This event is currently unavailable.');
        }

        $rules = [
            'quantity' => 'required|integer|min:1|max:10',
            'names' => 'required|array|min:1',
            'names.*' => 'required|string|max:255',
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email|max:255',
            'rsvp_status' => 'required|in:attending,declined',
            'answers' => 'nullable|array',
            'answers.*' => 'nullable|array',
        ];

        if (config('services.recaptcha.site_key')) {
            $rules['g-recaptcha-response'] = ['required', new \App\Rules\Recaptcha()];
        }

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
            
            // Save custom answers
            if (isset($validated['answers'][$i]) && is_array($validated['answers'][$i])) {
                foreach ($validated['answers'][$i] as $questionId => $answerText) {
                    if (!empty($answerText)) {
                        GuestAnswer::create([
                            'guest_id' => $guest->id,
                            'custom_question_id' => $questionId,
                            'answer_text' => is_array($answerText) ? json_encode($answerText) : $answerText,
                        ]);
                    }
                }
            }
            
            // Only send confirmation if attending
            if ($validated['rsvp_status'] === 'attending') {
                \Illuminate\Support\Facades\Mail::to($guest->email)->send(new \App\Mail\GuestRsvpConfirmationEmail($guest));
            }
        }
        
        // Promoter tracking for free tickets (or declined RSVPs if we wanted, but let's stick to all RSVPs)
        $promoterCode = session('promoter_code');
        if ($promoterCode && $event->affiliateProgram && $event->affiliateProgram->is_active) {
            $promoterLink = \App\Models\PromoterLink::where('unique_code', $promoterCode)
                ->where('event_id', $event->id)
                ->first();
                
            if ($promoterLink) {
                $promoterLink->increment('sales_count', $qty);
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

        if ($event->is_suspended) {
            abort(404, 'This event is currently unavailable.');
        }

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

        $otherGuests = $event->guests()
            ->where('id', '!=', $guest->id)
            ->where('rsvp_status', 'attending')
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('guest.portal', [
            'guest' => $guest,
            'event' => $event,
            'phase' => $phase,
            'otherGuests' => $otherGuests,
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

    /**
     * Show the ticket verification page when a QR code is scanned.
     */
    public function verifyTicket($token)
    {
        $guest = Guest::with(['event', 'ticket'])->where('unique_token', $token)->firstOrFail();
        return view('guest.verify', compact('guest'));
    }

    /**
     * Generate and download an ICS Calendar file
     */
    public function downloadIcs($token)
    {
        $guest = Guest::with('event')->where('unique_token', $token)->firstOrFail();
        $event = $guest->event;

        $startDate = $event->event_date->format('Ymd\THis\Z');
        $endDate = $event->event_date->addHours(3)->format('Ymd\THis\Z'); // Default to 3 hours duration

        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Eventio//Eventio Calendar//EN\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:" . uniqid() . "@eventio.com\r\n";
        $ics .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
        $ics .= "DTSTART:" . $startDate . "\r\n";
        $ics .= "DTEND:" . $endDate . "\r\n";
        $ics .= "SUMMARY:" . $event->title . "\r\n";
        $ics .= "LOCATION:" . $event->venue_name . "\r\n";
        $ics .= "DESCRIPTION:You are invited to " . $event->title . ". Access your digital ticket at " . route('guest.portal', $guest->unique_token) . "\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="event_' . Str::slug($event->title) . '.ics"',
        ]);
    }

    /**
     * Download Apple Wallet Pass
     */
    public function downloadAppleWallet($token)
    {
        $guest = Guest::with('event')->where('unique_token', $token)->firstOrFail();
        
        // This is a placeholder since generating a real .pkpass requires Apple Developer Certificates
        // In a real scenario, you'd use a package like 'PKPass' to generate and sign the zip archive
        
        $json = json_encode([
            "description" => $guest->event->title . " Ticket",
            "formatVersion" => 1,
            "organizationName" => "Eventio",
            "passTypeIdentifier" => "pass.com.eventio.ticket",
            "serialNumber" => $guest->unique_token,
            "teamIdentifier" => "TEAMID",
            "eventTicket" => [
                "primaryFields" => [
                    ["key" => "event", "label" => "EVENT", "value" => $guest->event->title]
                ],
                "secondaryFields" => [
                    ["key" => "loc", "label" => "LOCATION", "value" => $guest->event->venue_name]
                ]
            ]
        ]);

        return response($json, 200, [
            'Content-Type' => 'application/vnd.apple.pkpass',
            'Content-Disposition' => 'attachment; filename="ticket.pkpass"',
        ]);
    }

    /**
     * Download Google Wallet Pass
     */
    public function downloadGoogleWallet($token)
    {
        $guest = Guest::with('event')->where('unique_token', $token)->firstOrFail();
        
        // Placeholder for Google Wallet JWT generation
        // Requires Google Service Account credentials
        return back()->with('success', 'Google Wallet integration requires active Google Service credentials.');
    }
}
