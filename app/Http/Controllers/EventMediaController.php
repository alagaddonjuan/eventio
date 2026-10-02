<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventMediaController extends Controller
{
    /**
     * Fetch gallery for an event.
     */
    public function index($event_token)
    {
        $event = Event::where('tracking_access_token', $event_token)->firstOrFail();
        
        $media = $event->eventMedia()->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $media
        ]);
    }

    /**
     * Upload an image to the gallery.
     */
    public function store(Request $request, $event_token)
    {
        $event = Event::where('tracking_access_token', $event_token)->firstOrFail();

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'uploader_type' => 'required|in:host,guest',
            'uploader_id' => 'required|integer'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Store directly in public directory to avoid symlink issues on live servers
            $destinationPath = public_path('uploads/events/' . $event->id);
            $file->move($destinationPath, $filename);

            $media = $event->eventMedia()->create([
                'uploader_type' => $request->uploader_type,
                'uploader_id' => $request->uploader_id,
                'media_url' => '/uploads/events/' . $event->id . '/' . $filename,
                'media_type' => 'image',
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $media,
                'message' => 'Image uploaded successfully.'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No image provided.'
        ], 400);
    }
}
