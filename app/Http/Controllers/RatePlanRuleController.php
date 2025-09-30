<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RatePlanRule;
use App\Models\RatePlan;

class RatePlanRuleController extends Controller 
{
    public function index($ratePlanId)
    {
        $ratePlan = RatePlan::with('rules')->findOrFail($ratePlanId);
        return view('admin.rate_plan_rules.index', compact('ratePlan'));
    }

    public function create($ratePlanId)
    {
        $ratePlan = RatePlan::findOrFail($ratePlanId);
        return view('admin.rate_plan_rules.create', compact('ratePlan'));
    }

    public function store(Request $r, $ratePlanId)
    {
        $r->validate([
            'room_id'=>'nullable|integer',
            'base_price'=>'required|numeric|min:0',
            'extra_bed_price'=>'nullable|numeric|min:0'
        ]);
        RatePlanRule::create(array_merge($r->all(), ['rate_plan_id'=>$ratePlanId]));
        return redirect()->route('rate-plan-rules.index', $ratePlanId)->with('success','Rule added.');
    }

    public function edit($ratePlanId, RatePlanRule $rule)
    {
        $ratePlan = RatePlan::findOrFail($ratePlanId);
        return view('admin.rate_plan_rules.edit', compact('ratePlan','rule'));
    }

    public function update(Request $r, $ratePlanId, RatePlanRule $rule)
    {
        $r->validate([
            'room_id'=>'nullable|integer',
            'base_price'=>'required|numeric|min:0',
            'extra_bed_price'=>'nullable|numeric|min:0'
        ]);
        $rule->update($r->all());
        return redirect()->route('rate-plan-rules.index', $ratePlanId)->with('success','Rule updated.');
    }

    public function destroy($ratePlanId, RatePlanRule $rule)
    {
        $rule->delete();
        return redirect()->route('rate-plan-rules.index', $ratePlanId)->with('success','Rule deleted.');
    }
}
