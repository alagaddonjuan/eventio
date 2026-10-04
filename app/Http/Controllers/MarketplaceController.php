<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\VendorCategory;
use App\Models\VendorProfile;
use App\Models\VendorBooking;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $categories = VendorCategory::all();
        $query = VendorProfile::with('category')->where('is_verified', true);

        if ($request->has('category_id') && $request->category_id) {
            $query->where('vendor_category_id', $request->category_id);
        }

        $vendors = $query->latest()->paginate(12);

        return view('marketplace.index', compact('categories', 'vendors'));
    }

    public function show(VendorProfile $vendorProfile)
    {
        $vendorProfile->load('category', 'bookings');
        $events = auth()->user()->events()->where('event_date', '>=', now())->get();
        return view('marketplace.show', compact('vendorProfile', 'events'));
    }

    public function book(Request $request, VendorProfile $vendorProfile)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'message' => 'required|string',
        ]);

        $event = auth()->user()->events()->findOrFail($request->event_id);

        VendorBooking::create([
            'event_id' => $event->id,
            'vendor_profile_id' => $vendorProfile->id,
            'status' => 'pending',
            'message' => $request->message,
        ]);

        return back()->with('success', 'Booking request sent to vendor!');
    }

    public function myProfile()
    {
        $categories = VendorCategory::all();
        $profile = VendorProfile::where('user_id', auth()->id())->first();
        $bookings = [];
        if ($profile) {
            $bookings = VendorBooking::with('event')->where('vendor_profile_id', $profile->id)->latest()->get();
        }

        return view('marketplace.profile', compact('categories', 'profile', 'bookings'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'vendor_category_id' => 'required|exists:vendor_categories,id',
            'description' => 'required|string',
            'location' => 'nullable|string',
            'base_price' => 'nullable|numeric|min:0',
        ]);

        $profile = VendorProfile::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'vendor_category_id' => $request->vendor_category_id,
                'business_name' => $request->business_name,
                'description' => $request->description,
                'location' => $request->location,
                'base_price' => $request->base_price,
                'is_verified' => true, // Auto verify for demo purposes
            ]
        );

        return back()->with('success', 'Vendor profile updated successfully!');
    }
}
