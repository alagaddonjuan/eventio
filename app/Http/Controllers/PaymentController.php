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
        ]);

        $pendingRsvp = session('pending_rsvp_data');
        if (!$pendingRsvp) {
            return redirect('/')->withErrors(['error' => 'No pending RSVP found.']);
        }

        $ticket = Ticket::findOrFail($pendingRsvp['ticket_id']);
        $amount = $ticket->price;
        $email = $pendingRsvp['email'];

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

        // Redirect the user to a page to enter their OTP
        return redirect()->route('guest.payment.otp')->with('success', 'Please enter the OTP sent to your phone or email by your bank.');
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
            
            session()->forget('pending_payment_id');
            
            // COMPLETE THE RSVP
            $pendingRsvp = session('pending_rsvp_data');
            $eventToken = session('pending_event_token');
            $event = Event::where('tracking_access_token', $eventToken)->firstOrFail();
            
            $uniqueToken = Str::random(40);
            $barcodeData = $uniqueToken;

            $guest = $event->guests()->create([
                'name' => $pendingRsvp['name'],
                'email' => $pendingRsvp['email'],
                'rsvp_status' => $pendingRsvp['rsvp_status'],
                'unique_token' => $uniqueToken,
                'check_in_status' => 'pending',
                'ticket_id' => $pendingRsvp['ticket_id'],
                'barcode_data' => $barcodeData,
            ]);
            
            Mail::to($guest->email)->send(new GuestRsvpConfirmationEmail($guest));
            
            // Notify host for every 10th guest
            $totalGuests = $event->guests()->count();
            if ($totalGuests > 0 && $totalGuests % 10 === 0) {
                Mail::to($event->user->email)->send(new \App\Mail\HostBatchRsvpNotificationEmail($event, $totalGuests));
            }

            session()->forget(['pending_rsvp_data', 'pending_event_token']);

            return redirect()->route('guest.portal', $guest->unique_token)->with('success', 'Payment successful! Your RSVP is confirmed.');
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
        // Placeholder for RexPay / Global Accelerex encryption
        return base64_encode(json_encode($data)); 
    }

    private function decryptGatewayResponse($encryptedData)
    {
        // Placeholder for RexPay / Global Accelerex decryption
        if (empty($encryptedData)) return [];
        return json_decode(base64_decode($encryptedData), true) ?? []; 
    }
}
