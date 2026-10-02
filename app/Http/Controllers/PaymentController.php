<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\PromoCode;
use App\Models\Payment;
use App\Services\PaymentGatewayService;
use App\Services\RsvpService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $gatewayService;
    protected $rsvpService;

    public function __construct(PaymentGatewayService $gatewayService, RsvpService $rsvpService)
    {
        $this->gatewayService = $gatewayService;
        $this->rsvpService = $rsvpService;
    }

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
        $paymentReference = strtoupper(Str::random(12));
        
        return view('guest.payment', compact('ticket', 'pendingRsvp', 'paymentReference'));
    }

    /**
     * PART 1: Start the charge and request OTP
     */
    public function initiateCharge(Request $request)
    {
        $request->validate([
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
            $promoCodeObj = PromoCode::where('event_id', $ticket->event_id)
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

            if ($amount <= 0) {
                return $this->bypassPaymentAndComplete($promoCodeObj);
            }
        }

        if (!$this->gatewayService->isConfigured()) {
            return back()->withErrors(['error' => 'Payment gateway is not configured properly on the server.']);
        }

        $reference = $request->input('payment_reference') ?: strtoupper(Str::random(12));
        $callbackUrl = route('guest.payment.callback');

        $response = $this->gatewayService->createPayment($reference, $email, $amount, $callbackUrl);

        if ($response['success']) {
            $initResponse = $response['data'];
            $paymentUrl = $initResponse['data']['paymentUrl'] ?? $initResponse['paymentUrl'] ?? $initResponse['data']['checkoutUrl'] ?? null;

            if ($paymentUrl) {
                session([
                    'pending_payment_reference' => $reference,
                    'pending_promo_code_id' => $promoCodeObj ? $promoCodeObj->id : null,
                ]);
                return redirect()->away($paymentUrl);
            }
        }

        $debugMessage = is_array($response['data'] ?? null) ? json_encode($response['data']) : 'Empty or invalid response';
        return back()->withErrors(['error' => 'RexPay Error: ' . $debugMessage]);
    }

    private function bypassPaymentAndComplete($promoCodeObj)
    {
        $reference = 'PAY-FREE-' . strtoupper(Str::random(8));
        $token = $this->rsvpService->processSuccessfulPayment(
            $reference, 
            session('pending_rsvp_data'), 
            session('pending_event_token'), 
            $promoCodeObj->id
        );

        session()->forget(['pending_rsvp_data', 'pending_event_token']);

        if ($token) {
            return redirect()->route('guest.portal', $token)->with('success', 'Promo code applied successfully! Your RSVP is confirmed.');
        }

        return redirect('/')->withErrors(['error' => 'Failed to process free RSVP.']);
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
     * Polling endpoint to check payment status while user is on another tab
     */
    public function checkStatus(Request $request)
    {
        $reference = $request->query('ref') ?: session('pending_payment_reference');
        
        if (!$reference) {
            if (!session()->has('pending_rsvp_data') && !session()->has('pending_event_token')) {
                return response()->json(['status' => 'successful', 'redirect' => '/']);
            }
            return response()->json(['status' => 'pending']);
        }

        $existingPayment = Payment::where('reference', 'LIKE', $reference . '%')->where('status', 'successful')->first();
        if ($existingPayment) {
            $guest = \App\Models\Guest::find($existingPayment->guest_id);
            return response()->json(['status' => 'successful', 'redirect' => route('guest.portal', $guest->unique_token)]);
        }

        $verifyResult = $this->gatewayService->verifyPayment($reference);

        if (!$verifyResult['success']) {
            return response()->json([
                'status' => 'pending', 
                'debug' => 'All endpoints failed. Logs: ' . json_encode($verifyResult['debugLogs'])
            ]);
        }

        $verifyResponse = $verifyResult['data'];
        $isSuccessful = $this->isPaymentSuccessful($verifyResponse);

        if ($isSuccessful) {
            $token = $this->rsvpService->processSuccessfulPayment(
                $reference,
                session('pending_rsvp_data'),
                session('pending_event_token'),
                session('pending_promo_code_id')
            );
            
            session()->forget(['pending_payment_reference', 'pending_rsvp_data', 'pending_event_token', 'pending_promo_code_id']);

            if ($token) {
                return response()->json(['status' => 'successful', 'redirect' => route('guest.portal', $token)]);
            }
        }

        return response()->json(['status' => 'pending', 'debug' => $verifyResponse]);
    }

    /**
     * PART 2: Handle redirect callback from RexPay
     */
    public function handleCallback(Request $request)
    {
        $reference = session('pending_payment_reference') ?? $request->reference;

        if (!$reference) {
            return redirect('/')->withErrors(['error' => 'Payment reference not found.']);
        }

        $existingPayment = Payment::where('reference', 'LIKE', $reference . '%')->where('status', 'successful')->first();
        if ($existingPayment) {
            $guest = \App\Models\Guest::find($existingPayment->guest_id);
            return redirect()->route('guest.portal', $guest->unique_token)->with('success', 'Payment successful! Your RSVP is confirmed.');
        }

        $verifyResult = $this->gatewayService->verifyPayment($reference);
        $verifyResponse = $verifyResult['data'];

        $isSuccessful = $this->isPaymentSuccessful($verifyResponse, $request->status);

        if ($isSuccessful) {
            $token = $this->rsvpService->processSuccessfulPayment(
                $reference,
                session('pending_rsvp_data'),
                session('pending_event_token'),
                session('pending_promo_code_id')
            );
            
            session()->forget(['pending_payment_reference', 'pending_rsvp_data', 'pending_event_token', 'pending_promo_code_id']);

            if ($token) {
                return redirect()->route('guest.portal', $token)->with('success', 'Payment successful! Your RSVP is confirmed.');
            } else {
                return redirect('/')->withErrors(['error' => 'Your payment was successful, but RSVP data was lost. Please contact support.']);
            }
        }

        $debugDump = is_array($verifyResponse) ? json_encode($verifyResponse) : 'Failed or empty response';
        return redirect()->route('guest.payment.form')->withErrors(['error' => 'Payment failed or was cancelled. Response: ' . $debugDump]);
    }

    private function isPaymentSuccessful($verifyResponse, $requestStatus = null)
    {
        $successStatuses = ['successful', 'success', 'paid', 'approved', '00', '0'];

        if (isset($verifyResponse['data']['status']) && in_array(strtolower($verifyResponse['data']['status']), $successStatuses)) return true;
        if (isset($verifyResponse['status']) && in_array(strtolower($verifyResponse['status']), $successStatuses)) return true;
        if (isset($verifyResponse['responseCode']) && in_array(strtolower($verifyResponse['responseCode']), $successStatuses)) return true;
        if (isset($verifyResponse['data']['responseCode']) && in_array(strtolower($verifyResponse['data']['responseCode']), $successStatuses)) return true;
        if ($requestStatus && in_array(strtolower($requestStatus), $successStatuses)) return true;

        return false;
    }
}
