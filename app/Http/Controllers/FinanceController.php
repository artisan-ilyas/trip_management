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
    protected $tenant;

    public function __construct()
    {
        $this->tenant = app()->bound('tenant') ? app('tenant') : null;
    }

    // Finances
    public function finance_index()
    {
        $query = Trip::query();
        if ($this->tenant) {
            $query->where('company_id', $this->tenant->id);
        }
        $trips = $query->get();

        $agents = $this->tenant ? Agent::where('company_id', $this->tenant->id)->get() : Agent::all();

        return view('admin.finances.index', compact('trips', 'agents'));
    }

    public function create_finance()
    {
        $agents = $this->tenant ? Agent::where('company_id', $this->tenant->id)->get() : Agent::all();
        return view('admin.trips.create', compact('agents'));
    }

    public function store_finance(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'guests'     => 'required|integer|min:1',
            'price'      => 'required|numeric',
            'boat'       => 'required|string',
            'agent_id'   => 'required|exists:agents,id',
        ]);

        if ($this->tenant) {
            $validated['company_id'] = $this->tenant->id;
        } else {
            $validated['company_id'] = $request->company_id;
        }

        Trip::create($validated);

        return redirect()->route('trips.index')->with('success', 'Trip created successfully.');
    }

    public function update_finance(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        if ($this->tenant && $trip->company_id != $this->tenant->id) {
            abort(403, 'Unauthorized');
        }

        $trip->update($request->only([
            'start_date', 'end_date', 'guests', 'price', 'boat', 'agent_id'
        ]));

        return redirect()->route('trips.index')->with('success', 'Trip updated successfully.');
    }

    public function destroy_finance($id)
    {
        $trip = Trip::findOrFail($id);

        if ($this->tenant && $trip->company_id != $this->tenant->id) {
            abort(403, 'Unauthorized');
        }

        $trip->delete();

        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully.');
    }
}
