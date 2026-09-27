<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Guest;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalEvents = Event::count();
        $totalGuests = Guest::count();
        $recentSignups = User::latest()->take(5)->get();
        $recentEvents = Event::with('host')->latest()->take(5)->get();
        
        $premiumSetting = Setting::firstOrCreate(
            ['key' => 'premium_active'],
            ['value' => '0', 'type' => 'boolean']
        );
        $isPremiumActive = $premiumSetting->value === '1';

        return view('admin.dashboard', compact(
            'totalUsers', 'totalEvents', 'totalGuests', 'recentSignups', 'recentEvents', 'isPremiumActive'
        ));
    }

    public function togglePremium(Request $request)
    {
        $premiumSetting = Setting::firstOrCreate(
            ['key' => 'premium_active'],
            ['value' => '0', 'type' => 'boolean']
        );
        
        // Toggle the value
        $newValue = $premiumSetting->value === '1' ? '0' : '1';
        $premiumSetting->update(['value' => $newValue]);

        $status = $newValue === '1' ? 'enabled' : 'disabled';
        return back()->with('success', "Premium features have been $status globally.");
    }
}
