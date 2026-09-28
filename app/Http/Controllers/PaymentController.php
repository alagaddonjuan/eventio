<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Guest;
use Illuminate\Support\Facades\Mail;
use App\Mail\GuestRsvpConfirmationEmail;

class PaymentController extends Controller
{
    /**
     * Show the payment form.
     */
    public function showPaymentForm()
    {
        $pendingRsvp = session('pending_rsvp_data');
        if (!$pendingRsvp) {
            return redirect('/')->withErrors(['error' => 'No pending RSVP found.']);
        }

        $ticket = Ticket::findOrFail($pendingRsvp['ticket_id']);
        
        return view('guest.payment', compact('ticket', 'pendingRsvp'));
    }

    /**
     * PART 1: Start the charge and request OTP
     */
    public function initiateCharge(Request $request)
    {
        $request->validate([
            'card_number' => 'required',
            'cvv' => 'required',
            'expiry_month' => 'required|size:2',
            'expiry_year' => 'required|size:2',
            'promo_code' => 'nullable|string'
        ]);

        $pendingRsvp = session('pending_rsvp_data');
        if (!$pendingRsvp) {
            return redirect('/')->withErrors(['error' => 'No pending RSVP found.']);
        }

        $ticket = Ticket::findOrFail($pendingRsvp['ticket_id']);
        $qty = $pendingRsvp['quantity'] ?? 1;
        $amount = $ticket->price * $qty;
        $email = $pendingRsvp['emails'][0];
        
        $promoCodeObj = null;

        if ($request->filled('promo_code')) {
            $code = strtoupper(trim($request->promo_code));
            $promoCodeObj = \App\Models\PromoCode::where('event_id', $ticket->event_id)
                ->where('code', $code)
                ->first();

            if (!$promoCodeObj || !$promoCodeObj->isValid()) {
                return back()->withErrors(['promo_code' => 'Invalid or expired promo code.'])->withInput();
            }

            if ($promoCodeObj->discount_type === 'percentage') {
                $amount = $amount - ($amount * ($promoCodeObj->discount_amount / 100));
            } else {
                $amount = $amount - $promoCodeObj->discount_amount;
            }

            if ($amount < 0) $amount = 0;
            
            // If the ticket becomes free, bypass payment gateway entirely
            if ($amount == 0) {
                return $this->bypassPaymentAndComplete($promoCodeObj);
            }
        }

        $baseUrl = env('GATEWAY_BASE_URL');
        $clientId = env('GATEWAY_CLIENT_ID');
        $secretKey = env('GATEWAY_SECRET_KEY');

        // STEP 1: Create Payment (V2)
        $initResponse = Http::withBasicAuth($clientId, $secretKey)
            ->post($baseUrl . '/api/pgs/payment/v2/createPayment', [
                'amount' => $amount,
                'email' => $email,
            ])->json();

        // STEP 2: Encrypt Card 
        $encryptedCardData = $this->encryptCardData([
            'card_number' => $request->card_number,
            'cvv' => $request->cvv,
            'expiry' => $request->expiry_month . '/' . $request->expiry_year
        ]);

        // STEP 3: Charge Card
        $chargeResponse = Http::withBasicAuth($clientId, $secretKey)
            ->post($baseUrl . '/api/cps/v1/chargeCard', [
                'client' => $encryptedCardData,
            ])->json();

        // STEP 4: Decrypt Message to get the Payment ID
        $decryptedResponse = $this->decryptGatewayResponse($chargeResponse['data'] ?? '');
        $paymentId = $decryptedResponse['paymentId'] ?? null;

        // Save the paymentId to the session temporarily
        session(['pending_payment_id' => $paymentId]);
        if ($promoCodeObj) {
            session(['pending_promo_code_id' => $promoCodeObj->id]);
        }

        // Redirect the user to a page to enter their OTP
        return redirect()->route('guest.payment.otp')->with('success', 'Please enter the OTP sent to your phone or email by your bank.');
    }

    private function bypassPaymentAndComplete($promoCodeObj)
    {
        $pendingRsvp = session('pending_rsvp_data');
        $eventToken = session('pending_event_token');
        $event = Event::where('tracking_access_token', $eventToken)->firstOrFail();
        $ticket = Ticket::findOrFail($pendingRsvp['ticket_id']);
        $qty = $pendingRsvp['quantity'] ?? 1;
        
        $primaryGuestToken = null;
        
        for ($i = 0; $i < $qty; $i++) {
            $uniqueToken = Str::random(40);
            if ($i === 0) $primaryGuestToken = $uniqueToken;
            $barcodeData = $uniqueToken;

            $guest = $event->guests()->create([
                'name' => $pendingRsvp['names'][$i],
                'email' => $pendingRsvp['emails'][$i],
                'rsvp_status' => $pendingRsvp['rsvp_status'],
                'unique_token' => $uniqueToken,
                'check_in_status' => 'pending',
                'ticket_id' => $pendingRsvp['ticket_id'],
                'barcode_data' => $barcodeData,
            ]);
            
            \App\Models\Payment::create([
                'event_id' => $event->id,
                'guest_id' => $guest->id,
                'ticket_id' => $ticket->id,
                'reference' => 'PAY-FREE-'.strtoupper(Str::random(8)),
                'amount' => 0,
                'platform_fee' => 0,
                'host_payout' => 0,
                'status' => 'successful',
            ]);
            
            Mail::to($guest->email)->send(new GuestRsvpConfirmationEmail($guest));
        }
        
        $promoCodeObj->increment('times_used');
        
        $totalGuests = $event->guests()->count();
        if ($totalGuests > 0 && $totalGuests % 10 === 0) {
            Mail::to($event->user->email)->send(new \App\Mail\HostBatchRsvpNotificationEmail($event, $totalGuests));
        }

        session()->forget(['pending_rsvp_data', 'pending_event_token']);

        return redirect()->route('guest.portal', $primaryGuestToken)->with('success', 'Promo code applied successfully! Your RSVP is confirmed.');
    }

    /**
     * Show OTP Form
     */
    public function showOtpForm()
    {
        if (!session()->has('pending_payment_id')) {
            return redirect('/')->withErrors(['error' => 'No pending payment found.']);
        }
        return view('guest.otp');
    }

    /**
     * PART 2: Authorize the charge with the OTP
     */
    public function authorizeCharge(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);

        $baseUrl = env('GATEWAY_BASE_URL');
        $clientId = env('GATEWAY_CLIENT_ID');
        $secretKey = env('GATEWAY_SECRET_KEY');
        $paymentId = session('pending_payment_id');

        // STEP 5: Encrypt the OTP
        $encryptedOtp = $this->encryptCardData(['otp' => $request->otp]);

        // STEP 6: Authorize Transaction
        $authResponse = Http::withBasicAuth($clientId, $secretKey)
            ->post($baseUrl . '/api/cps/v1/authorizeTransaction', [
                'paymentId' => $paymentId,
                'auth_data' => $encryptedOtp
            ])->json();

        // Check if authorization was successful (adjust condition based on actual RexPay response)
        if (isset($authResponse['status']) && $authResponse['status'] === 'success') {
            
            $pendingRsvp = session('pending_rsvp_data');
            $eventToken = session('pending_event_token');
            $event = Event::where('tracking_access_token', $eventToken)->firstOrFail();
            $ticket = Ticket::findOrFail($pendingRsvp['ticket_id']);
            $qty = $pendingRsvp['quantity'] ?? 1;
            
            // Calculate Amount, Fee, Payout
            // We calculate total amount and then divide by qty for individual payment records
            $totalAmount = $ticket->price * $qty;
            $promo = null;
            if (session()->has('pending_promo_code_id')) {
                $promo = \App\Models\PromoCode::find(session('pending_promo_code_id'));
                if ($promo && $promo->discount_type === 'percentage') {
                    $totalAmount = $totalAmount - ($totalAmount * ($promo->discount_amount / 100));
                } else if ($promo) {
                    $totalAmount = $totalAmount - $promo->discount_amount;
                }
                if ($totalAmount < 0) $totalAmount = 0;
            }
            
            $amountPerGuest = $totalAmount / $qty;
            
            // 5% Platform Fee Config
            $platformFeePercent = floatval(env('PLATFORM_FEE_PERCENT', 5));
            $platformFeePerGuest = $amountPerGuest * ($platformFeePercent / 100);
            $hostPayoutPerGuest = $amountPerGuest - $platformFeePerGuest;
            
            $primaryGuestToken = null;
            
            for ($i = 0; $i < $qty; $i++) {
                $uniqueToken = Str::random(40);
                if ($i === 0) $primaryGuestToken = $uniqueToken;
                $barcodeData = $uniqueToken;
    
                $guest = $event->guests()->create([
                    'name' => $pendingRsvp['names'][$i],
                    'email' => $pendingRsvp['emails'][$i],
                    'rsvp_status' => $pendingRsvp['rsvp_status'],
                    'unique_token' => $uniqueToken,
                    'check_in_status' => 'pending',
                    'ticket_id' => $pendingRsvp['ticket_id'],
                    'barcode_data' => $barcodeData,
                ]);
                
                // Create Payment Record
                \App\Models\Payment::create([
                    'event_id' => $event->id,
                    'guest_id' => $guest->id,
                    'ticket_id' => $ticket->id,
                    'reference' => 'PAY-'.strtoupper(Str::random(12)),
                    'amount' => $amountPerGuest,
                    'platform_fee' => $platformFeePerGuest,
                    'host_payout' => $hostPayoutPerGuest,
                    'status' => 'successful',
                ]);
                
                Mail::to($guest->email)->send(new GuestRsvpConfirmationEmail($guest));
            }
            
            if ($promo) {
                $promo->increment('times_used');
            }
            
            // Notify host for every 10th guest
            $totalGuests = $event->guests()->count();
            if ($totalGuests > 0 && $totalGuests % 10 === 0) {
                Mail::to($event->user->email)->send(new \App\Mail\HostBatchRsvpNotificationEmail($event, $totalGuests));
            }

            session()->forget(['pending_payment_id', 'pending_rsvp_data', 'pending_event_token', 'pending_promo_code_id']);

            return redirect()->route('guest.portal', $primaryGuestToken)->with('success', 'Payment successful! Your RSVP is confirmed.');
        }

        return back()->withErrors(['otp' => 'Invalid OTP or authorization failed. Please try again.']);
    }

    /*
     |--------------------------------------------------------------------------
     | Private Helper Methods for Encryption/Decryption
     |--------------------------------------------------------------------------
     */
    
    private function encryptCardData($data)
    {
        $plaintext = json_encode($data);
        $publicKeyPath = env('REXPAY_PUBLIC_KEY_PATH');
        
        // Ensure you have uploaded the .asc public key file from RexPay to the path specified in .env
        if ($publicKeyPath && file_exists(storage_path($publicKeyPath))) {
            try {
                $keyData = file_get_contents(storage_path($publicKeyPath));
                $keyMsg = \OpenPGP_Message::parse(\OpenPGP::unarmor($keyData, 'PGP PUBLIC KEY BLOCK'));
                $dataPacket = new \OpenPGP_LiteralDataPacket($plaintext, array('format' => 'u', 'filename' => 'payload.txt'));
                $encrypted = \OpenPGP_Crypt_Symmetric::encrypt($keyMsg, new \OpenPGP_Message(array($dataPacket)));
                
                return \OpenPGP::enarmor($encrypted->to_bytes(), 'PGP MESSAGE');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('PGP Encryption Failed: ' . $e->getMessage());
            }
        }
        
        // Fallback or local dev
        return base64_encode($plaintext);
    }

    private function decryptGatewayResponse($encryptedData)
    {
        if (empty($encryptedData)) return [];
        
        $privateKeyPath = env('EVENTIO_PRIVATE_KEY_PATH');
        
        if ($privateKeyPath && file_exists(storage_path($privateKeyPath))) {
            try {
                $keyData = file_get_contents(storage_path($privateKeyPath));
                $keyMsg = \OpenPGP_Message::parse(\OpenPGP::unarmor($keyData, 'PGP PRIVATE KEY BLOCK'));
                
                $decryptor = new \OpenPGP_Crypt_RSA($keyMsg);
                $msg = \OpenPGP_Message::parse(\OpenPGP::unarmor($encryptedData, 'PGP MESSAGE'));
                $decrypted = $decryptor->decrypt($msg);
                
                foreach ($decrypted as $packet) {
                    if ($packet instanceof \OpenPGP_LiteralDataPacket) {
                        return json_decode($packet->data, true) ?? [];
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('PGP Decryption Failed: ' . $e->getMessage());
            }
        }
        
        // Fallback or local dev
        return json_decode(base64_decode($encryptedData), true) ?? []; 
    }
}
