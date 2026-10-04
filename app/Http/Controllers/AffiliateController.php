<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\AffiliateProgram;

class AffiliateController extends Controller
{
    public function manage(Event $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403);
        }

        $program = $event->affiliateProgram()->firstOrCreate(
            ['event_id' => $event->id],
            ['commission_percentage' => 10.00, 'is_active' => false]
        );

        $promoters = $event->promoterLinks()->with('user')->get();

        return view('affiliates.manage', compact('event', 'program', 'promoters'));
    }

    public function update(Request $request, Event $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'commission_percentage' => 'required|numeric|min:1|max:99',
            'is_active' => 'boolean'
        ]);

        $program = $event->affiliateProgram()->first();
        $program->update([
            'commission_percentage' => $request->commission_percentage,
            'is_active' => $request->has('is_active')
        ]);

        return back()->with('success', 'Affiliate program updated.');
    }
}
