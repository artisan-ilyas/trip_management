<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guest;
use App\Models\Agent;
use App\Models\Company;
use App\Models\Trip;
use App\Models\Booking;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // Bookings
    public function booking_index(Request $request)
    {
        $bookings = Booking::with(['trip', 'agent'])
            ->when($request->customer_name, function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->customer_name . '%');
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('booking_status', $request->status);
            })
            ->when($request->start_date, function ($q) use ($request) {
                $q->whereHas('trip', function ($q2) use ($request) {
                    $q2->whereDate('start_date', '>=', $request->start_date);
                });
            })
            ->when($request->end_date, function ($q) use ($request) {
                $q->whereHas('trip', function ($q2) use ($request) {
                    $q2->whereDate('end_date', '<=', $request->end_date);
                });
            })
            ->latest()
            ->get();

        if ($request->ajax()) {
            $html = '';
            if ($bookings->count()) {
                foreach ($bookings as $index => $booking) {
                    $html .= '
                    <tr>
                        <td>'.($index+1).'</td>
                        <td>'.($booking->customer_name ?? "—").'</td>
                        <td>'.($booking->booking_status ?? "—").'</td>
                        <td>'.(optional($booking->agent)->first_name.' '.optional($booking->agent)->last_name).'</td>
                        <td>'.($booking->trip->start_date ?? "—").'</td>
                        <td>'.($booking->trip->end_date ?? "—").'</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="copyText('.$booking->id.')">Copy Link</button>
                            <span id="linkText'.$booking->id.'" class="d-none">'.route("guest.form",$booking->token).'</span>
                        </td>
                        <td>'.($booking->source ?? "—").'</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center">
                                <a href="'.route("bookings.show",$booking->id).'" class="btn btn-sm btn-success mx-2">View</a>
                                <a href="'.route("bookings.edit",$booking->id).'" class="btn btn-sm btn-primary mx-2">Edit</a>
                                <form action="'.route("bookings.destroy",$booking->id).'" method="POST" onsubmit="return confirm(\'Are you sure?\')">
                                    '.csrf_field().method_field("DELETE").'
                                    <button type="submit" class="btn btn-sm btn-danger mx-2">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>';
                }
            } else {
                $html .= '<tr><td colspan="11" class="text-center">No Bookings available</td></tr>';
            }
            return response()->json(['html' => $html]);
        }

        return view('admin.bookings.index', compact('bookings'));
    }


    public function create_booking()
    {
         $agents = Agent::get();
         $trips = Trip::get();

        return view('admin.bookings.create',compact('agents','trips'));
    }

    


 public function store_booking(Request $request)
{
    // If creating inline availability (Trip)
    if (!$request->trip_id && $request->inline_trip) {
        $trip = Trip::create([
            'title'      => $request->trip_title,
            'boat'       => $request->boat,
            'trip_type'  => $request->trip_type,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => 'Booked', // trips = active/inactive
            'guests'     => $request->inline_guests,
            'price'      => $request->price,
            'region'     => $request->region,
        ]);

        $request->merge(['trip_id' => $trip->id]);
    }

    // Validation
    $validated = $request->validate([
        'trip_id'            => 'required|exists:trips,id',
        'customer_name'      => 'required|string|max:255',
        // Guests rule depends on inline_trip
        'guests'             => $request->inline_trip ? 'nullable' : 'required|integer|min:1',
        'inline_guests'      => $request->inline_trip ? 'required|integer|min:1' : 'nullable',
        'source'             => 'required|string|max:255',
        'email'              => 'nullable|email',
        'phone_number'       => 'nullable|string|max:20',
        'nationality'        => 'nullable|string|max:255',
        'passport_number'    => 'nullable|string|max:255',
        'booking_status'     => 'nullable|in:pending,confirmed,cancelled',
        'pickup_location_time' => 'nullable|string|max:255',
        'addons'             => 'nullable|string|max:255',
        'room_preference'    => 'nullable|in:single,double,suite',
        'agent_id'           => 'nullable|exists:agents,id',
        'comments'           => 'nullable|string',
        'notes'              => 'nullable|string',
    ]);

    // Default booking_status
    if (empty($validated['booking_status'])) {
        $validated['booking_status'] = 'pending';
    }

    // Always map guests correctly for Booking
    if ($request->inline_trip) {
        $validated['guests'] = $request->inline_guests;
    }

    // Generate unique token
    $validated['token'] = Str::random(32);

    $booking = Booking::create($validated);

    return redirect()->route('bookings.index')
        ->with('success', 'Booking created successfully. Share this link with the user: ' . route('guest.form', $booking->token));
}

    public function show_booking($id)
    {
        $booking = Booking::with(['trip', 'agent'])->findOrFail($id);
        return view('admin.bookings.detail', compact('booking'));
    }

    public function edit_booking($id)
    {
        $booking = Booking::findOrFail($id);
        $trips   = Trip::all();
        $agents  = Agent::all();

        return view('admin.bookings.edit', compact('booking', 'trips', 'agents'));
    }




  public function update_booking(Request $request, $id)
{
    $validated = $request->validate([
        'trip_id' => 'required|exists:trips,id',
        'customer_name' => 'required|string|max:255',
        'guests' => 'required|integer|min:1',
        'source' => 'required|string|max:255',
        'email' => 'nullable|email',
        'phone_number' => 'nullable|string|max:20',
        'nationality' => 'nullable|string|max:255',
        'passport_number' => 'nullable|string|max:255',
        'booking_status' => 'nullable|in:pending,confirmed,cancelled',
        'pickup_location_time' => 'nullable|string|max:255',
        'addons' => 'nullable|string|max:255',
        'room_preference' => 'nullable|in:single,double,suite',
        'agent_id' => 'nullable|exists:agents,id',
        'comments' => 'nullable|string',
        'notes' => 'nullable|string',
        'dp_paid' => 'nullable|boolean',
    ]);

    $booking = Booking::findOrFail($id);

    // ✅ If DP is paid, force status = confirmed
    if ($request->has('dp_paid') && $request->dp_paid) {
        $validated['dp_paid'] = true;
        $validated['booking_status'] = 'confirmed';
    } else {
        $validated['dp_paid'] = false;
    }

    $booking->update($validated);

    return redirect()->route('bookings.index')->with('success', 'Booking updated successfully.');
}



    public function destroy_booking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully.');
    }

    public function getRoomsByBoat(Request $request)
    {
        $boatName = $request->input('boat'); // e.g. "Samara 1 (5 rooms)"
        $tripType = $request->input('trip_type'); // 'open' or 'private'

        if (!$boatName) {
            return response()->json(['rooms' => []]);
        }

        // Extract total rooms from boat name
        preg_match('/\((\d+)\s*rooms?\)/i', $boatName, $matches);
        $totalRooms = isset($matches[1]) ? (int)$matches[1] : 0;

        $availableRooms = range(1, $totalRooms);

        // If Private Charter, auto-attach all rooms (we just return all)
        // For Open Trip, user can select multiple but not all
        return response()->json([
            'rooms' => $availableRooms
        ]);
    }

}

