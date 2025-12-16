<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Service\ImageService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected $imageService;
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    public function index()
    {
        $events = Event::get();
        return response()->json([
            'success' => true,
            'count' => $events->count(),
            'data' =>  EventResource::collection($events)
        ], 200);
    }
    public function show($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => new EventResource($event)
        ], 200);
    }




    public function store(CreateEventRequest $request)
    {
        $request->validated();

        // create
        $event = Event::create([
            'title' => $request->title,
            'desc' => $request->desc,
            'location' => $request->location,
            'date' => $request->date,
            'available_seats' => $request->available_seats,
            'category_id' => $request->category_id,

        ]);
        if ($request->hasFile('images')) {
            $this->imageService->createFile($event, $request->file('images'), 'event_images');
            // foreach ($request->file('images') as $image) {
            //     $this->imageService->createFile($event, $image, 'event_images');
            // }
            // $event->addMediaFromRequest('images')->toMediaCollection('images');
        }

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not created'
            ], 400);
        }
        // response
        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
        ], 200);
    }

    // update
    public function update(UpdateEventRequest $request, $id)
    {
        $request->validated();

        $event = Event::find($id);
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found'
            ], 404);
        }

        $event->update([
            'title' => $request->title,
            'desc' => $request->desc,
            'location' => $request->location,
            'date' => $request->date,
            'available_seats' => $request->available_seats,
            'category_id' => $request->category_id,
        ]);

        if ($request->hasFile('images')) {
            $this->imageService->updateImages($event, $request->file('images'), 'event_images');
            // $event->clearMediaCollection('event_images');
            // foreach ($request->file('images') as $image) {
            //     $this->imageService->createFile($event, $image, 'event_images');
            // }
        }

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => new EventResource($event)
        ], 200);
    }

    // delete
    public function destroy($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found'
            ], 404);
        }

        $event->clearMediaCollection('event_images');
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ], 200);
    }
}
