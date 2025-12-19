<?php

namespace App\Http\Controllers\Booking;


use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class BookingController extends Controller
{
    public function book(Event $event)
    {
        $user = Auth::user();
        $this->authorize('create', Booking::class);


        $booking = Booking::where('user_id', $user->id)->where('event_id', $event->id)->exists();
        if ($booking) {
            return response()->json([
                'success' => false,
                'message' => 'You already booked this event'
            ], 409);
        }
        if ($event->available_seats <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'No Available seats'
            ], 409);
        }

        $booking = DB::transaction(function () use ($user, $event) {
            $booking = Booking::create([
                'user_id'  => $user->id,
                'event_id' => $event->id,
                'status'   => 'confirmed'
            ]);

            $event->decrement('available_seats');

            return $booking;
        });

        return response()->json([
            'success' => true,
            'message' => 'Booking Created Successfully',
            'booking' => new BookingResource($booking)
        ], 201);
    }

    public function myBooking()
    {
        $user = Auth::user();
        $bookings = Booking::with('event')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return BookingResource::collection($bookings)->additional([
            'success' => true,
            'pages_left' => $bookings->lastPage() - $bookings->currentPage(),
            'total' => $bookings->total(),
        ]);
    }

    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);
        $user = Auth::user();
        // $booking = Booking::with(['event:id,date'])->where('id', $booking->id)->where('user_id', $user->id)->first();
        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Booking already cancelled'
            ], 409);
        }

        if ($booking->status === "cancelled") {
            return response()->json([
                'success' => false,
                'message' => "Booking already cancelled",
            ], 409);
        }

        if (now()->diffInDays($booking->event->date, false) < 7) {
            return response()->json([
                'success' => false,
                'message' => "Cannot Cancel the Booking, This event starts with less than 7 days",
            ], 400);
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'cancelled']);
            $booking->event->increment('available_seats');
        });

        return response()->json([
            'success' => true,
            'message' => "Booking Cancelled Successfully",
        ], 200);
    }
}
