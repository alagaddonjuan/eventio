<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Event;
use Symfony\Component\HttpFoundation\Response;

class CheckEventSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Assuming route is bound like /events/{event}/command-center
        $event = $request->route('event');
        
        if (!$event) {
            abort(404, 'Event not found.');
        }

        // Must be paid to access the Live Command Center
        if ($event->payment_status !== 'paid') {
            return redirect()->route('events.upgrade', ['event' => $event->id])
                ->with('error', 'Please upgrade your event to access the Live Command Center.');
        }

        return $next($request);
    }
}
