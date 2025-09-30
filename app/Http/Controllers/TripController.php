<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CancellationPolicy;
use App\Models\Guest;
use App\Models\Agent;
use App\Models\Company;
use App\Models\PaymentPolicy;
use App\Models\RatePlan;
use App\Models\Trip;
use App\Models\Booking;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TripController extends Controller
{
    protected $tenant;

    public function __construct()
    {
        $this->tenant = app()->bound('tenant') ? app('tenant') : null;
    }

    // Trips
    public function trip_index()
    {
        $query = Trip::query();
        if ($this->tenant) {
            $query->where('company_id', $this->tenant->id);
        }
        $trips = $query->get();

        $agents = $this->tenant ? Agent::where('company_id', $this->tenant->id)->get() : Agent::all();
        $tripTypes = $trips->pluck('trip_type')->unique();

        return view('admin.trips.index', compact('trips', 'agents', 'tripTypes'));
    }

    public function create_trip()
    {
        $agents = $this->tenant ? Agent::where('company_id', $this->tenant->id)->get() : Agent::all();
        $ratePlans = RatePlan::all();
        $paymentPolicies = PaymentPolicy::all();
        $cancellationPolicies = CancellationPolicy::all();

        return view('admin.trips.create', compact('agents', 'ratePlans', 'paymentPolicies', 'cancellationPolicies'));
    }

    public function store_trip(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'status' => 'required|string',
            'trip_type' => 'required|string',
            'boat' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'guests' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'rate_plan_id' => 'required|exists:rate_plans,id',
            'payment_policy_id' => 'required|exists:payment_policies,id',
            'cancellation_policy_id' => 'required|exists:cancellation_policies,id',
            'notes' => 'nullable|string',
        ]);

        // Assign tenant company_id if exists
        if ($this->tenant) {
            $validated['company_id'] = $this->tenant->id;
        } else {
            $validated['company_id'] = $request->company_id;
        }

        $trip = Trip::create($validated);

        $paymentPolicy = PaymentPolicy::find($request->payment_policy_id);
        $total = $request->price;
        $dp_amount = round($total * $paymentPolicy->dp_percent / 100, 2);
        $balance_due_date = now()->parse($request->start_date)->subDays($paymentPolicy->balance_days_before_start);

        $trip->update([
            'pricing_snapshot_json' => json_encode([
                'total' => $total,
                'dp_amount' => $dp_amount
            ]),
            'payment_policy_snapshot_json' => json_encode($paymentPolicy),
            'cancellation_policy_snapshot_json' => json_encode(CancellationPolicy::find($request->cancellation_policy_id)),
            'dp_amount' => $dp_amount,
            'balance_due_date' => $balance_due_date,
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip created successfully.');
    }

    public function show($id)
    {
        $trip = Trip::with(['agent', 'guestList.otherGuests'])->findOrFail($id);

        if ($this->tenant && $trip->company_id != $this->tenant->id) {
            abort(403, 'Unauthorized');
        }

        return view('admin.trips.detail', compact('trip'));
    }

    public function update_trip(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        if ($this->tenant && $trip->company_id != $this->tenant->id) {
            abort(403, 'Unauthorized');
        }

        $trip->update($request->only([
            'title', 'region', 'status', 'trip_type', 'leading_guest_id', 
            'notes', 'start_date', 'end_date', 'guests', 'price', 'boat', 'agent_id'
        ]));

        return redirect()->route('trips.index')->with('success', 'Trip updated successfully.');
    }

    public function destroy_trip($id)
    {
        $trip = Trip::findOrFail($id);

        if ($this->tenant && $trip->company_id != $this->tenant->id) {
            abort(403, 'Unauthorized');
        }

        $trip->delete();

        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully.');
    }

    public function getRooms($tripId)
    {
        $trip = Trip::findOrFail($tripId);

        if ($this->tenant && $trip->company_id != $this->tenant->id) {
            abort(403, 'Unauthorized');
        }

        preg_match('/\((\d+)\s*rooms?\)/i', $trip->boat, $matches);
        $totalRooms = isset($matches[1]) ? (int)$matches[1] : 0;

        $booked = Booking::where('trip_id', $tripId)->pluck('guests')->toArray();
        $availableRooms = array_values(array_diff(range(1, $totalRooms), $booked));

        return response()->json(['rooms' => $availableRooms]);
    }

    // Filter and events methods also need tenant checks
    public function filter(Request $request)
    {
        $query = Trip::query()->with('bookings');

        if ($this->tenant) {
            $query->where('company_id', $this->tenant->id);
        }

        if ($request->boat) $query->where('boat', $request->boat);
        if ($request->status) $query->where('status', $request->status);
        if ($request->start_date) $query->whereDate('start_date', '>=', $request->start_date);
        if ($request->end_date) $query->whereDate('end_date', '<=', $request->end_date);

        $trips = $query->get();

        // Build resources and events same as before
        $boats = $trips->pluck('boat')->unique()->map(fn($boat) => ['id' => $boat, 'title' => $boat])->values();

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

            foreach ($trip->bookings as $booking) {
                $events[] = [
                    'id' => 'booking-' . $booking->id,
                    'resourceId' => $trip->boat,
                    'title' => 'Booking #' . $booking->id,
                    'start' => $booking->start_date,
                    'end'   => $booking->end_date,
                    'display' => 'list-item',
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

        return response()->json(['resources' => $boats, 'events' => $events]);
    }

    public function events(Request $request)
    {
        $query = Trip::query();

        if ($this->tenant) {
            $query->where('company_id', $this->tenant->id);
        }

        if ($request->filled('boat')) $query->where('boat', $request->boat);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('start_date')) $query->whereDate('start_date', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('end_date', '<=', $request->end_date);

        $trips = $query->get();
        $events = [];

        foreach ($trips as $trip) {
            $events[] = [
                't_id' => $trip->id,
                'id' => 'trip-' . $trip->id,
                'resourceId' => $trip->boat,
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

        return response()->json($events);
    }
}
    