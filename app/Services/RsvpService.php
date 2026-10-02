<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\PromoCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\GuestRsvpConfirmationEmail;
use App\Mail\HostBatchRsvpNotificationEmail;
use Illuminate\Support\Facades\Log;

class RsvpService
{
    public function processSuccessfulPayment($reference, $pendingRsvp, $eventToken, $promoCodeId = null)
    {
        if (!$pendingRsvp || !$eventToken) {
            Log::error('RSVP Processing failed: Missing pending RSVP or Event Token in session.');
            return null;
        }

        $event = Event::where('tracking_access_token', $eventToken)->first();
        if (!$event) {
            Log::error("RSVP Processing failed: Event not found for token $eventToken");
            return null;
        }

        $ticket = Ticket::find($pendingRsvp['ticket_id']);
        if (!$ticket) {
            Log::error("RSVP Processing failed: Ticket not found {$pendingRsvp['ticket_id']}");
            return null;
        }

        $qty = $pendingRsvp['quantity'] ?? 1;
        $totalAmount = $ticket->price * $qty;
        $promo = null;
        
        if ($promoCodeId) {
            $promo = PromoCode::find($promoCodeId);
            if ($promo && $promo->discount_type === 'percentage') {
                $totalAmount = $totalAmount - ($totalAmount * ($promo->discount_amount / 100));
            } else if ($promo) {
                $totalAmount = $totalAmount - $promo->discount_amount;
            }
            if ($totalAmount < 0) $totalAmount = 0;
        }
        
        $amountPerGuest = $totalAmount / $qty;
        $platformFeePercent = floatval(config('services.platform.fee_percent', env('PLATFORM_FEE_PERCENT', 5)));
        $platformFeePerGuest = $amountPerGuest * ($platformFeePercent / 100);
        $hostPayoutPerGuest = $amountPerGuest - $platformFeePerGuest;
        
        $primaryGuestToken = null;
        
        \DB::transaction(function() use ($qty, &$primaryGuestToken, $event, $pendingRsvp, $ticket, $reference, $amountPerGuest, $platformFeePerGuest, $hostPayoutPerGuest, $promo) {
            for ($i = 0; $i < $qty; $i++) {
                $uniqueToken = Str::random(40);
                if ($i === 0) $primaryGuestToken = $uniqueToken;
                
                $guest = $event->guests()->create([
                    'name' => $pendingRsvp['names'][$i],
                    'email' => $pendingRsvp['emails'][$i],
                    'rsvp_status' => $pendingRsvp['rsvp_status'],
                    'unique_token' => $uniqueToken,
                    'check_in_status' => 'pending',
                    'ticket_id' => $pendingRsvp['ticket_id'],
                    'barcode_data' => $uniqueToken,
                ]);
                
                Payment::create([
                    'event_id' => $event->id,
                    'guest_id' => $guest->id,
                    'ticket_id' => $ticket->id,
                    'reference' => $reference . '-' . $i,
                    'amount' => $amountPerGuest,
                    'platform_fee' => $platformFeePerGuest,
                    'host_payout' => $hostPayoutPerGuest,
                    'status' => 'successful',
                ]);
                
                Mail::to($guest->email)->queue(new GuestRsvpConfirmationEmail($guest));
            }
            
            if ($promo) {
                $promo->increment('times_used');
            }
        });
        
        $totalGuests = $event->guests()->count();
        Mail::to($event->user->email)->queue(new HostBatchRsvpNotificationEmail($event, $totalGuests));

        Log::info("RSVP processed successfully for reference $reference");

        return $primaryGuestToken;
    }
}
