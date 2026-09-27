<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Event $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $tickets = $event->tickets;
        return view('tickets.index', compact('event', 'tickets'));
    }

    public function store(Request $request, Event $event)
    {
        // Ensure the user owns this event
        if ($event->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:free,paid',
            'price' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
        ]);

        if ($validated['type'] === 'free') {
            $validated['price'] = 0;
        }

        $event->tickets()->create($validated);

        return back()->with('success', 'Ticket created successfully.');
    }

    public function destroy(Event $event, Ticket $ticket)
    {
        // Ensure the user owns this event and ticket belongs to event
        if ($event->user_id !== auth()->id() || $ticket->event_id !== $event->id) {
            abort(403, 'Unauthorized');
        }

        $ticket->delete();

        return back()->with('success', 'Ticket deleted successfully.');
    }
}
