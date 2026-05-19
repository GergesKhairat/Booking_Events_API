<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Http\Services\MediaService;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected $mediaService;
    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }
    //all events
    public function index()
    {
        $events = Event::get();
        return response()->json([
            "success"   => true,
            "Events"    => EventResource::collection($events)
        ], 200);
    }
    public function allWithCategory()
    {
        $events = Event::with('category')->get();
        return response()->json([
            "success"   => true,
            "Events"    => EventResource::collection($events)
        ], 200);
    }
    //event selection
    public function returnEvent($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json([
                "success" => false,
                "message" => "Event Not Found"
            ], 404);
        }
        return response()->json([
            "success" => true,
            "Event" => new EventResource($event)
        ], 200);
        return $event;
    }
    //show
    public function show($id)
    {
        $event = $this->returnEvent($id);
        return response()->json([
            "success" => true,
            "Event" => new EventResource($event)
        ], 404);
    }
    public function showWithCategory($id)
    {
        $event = Event::with('category')->find($id);
        if (!$event) {
            return response()->json([
                "success" => false,
                "message" => "Event Not Found"
            ], 404);
        }
        return response()->json([
            "success" => true,
            "Event" => new EventResource($event)
        ], 200);
    }
    //create
    public function create(CreateEventRequest $request)
    {
        $request->validated();

        $event = Event::create([
            "title"         => $request->title,
            "description"   => $request->description,
            "location"      => $request->location,
            "start_date"    => $request->start_date,
            "avilable_seats"    => $request->avilable_seats,
            "category_id"       => $request->category_id,
        ]);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $this->mediaService->createMedia($event, $image, 'main_image');
            }
        }
        return response()->json([
            'success' => true,
            'Message' => "Event Created Successfully"
        ], 201);
    }

    //edit
    public function edit(UpdateEventRequest $request, $id)
    {
        $request->validated();
        $event = $this->returnEvent($id);
        $event->update([
            "title"         => $request->title,
            "description"   => $request->description,
            "location"      => $request->location,
            "start_date"    => $request->start_date,
            "avilable_seats"    => $request->avilable_seats,
            "category_id"       => $request->category_id
        ]);
        if ($request->hasFile('image')) {
            $this->mediaService->editMedia($event, $request->file('image'), "main_image");
        }
        return response()->json([
            "success" => true,
            "message" => "Event Updated Successfully"
        ], 200);
    }
    //delete
    public function delete($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json([
                "success" => false,
                "message" => "Event Not Found"
            ], 404);
        }
        $this->mediaService->deleteMedia($event, "main_image");
        $event->delete();
        return response()->json([
            "success" => true,
            "message" => "event deleted successfully"
        ], 200);
    }
}
