<?php

namespace App\Http\Controllers\Booking;


use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function book(Request $request, $eventId)
    {
        $user = Auth::user();
        $event = Event::find($eventId);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found'
            ], 404);
        }
        $booking = Booking::where('user_id', $user->id)->where('event_id', $eventId)->exists();
        if ($booking) {
            return response()->json([
                'success' => false,
                'message' => 'You already booked this event'
            ], 400);
        }
        if ($event->available_seats <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'No Available seats'
            ], 200);
        }

        $booking = Booking::create([
            "user_id" => $user->id,
            "event_id" =>  $eventId,
            "status" => $request->status ?? 'confirmed'
        ]);
        $event->decrement('available_seats');

        return response()->json([
            'success' => true,
            'message' => 'Booking Created Successfully',
            'booking' => new BookingResource($booking)
        ], 201);
    }

    public function myBooking()
    {
        $user = Auth::user();
        $bookings = Booking::with('event')->where("user_id", $user->id)->get();
        return response()->json([
            'success' => true,
            'booking' => BookingResource::collection($bookings),
        ], 200);
    }

    public function cancel(Request $request, $id)
    {

        $user = Auth::user();
        $booking = Booking::with(['event:id,date'])->where('id', $id)->where('user_id', $user->id)->first();
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => "Booking not found",
            ], 404);
        }
        if ($booking->status === "cancelled") {
            return response()->json([
                'success' => false,
                'message' => "Booking already cancelled",
            ], 400);
        }
        $eventDate = $booking->event->date;
        $daysUntilEvent = (int)  now()->diffInDays($eventDate);
        if($daysUntilEvent <3){
             return response()->json([
                'success' => false,
                'message' => "Cannot Cancel the Booking, This event starts with less than 7 days",
            ], 400);
        }

        $booking->update(['status' => 'cancelled']);
        $booking->event->increment('available_seats');

        return response()->json([
            'success' => true,
            'message' => "Booking Cancelled Successfully",
        ], 200);
    }
}
