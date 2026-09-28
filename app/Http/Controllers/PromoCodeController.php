<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\PromoCode;

class PromoCodeController extends Controller
{
    public function store(Request $request, Event $event)
    {
        // Ensure user owns the event
        if ($event->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code,NULL,id,event_id,' . $event->id,
            'discount_type' => 'required|in:percentage,fixed',
            'discount_amount' => 'required|numeric|min:0.01',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $validated['code'] = strtoupper($validated['code']);

        $event->promoCodes()->create($validated);

        return back()->with('success', 'Promo code created successfully.');
    }

    public function destroy(Event $event, PromoCode $promoCode)
    {
        // Ensure user owns the event and promo code belongs to event
        if ($event->user_id !== auth()->id() || $promoCode->event_id !== $event->id) {
            abort(403);
        }

        $promoCode->delete();

        return back()->with('success', 'Promo code deleted successfully.');
    }
}
