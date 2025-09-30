<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CancellationPolicyRule;
use App\Models\CancellationPolicy;

class CancellationPolicyRuleController extends Controller 
{
    public function index($policyId)
    {
        $policy = CancellationPolicy::with('rules')->findOrFail($policyId);
        return view('admin.cancellation_policy_rules.index', compact('policy'));
    }

    public function create($policyId)
    {
        $policy = CancellationPolicy::findOrFail($policyId);
        return view('admin.cancellation_policy_rules.create', compact('policy'));
    }

    public function store(Request $r, $policyId)
    {
        $r->validate([
            'days_from'=>'required|integer|min:0',
            'days_to'=>'required|integer|min:0',
            'penalty_percent'=>'required|integer|min:0|max:100',
            'refundable'=>'nullable|boolean'
        ]);
        CancellationPolicyRule::create(array_merge($r->all(), ['cancellation_policy_id'=>$policyId]));
        return redirect()->route('cancellation-policy-rules.index', $policyId)->with('success','Rule added.');
    }

    public function edit($policyId, CancellationPolicyRule $rule)
    {
        $policy = CancellationPolicy::findOrFail($policyId);
        return view('admin.cancellation_policy_rules.edit', compact('policy','rule'));
    }

    public function update(Request $r, $policyId, CancellationPolicyRule $rule)
    {
        $r->validate([
            'days_from'=>'required|integer|min:0',
            'days_to'=>'required|integer|min:0',
            'penalty_percent'=>'required|integer|min:0|max:100',
            'refundable'=>'nullable|boolean'
        ]);
        $rule->update($r->all());
        return redirect()->route('cancellation-policy-rules.index', $policyId)->with('success','Rule updated.');
    }

    public function destroy($policyId, CancellationPolicyRule $rule)
    {
        $rule->delete();
        return redirect()->route('cancellation-policy-rules.index', $policyId)->with('success','Rule deleted.');
    }
}

