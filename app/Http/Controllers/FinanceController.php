<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guest;
use App\Models\Agent;
use App\Models\Company;
use App\Models\Trip;
use App\Models\Booking;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
       // Finances
    public function finance_index()
    {
        $trips = Trip::all();
        $agents = Agent::all();
        return view('admin.finances.index',compact('trips','agents'));
    }

     public function create_finance()
    {
         $agents = Agent::all();
        return view('admin.trips.create',compact('agents'));
    }

    public function store_finance(Request $request)
    {

    return redirect()->route('trips.index')->with('success', 'Trip created successfully.');
}

public function update_finance(Request $request, $id)
{
    $trip = Trip::findOrFail($id);

    $trip->update([
        'start_date' => $request->start_date,
        'end_date'   => $request->end_date,
        'guests'     => $request->guests,
        'price'      => $request->price,
        'boat'       => $request->boat,
        'agent_id'   => $request->agent_id,
    ]);


    return redirect()->route('trips.index')->with('success', 'Trip updated successfully.');
}


public function destroy_finance($id)
{
    $trip = Trip::findOrFail($id);
    $trip->delete();

    return redirect()->route('trips.index')->with('success', 'Trip deleted successfully.');
}
}
