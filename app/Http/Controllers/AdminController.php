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
        
        $totalRevenue = \App\Models\Payment::where('status', 'successful')->sum('amount');
        $platformRevenue = \App\Models\Payment::where('status', 'successful')->sum('platform_fee');

        return view('admin.dashboard', compact(
            'totalUsers', 'totalEvents', 'totalGuests', 'recentSignups', 'recentEvents', 'isPremiumActive', 'totalRevenue', 'platformRevenue'
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

    public function users()
    {
        $users = User::withCount('events')->latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    public function events()
    {
        $events = Event::with('host')->withCount('guests')->latest()->paginate(20);
        return view('admin.events', compact('events'));
    }

    public function destroyEvent(Event $event)
    {
        $event->delete();
        return back()->with('success', 'Event deleted successfully.');
    }
}
