<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    /**
     * GUEST: View their support tickets
     */
    public function guestIndex($token)
    {
        $guest = Guest::where('unique_token', $token)->firstOrFail();
        $tickets = $guest->supportTickets()->with('messages')->latest()->get();
        return view('guest.support.index', compact('guest', 'tickets'));
    }

    /**
     * HOST: View event's support tickets
     */
    public function hostIndex(Event $event)
    {
        // Ensure user owns this event
        if ($event->user_id !== auth()->id()) {
            abort(403);
        }
        $tickets = $event->supportTickets()->with(['messages', 'guest'])->latest()->get();
        return view('host.support.index', compact('event', 'tickets'));
    }

    /**
     * GUEST: Create a new support ticket
     */
    public function guestStoreTicket(Request $request, $token)
    {
        $guest = Guest::where('unique_token', $token)->firstOrFail();
        
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        $ticket = $guest->supportTickets()->create([
            'event_id' => $guest->event_id,
            'subject' => $request->subject,
            'status' => 'open'
        ]);

        $ticket->messages()->create([
            'sender_type' => 'guest',
            'message' => $request->message
        ]);

        // Optional: Notify host via email that a new ticket was created
        // Mail::to($guest->event->user->email)->send(new ...);

        return back()->with('success', 'Your message has been sent to the host.');
    }

    /**
     * GUEST or HOST: Add a reply to an existing ticket
     */
    public function storeMessage(Request $request, $ticketId)
    {
        $request->validate(['message' => 'required|string']);
        $ticket = SupportTicket::findOrFail($ticketId);
        
        // Determine sender (Guest or Host based on route or token)
        $senderType = $request->has('host_reply') ? 'host' : 'guest';
        
        $ticket->messages()->create([
            'sender_type' => $senderType,
            'message' => $request->message
        ]);

        // If host replied, notify the guest
        if ($senderType === 'host') {
            // Re-open ticket if it was closed
            $ticket->update(['status' => 'open']);
            
            // We should ideally use a Mailable, but we can use raw mail for simplicity if needed
            Mail::raw("The host for '{$ticket->event->title}' has replied to your message.\n\nReply:\n{$request->message}\n\nYou can view the full thread in your Guest Portal.", function ($msg) use ($ticket) {
                $msg->to($ticket->guest->email)->subject('New reply from host for ' . $ticket->event->title);
            });
        } else {
            // Guest replied, notify host (optional)
        }

        return back()->with('success', 'Message sent successfully.');
    }

    /**
     * HOST: Close a ticket
     */
    public function closeTicket($ticketId)
    {
        $ticket = SupportTicket::findOrFail($ticketId);
        $ticket->update(['status' => 'closed']);
        return back()->with('success', 'Ticket closed.');
    }
}
