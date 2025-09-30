<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RatePlan;

class RatePlanController extends Controller
{
    public function index() {
        $plans = RatePlan::all();
        return view('admin.rate_plans.index', compact('plans'));
    }

    public function create() {
        return view('admin.rate_plans.create');
    }

    public function store(Request $request) {
        $request->validate([
            'name'=>'required|string|max:255',
            'currency'=>'required|string|max:3',
            'base_price_type'=>'required|in:per_room,charter'
        ]);
        RatePlan::create($request->all());
        return redirect()->route('rate-plans.index')->with('success','Rate Plan created.');
    }

    public function edit(RatePlan $ratePlan) {
        return view('admin.rate_plans.edit', compact('ratePlan'));
    }

    public function update(Request $request, RatePlan $ratePlan) {
        $request->validate([
            'name'=>'required|string|max:255',
            'currency'=>'required|string|max:3',
            'base_price_type'=>'required|in:per_room,charter'
        ]);
        $ratePlan->update($request->all());
        return redirect()->route('rate-plans.index')->with('success','Rate Plan updated.');
    }

    public function destroy(RatePlan $ratePlan) {
        $ratePlan->delete();
        return back()->with('success','Rate Plan deleted.');
    }
}

