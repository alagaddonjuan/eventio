<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;

class EventDiscoveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('affiliateProgram')
            ->where('is_suspended', false)
            ->where('event_date', '>=', Carbon::now())
            ->orderBy('event_date', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('venue_name', 'like', "%{$search}%");
            });
        }

        $events = $query->paginate(12);

        return view('discovery.index', compact('events'));
    }
}
