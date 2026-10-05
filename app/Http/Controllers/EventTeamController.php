<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventCohost;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EventTeamController extends Controller
{
    public function invite(Request $request, Event $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:co-host,manager',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        if ($user->id === $event->user_id) {
            return redirect()->back()->withErrors(['email' => 'You cannot invite yourself as a co-host.']);
        }

        $existing = $event->cohosts()->where('user_id', $user->id)->first();
        
        if ($existing) {
            return redirect()->back()->withErrors(['email' => 'User is already on the event team.']);
        }

        $event->cohosts()->create([
            'user_id' => $user->id,
            'role' => $request->role,
        ]);

        return redirect()->back()->with('team_success', "{$user->name} has been added as a {$request->role}!");
    }

    public function remove(Event $event, EventCohost $cohost)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($cohost->event_id !== $event->id) {
            abort(404);
        }

        $cohost->delete();

        return redirect()->back()->with('team_success', "Team member removed successfully.");
    }
}
