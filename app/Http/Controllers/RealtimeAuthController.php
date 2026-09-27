<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Guest;

class RealtimeAuthController extends Controller
{
    /**
     * Verify credentials sent from the Node.js server.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'role' => 'required|in:host,guest',
            'token' => 'required|string', // Could be tracking_access_token (host) or unique_token (guest)
        ]);

        $role = $request->input('role');
        $token = $request->input('token');

        if ($role === 'host') {
            $event = Event::where('tracking_access_token', $token)->first();
            
            if ($event && $event->payment_status === 'paid') {
                return response()->json([
                    'valid' => true,
                    'event_id' => $event->id
                ]);
            }
        } elseif ($role === 'guest') {
            $guest = Guest::with('event')->where('unique_token', $token)->first();

            // Only allow tracking if the event is paid
            if ($guest && $guest->event->payment_status === 'paid') {
                return response()->json([
                    'valid' => true,
                    'event_id' => $guest->event_id,
                    'guest_id' => $guest->id,
                    'guest_name' => $guest->name
                ]);
            }
        }

        return response()->json(['valid' => false], 401);
    }
}
