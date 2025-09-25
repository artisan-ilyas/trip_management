<?php

namespace App\Http\Controllers;
use App\Models\Guest;
use App\Models\Agent;
use App\Models\Company;
use App\Models\Trip;
use App\Models\Booking;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class TripController extends Controller
{
    // Trips
    public function trip_index()
    {
        $trips = Trip::all();
        $agents = Agent::all();
        $tripTypes = Trip::select('trip_type')->distinct()->pluck('trip_type');
        return view('admin.trips.index',compact('trips','agents', 'tripTypes'));
    }

    public function create_trip()
    {
         $agents = Agent::all();

        return view('admin.trips.create',compact('agents'));
    }

public function filter(Request $request)
{
    $query = Trip::query()->with('bookings');

    if ($request->boat) $query->where('boat', $request->boat);
    if ($request->status) $query->where('status', $request->status);
    if ($request->start_date) $query->whereDate('start_date', '>=', $request->start_date);
    if ($request->end_date) $query->whereDate('end_date', '<=', $request->end_date);

    $trips = $query->get();

    // Build resources = boats
    $boats = $trips->pluck('boat')->unique()->map(fn($boat) => [
        'id' => $boat,
        'title' => $boat
    ])->values();

    // Build events = availabilities
    $events = [];
    foreach ($trips as $trip) {
        $events[] = [
            'id' => 'trip-' . $trip->id,
            'resourceId' => $trip->boat,
            'title' => $trip->title,
            'start' => $trip->start_date,
            'end'   => $trip->end_date,
            'color' => match($trip->status) {
                'draft' => '#6c757d',
                'published' => '#007bff',
                'active' => '#28a745',
                'completed' => '#20c997',
                'cancelled' => '#dc3545',
                default => '#17a2b8',
            },
            'extendedProps' => [
                'trip_id' => $trip->id,
                'status' => $trip->status,
                'occupancy' => $trip->occupancy_percent ?? 0,
            ]
        ];

        // Nested bookings → as chips
        foreach ($trip->bookings as $booking) {
            $events[] = [
                'id' => 'booking-' . $booking->id,
                'resourceId' => $trip->boat,
                'title' => 'Booking #' . $booking->id,
                'start' => $booking->start_date,
                'end'   => $booking->end_date,
                'display' => 'list-item', // renders as small chip
                'color' => match($booking->status) {
                    'pre_booking' => '#ffc107',
                    'confirmed' => '#28a745',
                    'active' => '#17a2b8',
                    'completed' => '#20c997',
                    'cancelled' => '#dc3545',
                    default => '#6c757d',
                },
                'extendedProps' => [
                    'status' => $booking->status,
                    'lead_guest' => $booking->lead_guest,
                    'rooms' => $booking->rooms,
                    'pax' => $booking->pax,
                    'dp_status' => $booking->dp_status,
                ]
            ];
        }
    }

    return response()->json([
        'resources' => $boats,
        'events' => $events
    ]);
}


public function events(Request $request)
{
    $query = Trip::query();

    // Apply filters if passed
    if ($request->filled('boat')) {
        $query->where('boat', $request->boat);
    }
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    if ($request->filled('start_date')) {
        $query->whereDate('start_date', '>=', $request->start_date);
    }
    if ($request->filled('end_date')) {
        $query->whereDate('end_date', '<=', $request->end_date);
    }

    $trips = $query->get();

    $events = [];

    foreach ($trips as $trip) {
        $resourceId = match($trip->boat) {
            'Samara 1 (5 rooms)' => 'boat-1',
            'Samara 1 (4 rooms)' => 'boat-2',
            'Mischief (5 rooms)' => 'boat-3',
            'Samara (6 rooms)' => 'boat-4',
            default => null,
        };

        if (!$resourceId) continue;

        $events[] = [
            't_id' => $trip->id,
            'id' => 'trip-' . $trip->id,
            'resourceId' => $resourceId,
            'title' => $trip->title,
            'start' => $trip->start_date,
            'end' => $trip->end_date,
            'status' => $trip->status,
            'region' => $trip->region,
            'trip_type' => $trip->trip_type,
            'guests' => $trip->guests,
            'price' => $trip->price,
            'notes' => $trip->notes,
        ];
    }

    // dd($events);

    return response()->json($events);
}



    public function store_trip(Request $request)
    {
       

        Trip::create([
            'title'            => $request->title,
            'region'           => $request->region,
            'status'           => $request->status,
            'trip_type'        => $request->trip_type,
            'leading_guest_id' => $request->leading_guest_id,
            'notes'            => $request->notes,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'guests'           => $request->guests,
            'price'            => $request->price,
            'boat'             => $request->boat,
            'agent_id'         => $request->agent_id,
         
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip created successfully.');
    }

    public function show($id)
    {
        $trip = Trip::with(['agent', 'guestList.otherGuests'])->findOrFail($id);
        return view('admin.trips.detail', compact('trip'));
    }



    public function update_trip(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        $trip->update([
            'title'            => $request->title,
            'region'           => $request->region,
            'status'           => $request->status,
            'trip_type'        => $request->trip_type,
            'leading_guest_id' => $request->leading_guest_id,
            'notes'            => $request->notes,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'guests'           => $request->guests,
            'price'            => $request->price,
            'boat'             => $request->boat,
            'agent_id'         => $request->agent_id,
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip updated successfully.');
    }


    public function destroy_trip($id)
    {
        $trip = Trip::findOrFail($id);
        $trip->delete();

        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully.');
    }


public function getRooms($tripId)
{
    $trip = Trip::findOrFail($tripId);

    // Extract total rooms from boat name
    preg_match('/\((\d+)\s*rooms?\)/i', $trip->boat, $matches);
    $totalRooms = isset($matches[1]) ? (int)$matches[1] : 0;

    // Already booked guest numbers
    $booked = Booking::where('trip_id', $tripId)->pluck('guests')->toArray();

    // Available = total minus booked
    $availableRooms = array_values(array_diff(range(1, $totalRooms), $booked));

    return response()->json([
        'rooms' => $availableRooms
    ]);
}

}
