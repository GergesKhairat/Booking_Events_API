<?php

namespace App\Http\Controllers\Api\Booking;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function book(Request $request, $event_id)
    {
        //event selection
        $event = Event::find($event_id);
        if (!$event) {
            return response()->json([
                "success" => false,
                "message" => "Event Not Found"
            ], 404);
        }
        //user selection
        $user = Auth::user();

        $booking = Booking::where('user_id', $user->id)->where('event_id', $event->id)->exists();
        //previous booking check
        if ($booking) {
            return response()->json([
                "success" => false,
                "message" => "Event is already booked !"
            ], 400);
        }
        //avilability of seats check
        if ($event->avilable_seats <= 0) {
            return response()->json([
                "success" => false,
                "message" => "SOLD OUT!"
            ], 200);
        }
        $booking = Booking::create([
            "user_id" => $user->id,
            "event_id" => $event_id,
            "status" => $request->status ?? "pending",
        ]);
        $event->decrement('avilable_seats');
        return response()->json([
            "success" => true,
            "message" => "Event Booked Successfully",
            "Booking" => new BookingResource($booking)
        ], 201);
    }
    //list bookings of all users - for admin
    public function all()
    {
        $bookings = Booking::with(['user', 'event'])->get();
        return response()->json([
            "success" => true,
            "booking" => BookingResource::collection($bookings)
        ], 200);
    }
    //list pending bookings of all users - for admin
    public function pending()
    {
        $bookings = Booking::with(['user', 'event'])->pending()->get();
        return response()->json([
            "success" => true,
            "booking" => BookingResource::collection($bookings)
        ], 200);
    }
    //list bookings of current user

    public function userBookings()
    {
        $user = Auth::user();
        $bookings = Booking::with(['user', 'event'])->where('user_id', $user->id)->get();

        return response()->json([
            "success" => true,
            "booking" => BookingResource::collection($bookings)
        ], 200);
    }

    //update
    public function update(Request $request)
    {
        $user = Auth::user();
        if ($user->role == "user") {
            $request->merge([
                "user_id" => $user->id
            ]);
        }

        $request->validate([
            "user_id" => "required|integer|exists:users,id",
            "event_id" => "required|integer|exists:events,id",
            "status" => "required|string",
        ]);

        $booking = Booking::where('user_id', $request->user_id)->where('event_id', $request->event_id)->first();
        if (!$booking) {
            return response()->json([
                "success" => false,
                "message" => "Booking Not Found !"
            ], 404);
        }
        //previlidge
        if ($user->role == "user" && $request->status != "cancelled") {
            return response()->json([
                "success" => false,
                "message" => "unAuthorized"
            ], 403);
        }
        $booking->update([
            "user_id" => $request->user_id,
            "event_id" => $request->event_id,
            "status" => $request->status
        ]);
        return response()->json([
            "success" => true,
            "message" => "booking updated !",
            "Booking" => new BookingResource($booking)
        ], 200);
    }
    
}
