<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    use ApiResponse;
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'desc' => 'required|string',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:png,jpg,jpeg|max:2048',
            'date' => 'required|date',
            'max_reservation' => 'required|integer|min:1',
        ]);

        $paths = [];
        if($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('events', 'public');
                $paths[] = Storage::url($path);
            }
        }

        $validated['images'] = $paths;

        $event = Event::create($validated);

        return $this->successResponse($event, 'Event created successfully', 201);
    }
}
