<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Booking;

class PublicBookingController extends Controller
{
    // GET /public/boats?company=SAMARA
    public function boats(Request $request)
    {
        $company = $request->company;
        $boats = Trip::where('company', $company)
            ->pluck('boat')
            ->unique()
            ->values();

        return response()->json($boats);
    }

    // GET /public/availabilities?company=SAMARA&boat=...&date_from=...&date_to=...&trip_type=...
    public function availabilities(Request $request)
    {
        $query = Trip::query()->where('company', $request->company);

        if ($request->boat) $query->where('boat', $request->boat);
        if ($request->trip_type) $query->where('trip_type', $request->trip_type);
        if ($request->date_from) $query->whereDate('start_date', '>=', $request->date_from);
        if ($request->date_to) $query->whereDate('end_date', '<=', $request->date_to);

        $trips = $query->whereIn('status', ['published','active'])->get();

        return response()->json(
            $trips->map(fn($t) => [
                'id' => $t->id,
                'boat' => $t->boat,
                'dates' => [$t->start_date, $t->end_date],
                'trip_type' => $t->trip_type,
                'status' => $t->status,
                'available_rooms_count' => $t->guests, // or custom logic
            ])
        );
    }

    // GET /public/availability/{id}
    public function availabilityDetail($id)
    {
        $trip = Trip::findOrFail($id);

        // Example: get available rooms
        preg_match('/\((\d+)\s*rooms?\)/i', $trip->boat, $matches);
        $totalRooms = $matches[1] ?? 0;

        $bookedRooms = Booking::where('trip_id', $trip->id)->pluck('guests')->toArray();

        return response()->json([
            'id' => $trip->id,
            'boat' => $trip->boat,
            'trip_type' => $trip->trip_type,
            'dates' => [$trip->start_date, $trip->end_date],
            'rooms' => [
                'total' => (int) $totalRooms,
                'booked' => $bookedRooms,
                'available' => array_values(array_diff(range(1, $totalRooms), $bookedRooms)),
            ]
        ]);
    }

    // POST /public/prebooking
    public function prebooking(Request $request)
    {
        $validated = $request->validate([
            'company' => 'required|string',
            'availability_id' => 'required|exists:trips,id',
            'trip_type' => 'required|string',
            'rooms' => 'required|array',
            'lead_guest.name' => 'required|string|max:255',
            'lead_guest.email' => 'required|email',
            'lead_guest.phone' => 'nullable|string',
            'notes' => 'nullable|string',
            'source' => 'nullable|string',
        ]);

        $trip = Trip::find($validated['availability_id']);

        $booking = Booking::create([
            'trip_id' => $trip->id,
            'customer_name' => $validated['lead_guest']['name'],
            'email' => $validated['lead_guest']['email'],
            'phone_number' => $validated['lead_guest']['phone'] ?? null,
            'booking_status' => 'pre_booking',
            'source' => $validated['source'] ?? 'direct',
            'notes' => $validated['notes'] ?? null,
            'guests' => collect($validated['rooms'])->sum('guests_count'),
        ]);

        return response()->json([
            'status' => 'success',
            'booking_id' => $booking->id,
            'message' => 'Pre-booking created successfully.'
        ]);
    }

    // GET /public/ics/boat/{boat}
    public function icsFeed($boat)
    {
        // Optional: generate ICS calendar feed for the boat
    }
}
