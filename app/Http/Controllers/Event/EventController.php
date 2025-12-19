<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Service\ImageService;
use Illuminate\Support\Facades\DB;


class EventController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    public function index()
    {
        $events = Event::orderBy('date', 'desc')->paginate(10);
        return EventResource::collection($events)->additional([
            'success' => true,
            'total' => $events->total(),
            'pages_left' => $events->lastPage() - $events->currentPage(),
        ]);
    }
    public function show(Event $event)
    {

        return (new EventResource($event))->additional([
            'success' => true
        ]);
    }


    public function store(CreateEventRequest $request)
    {
        $this->authorize('manage', Event::class);
        $data = $request->validated();
        $event = DB::transaction(function () use ($data, $request) {
            $event = Event::create($data);

            if ($request->hasFile('images')) {
                $this->imageService->createFile(
                    $event,
                    $request->file('images'),
                    'event_images'
                );
            }

            return $event;
        });

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => new EventResource($event),
        ], 201); // ✅ correct status
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $event = DB::transaction(function () use ($request, $event) {
            $event->update($request->validated());

            if ($request->hasFile('images')) {
                $this->imageService->updateImages(
                    $event,
                    $request->file('images'),
                    'event_images'
                );
            }

            return $event->fresh();
        });

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data'    => new EventResource($event),
        ]);
    }

    // delete
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $event->clearMediaCollection('event_images');
        $event->delete();
        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ], 200);
    }
}
