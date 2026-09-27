<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\WelcomeHostEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        $query = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'prompt' => 'select_account'
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            if (!$request->has('code')) {
                return redirect()->route('login')->with('error', 'Google authentication was cancelled.');
            }

            // 1. Exchange code for access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => config('services.google.redirect'),
                'grant_type' => 'authorization_code',
                'code' => $request->code,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to get access token from Google.');
            }

            $accessToken = $response->json('access_token');

            // 2. Fetch user information
            $userResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');
            
            if (!$userResponse->successful()) {
                throw new \Exception('Failed to fetch user info from Google.');
            }

            $googleUser = $userResponse->json();

            // 3. Find or Create User
            $user = User::where('google_id', $googleUser['sub'])->orWhere('email', $googleUser['email'])->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser['name'],
                    'email' => $googleUser['email'],
                    'google_id' => $googleUser['sub'],
                    'password' => Hash::make(Str::random(24)),
                ]);

                // Dispatch Welcome Email for new users
                Mail::to($user->email)->send(new WelcomeHostEmail($user));
            } elseif (!$user->google_id) {
                // Link Google ID if email already exists
                $user->update(['google_id' => $googleUser['sub']]);
            }

            Auth::login($user);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed. Please try again.');
        }
    }
}
