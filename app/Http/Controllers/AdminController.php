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
        
        $feeSetting = Setting::where('key', 'platform_fee_percent')->first();
        $platformFeePercent = $feeSetting ? $feeSetting->value : '5';

        return view('admin.dashboard', compact(
            'totalUsers', 'totalEvents', 'totalGuests', 'recentSignups', 'recentEvents', 'isPremiumActive', 'totalRevenue', 'platformRevenue', 'platformFeePercent'
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

    public function updatePlatformFee(Request $request)
    {
        $request->validate([
            'platform_fee_percent' => 'required|numeric|min:0|max:100',
        ]);

        Setting::updateOrCreate(
            ['key' => 'platform_fee_percent'],
            ['value' => $request->platform_fee_percent, 'type' => 'string']
        );

        return back()->with('success', 'Platform fee updated successfully.');
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

    public function suspendUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend yourself.');
        }

        $user->is_suspended = !$user->is_suspended;
        $user->save();

        $status = $user->is_suspended ? 'suspended' : 'unsuspended';
        return back()->with('success', "User has been {$status} successfully.");
    }

    public function impersonateUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You are already logged in as yourself.');
        }

        auth()->login($user);
        return redirect()->route('dashboard')->with('success', "You are now impersonating {$user->name}.");
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

    public function suspendEvent(Event $event)
    {
        $event->is_suspended = !$event->is_suspended;
        $event->save();

        $status = $event->is_suspended ? 'suspended' : 'unsuspended';
        return back()->with('success', "Event has been {$status} successfully.");
    }

    public function transactions()
    {
        $transactions = \App\Models\Payment::with(['event.host', 'guest', 'ticket'])->latest()->paginate(50);
        return view('admin.transactions', compact('transactions'));
    }
}
