<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\PromoterLink;
use App\Models\AffiliateProgram;
use Illuminate\Support\Str;

class PromoterController extends Controller
{
    public function index()
    {
        $links = auth()->user()->promoterLinks()->with('event')->get();
        return view('promoter.dashboard', compact('links'));
    }

    public function join(Event $event)
    {
        $program = $event->affiliateProgram;
        if (!$program || !$program->is_active) {
            return back()->with('error', 'This event does not have an active affiliate program.');
        }

        $link = PromoterLink::firstOrCreate(
            ['user_id' => auth()->id(), 'event_id' => $event->id],
            ['unique_code' => Str::random(10)]
        );

        return redirect()->route('promoter.dashboard')->with('success', 'You are now a promoter for ' . $event->title . '!');
    }

    public function track($code)
    {
        $link = PromoterLink::where('unique_code', $code)->firstOrFail();
        
        $link->increment('clicks');

        // Store the promoter code in the session
        session(['promoter_code' => $code]);

        return redirect()->route('guest.rsvp', $link->event->tracking_access_token);
    }
}
