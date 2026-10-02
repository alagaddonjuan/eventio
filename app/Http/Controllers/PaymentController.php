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
        $paymentReference = strtoupper(\Illuminate\Support\Str::random(12));
        
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

        $baseUrl = config('services.rexpay.base_url', env('GATEWAY_BASE_URL', 'https://pgs-sandbox.globalaccelerex.com'));
        $clientId = config('services.rexpay.client_id', env('GATEWAY_CLIENT_ID'));
        $secretKey = config('services.rexpay.secret_key', env('GATEWAY_SECRET_KEY'));

        if (!$clientId || !$secretKey) {
            return back()->withErrors(['error' => 'Payment gateway is not configured properly on the server.']);
        }

        $reference = $request->input('payment_reference');
        if (!$reference) {
            $reference = strtoupper(\Illuminate\Support\Str::random(12));
        }

        // Create Payment (V2)
        $initResponse = Http::withBasicAuth($clientId, $secretKey)
            ->post(rtrim($baseUrl, '/') . '/api/pgs/payment/v2/createPayment', [
                'reference' => $reference,
                'customerReference' => $reference,
                'userId' => $email,
                'amount' => $amount,
                'currency' => 'NGN',
                'email' => $email,
                
                // Try all common naming conventions to force RexPay to override the dashboard URL
                'callbackUrl' => route('guest.payment.callback'),
                'callback_url' => route('guest.payment.callback'),
                'returnUrl' => route('guest.payment.callback'),
                'return_url' => route('guest.payment.callback'),
                'redirectUrl' => route('guest.payment.callback'),
                'redirect_url' => route('guest.payment.callback'),
            ])->json();

        // Redirect to Hosted Checkout Page
        $paymentUrl = $initResponse['data']['paymentUrl'] ?? $initResponse['paymentUrl'] ?? $initResponse['data']['checkoutUrl'] ?? null;

        if ($paymentUrl) {
            session([
                'pending_payment_reference' => $reference,
                'pending_promo_code_id' => $promoCodeObj ? $promoCodeObj->id : null,
            ]);
            return redirect()->away($paymentUrl);
        }

        \Illuminate\Support\Facades\Log::error('RexPay Init Error: ', $initResponse ?? []);
        $debugMessage = is_array($initResponse) ? json_encode($initResponse) : 'Empty or invalid response';
        return back()->withErrors(['error' => 'RexPay Error: ' . $debugMessage]);
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
        \Illuminate\Support\Facades\Mail::to($event->user->email)->send(new \App\Mail\HostBatchRsvpNotificationEmail($event, $totalGuests));

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
     * Polling endpoint to check payment status while user is on another tab
     */
    public function checkStatus(Request $request)
    {
        $reference = $request->query('ref');
        if (!$reference) {
            $reference = session('pending_payment_reference');
        }
        if (!$reference) {
            @file_put_contents(public_path('rexpay_debug.txt'), 'checkStatus null reference, session: ' . json_encode(session()->all()) . PHP_EOL, FILE_APPEND);
            
            // Might be a free ticket bypass that completed, let's check if there is a pending RSVP that was processed
            if (!session()->has('pending_rsvp_data') && !session()->has('pending_event_token')) {
                // If the session was cleared, it means the RSVP was processed successfully.
                // However, without the token, we can't redirect them easily unless we saved it.
                // Let's just return a generic success to let the frontend know it's done.
                return response()->json(['status' => 'successful', 'redirect' => '/']);
            }
            return response()->json(['status' => 'pending']);
        }

        // Check if payment already processed by webhook or another poll
        $existingPayment = \App\Models\Payment::where('reference', 'LIKE', $reference . '%')->where('status', 'successful')->first();
        if ($existingPayment) {
            $guest = \App\Models\Guest::find($existingPayment->guest_id);
            return response()->json(['status' => 'successful', 'redirect' => route('guest.portal', $guest->unique_token)]);
        }

        $pgsBaseUrl = config('services.rexpay.base_url', env('GATEWAY_BASE_URL', 'https://pgs-sandbox.globalaccelerex.com'));
        $isLive = strpos($pgsBaseUrl, 'sandbox') === false;
        $cpsBaseUrl = $isLive ? 'https://cps.globalaccelerex.com' : 'https://pgs-sandbox.globalaccelerex.com';

        $clientId = config('services.rexpay.client_id', env('GATEWAY_CLIENT_ID'));
        $secretKey = config('services.rexpay.secret_key', env('GATEWAY_SECRET_KEY'));

        $endpointsToTry = [
            // Try CPS getTransactionStatus
            ['method' => 'post', 'url' => '/api/cps/v1/getTransactionStatus', 'payload' => ['transactionReference' => $reference]],
            ['method' => 'post', 'url' => '/api/cps/v1/getTransactionStatus', 'payload' => ['reference' => $reference]],
            // Try PGS getTransactionStatus
            ['method' => 'post', 'url' => '/api/pgs/payment/v1/getTransactionStatus', 'payload' => ['transactionReference' => $reference]],
            ['method' => 'post', 'url' => '/api/pgs/payment/v1/getTransactionStatus', 'payload' => ['reference' => $reference]],
            // Try PGS v2 getTransactionStatus
            ['method' => 'post', 'url' => '/api/pgs/payment/v2/getTransactionStatus', 'payload' => ['transactionReference' => $reference]],
            ['method' => 'post', 'url' => '/api/pgs/payment/v2/getTransactionStatus', 'payload' => ['reference' => $reference]],
            // Try GET getPayments filtering
            ['method' => 'get', 'url' => '/api/pgs/payment/v1/getPayments/?reference=' . $reference, 'payload' => []]
        ];

        $verifyResponse = null;
        $workingUrl = '';
        $debugLogs = [];

        foreach ($endpointsToTry as $endpoint) {
            $base = strpos($endpoint['url'], '/api/cps') === 0 ? $cpsBaseUrl : $pgsBaseUrl;
            $fullUrl = rtrim($base, '/') . $endpoint['url'];
            
            if ($endpoint['method'] === 'post') {
                $response = Http::withBasicAuth($clientId, $secretKey)->post($fullUrl, $endpoint['payload']);
            } else {
                $response = Http::withBasicAuth($clientId, $secretKey)->get($fullUrl);
            }
            
            $json = $response->json();
            $debugLogs[$endpoint['url']] = $json;

            // If it doesn't contain "No static resource", it means the endpoint exists!
            if (!isset($json['responseMessage']) || strpos($json['responseMessage'], 'No static resource') === false) {
                // If it's the getPayments endpoint, we need to extract the specific payment
                if (strpos($endpoint['url'], 'getPayments') !== false && isset($json['data']) && is_array($json['data'])) {
                    // Try to find the payment in the array
                    $found = null;
                    foreach ($json['data'] as $payment) {
                        if (isset($payment['reference']) && $payment['reference'] === $reference) {
                            $found = $payment;
                            break;
                        }
                    }
                    if ($found) {
                        $verifyResponse = ['data' => $found];
                    } else {
                        $verifyResponse = $json; // Just to fail gracefully
                    }
                } else {
                    $verifyResponse = $json;
                }
                
                $workingUrl = $endpoint['url'];
                break;
            }
        }

        \Illuminate\Support\Facades\Log::info('RexPay checkStatus verify response for reference: ' . $reference, ['workingUrl' => $workingUrl, 'response' => $verifyResponse, 'all_attempts' => $debugLogs]);

        if (!$verifyResponse) {
            return response()->json([
                'status' => 'pending', 
                'debug' => 'All endpoints failed. Logs: ' . json_encode($debugLogs)
            ]);
        }

        $isSuccessful = false;
        $successStatuses = ['successful', 'success', 'paid', 'approved', '00', '0'];

        if (isset($verifyResponse['data']['status']) && in_array(strtolower($verifyResponse['data']['status']), $successStatuses)) {
            $isSuccessful = true;
        } elseif (isset($verifyResponse['status']) && in_array(strtolower($verifyResponse['status']), $successStatuses)) {
            $isSuccessful = true;
        } elseif (isset($verifyResponse['responseCode']) && in_array(strtolower($verifyResponse['responseCode']), $successStatuses)) {
            $isSuccessful = true;
        } elseif (isset($verifyResponse['data']['responseCode']) && in_array(strtolower($verifyResponse['data']['responseCode']), $successStatuses)) {
            $isSuccessful = true;
        }

        if ($isSuccessful) {
            // Process the successful payment (same logic as handleCallback)
            $token = $this->processSuccessfulPayment($reference);
            if ($token) {
                return response()->json(['status' => 'successful', 'redirect' => route('guest.portal', $token)]);
            }
        } else {
            // Write debug info to a text file in public folder for me to inspect
            @file_put_contents(public_path('rexpay_debug.txt'), json_encode($verifyResponse) . PHP_EOL, FILE_APPEND);
        }

        return response()->json(['status' => 'pending', 'debug' => $verifyResponse]);
    }

    /**
     * Extract the successful payment processing logic so it can be reused
     */
    private function processSuccessfulPayment($reference)
    {
        $pendingRsvp = session('pending_rsvp_data');
        if (!$pendingRsvp) return null;
        
        $eventToken = session('pending_event_token');
        $event = \App\Models\Event::where('tracking_access_token', $eventToken)->first();
        if (!$event) return null;

        $ticket = \App\Models\Ticket::find($pendingRsvp['ticket_id']);
        if (!$ticket) return null;

        $qty = $pendingRsvp['quantity'] ?? 1;
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
        $platformFeePercent = floatval(env('PLATFORM_FEE_PERCENT', 5));
        $platformFeePerGuest = $amountPerGuest * ($platformFeePercent / 100);
        $hostPayoutPerGuest = $amountPerGuest - $platformFeePerGuest;
        
        $primaryGuestToken = null;
        
        for ($i = 0; $i < $qty; $i++) {
            $uniqueToken = \Illuminate\Support\Str::random(40);
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
            
            \App\Models\Payment::create([
                'event_id' => $event->id,
                'guest_id' => $guest->id,
                'ticket_id' => $ticket->id,
                'reference' => $reference . '-' . $i,
                'amount' => $amountPerGuest,
                'platform_fee' => $platformFeePerGuest,
                'host_payout' => $hostPayoutPerGuest,
                'status' => 'successful',
            ]);
            
            \Illuminate\Support\Facades\Mail::to($guest->email)->send(new \App\Mail\GuestRsvpConfirmationEmail($guest));
        }
        
        if ($promo) {
            $promo->increment('times_used');
        }
        
        $totalGuests = $event->guests()->count();
        \Illuminate\Support\Facades\Mail::to($event->user->email)->send(new \App\Mail\HostBatchRsvpNotificationEmail($event, $totalGuests));

        session()->forget(['pending_payment_reference', 'pending_rsvp_data', 'pending_event_token', 'pending_promo_code_id']);

        return $primaryGuestToken;
    }

    /**
     * PART 2: Handle redirect callback from RexPay
     */
    public function handleCallback(Request $request)
    {
        $pgsBaseUrl = config('services.rexpay.base_url', env('GATEWAY_BASE_URL', 'https://pgs-sandbox.globalaccelerex.com'));
        $isLive = strpos($pgsBaseUrl, 'sandbox') === false;
        $cpsBaseUrl = $isLive ? 'https://cps.globalaccelerex.com' : 'https://pgs-sandbox.globalaccelerex.com';

        $clientId = config('services.rexpay.client_id', env('GATEWAY_CLIENT_ID'));
        $secretKey = config('services.rexpay.secret_key', env('GATEWAY_SECRET_KEY'));
        $reference = session('pending_payment_reference') ?? $request->reference;

        if (!$reference) {
            return redirect('/')->withErrors(['error' => 'Payment reference not found.']);
        }

        // Check if it was already processed by polling
        $existingPayment = \App\Models\Payment::where('reference', 'LIKE', $reference . '%')->where('status', 'successful')->first();
        if ($existingPayment) {
            $guest = \App\Models\Guest::find($existingPayment->guest_id);
            return redirect()->route('guest.portal', $guest->unique_token)->with('success', 'Payment successful! Your RSVP is confirmed.');
        }

        $endpointsToTry = [
            ['method' => 'post', 'url' => '/api/cps/v1/getTransactionStatus', 'payload' => ['transactionReference' => $reference]],
            ['method' => 'post', 'url' => '/api/cps/v1/getTransactionStatus', 'payload' => ['reference' => $reference]],
            ['method' => 'post', 'url' => '/api/pgs/payment/v1/getTransactionStatus', 'payload' => ['transactionReference' => $reference]],
            ['method' => 'post', 'url' => '/api/pgs/payment/v1/getTransactionStatus', 'payload' => ['reference' => $reference]],
            ['method' => 'post', 'url' => '/api/pgs/payment/v2/getTransactionStatus', 'payload' => ['transactionReference' => $reference]],
            ['method' => 'post', 'url' => '/api/pgs/payment/v2/getTransactionStatus', 'payload' => ['reference' => $reference]],
            ['method' => 'get', 'url' => '/api/pgs/payment/v1/getPayments/?reference=' . $reference, 'payload' => []]
        ];

        $verifyResponse = null;

        foreach ($endpointsToTry as $endpoint) {
            $base = strpos($endpoint['url'], '/api/cps') === 0 ? $cpsBaseUrl : $pgsBaseUrl;
            $fullUrl = rtrim($base, '/') . $endpoint['url'];
            if ($endpoint['method'] === 'post') {
                $response = Http::withBasicAuth($clientId, $secretKey)->post($fullUrl, $endpoint['payload']);
            } else {
                $response = Http::withBasicAuth($clientId, $secretKey)->get($fullUrl);
            }
            $json = $response->json();
            if (!isset($json['responseMessage']) || strpos($json['responseMessage'], 'No static resource') === false) {
                if (strpos($endpoint['url'], 'getPayments') !== false && isset($json['data']) && is_array($json['data'])) {
                    $found = null;
                    foreach ($json['data'] as $payment) {
                        if (isset($payment['reference']) && $payment['reference'] === $reference) {
                            $found = $payment;
                            break;
                        }
                    }
                    if ($found) { $verifyResponse = ['data' => $found]; } 
                    else { $verifyResponse = $json; }
                } else {
                    $verifyResponse = $json;
                }
                break;
            }
        }

        \Illuminate\Support\Facades\Log::info('RexPay handleCallback verify response for reference: ' . $reference, ['response' => $verifyResponse]);

        $isSuccessful = false;
        
        $successStatuses = ['successful', 'success', 'paid', 'approved', '00', '0'];

        if (isset($verifyResponse['data']['status']) && in_array(strtolower($verifyResponse['data']['status']), $successStatuses)) {
            $isSuccessful = true;
        } elseif (isset($verifyResponse['status']) && in_array(strtolower($verifyResponse['status']), $successStatuses)) {
            $isSuccessful = true;
        } elseif (isset($verifyResponse['responseCode']) && in_array(strtolower($verifyResponse['responseCode']), $successStatuses)) {
            $isSuccessful = true;
        } elseif (isset($verifyResponse['data']['responseCode']) && in_array(strtolower($verifyResponse['data']['responseCode']), $successStatuses)) {
            $isSuccessful = true;
        } elseif ($request->has('status') && in_array(strtolower($request->status), $successStatuses)) {
            $isSuccessful = true;
        }

        if ($isSuccessful) {
            $token = $this->processSuccessfulPayment($reference);
            if ($token) {
                return redirect()->route('guest.portal', $token)->with('success', 'Payment successful! Your RSVP is confirmed.');
            } else {
                return redirect('/')->withErrors(['error' => 'Your payment was successful, but RSVP data was lost. Please contact support.']);
            }
        }

        $debugDump = is_array($verifyResponse) ? json_encode($verifyResponse) : 'Failed or empty response';
        return redirect()->route('guest.payment.form')->withErrors(['error' => 'Payment failed or was cancelled. Response: ' . $debugDump]);
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
