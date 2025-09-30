<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CancellationPolicy;

class CancellationPolicyController extends Controller 
{
    public function index()
    {
        $policies = CancellationPolicy::all(); return view('admin.cancellation_policies.index', compact('policies')); 
    }
    public function create()
    {
        return view('admin.cancellation_policies.create'); 
    }
    public function store(Request $r)
    {
        $r->validate(['name'=>'required|string|max:255']);
        CancellationPolicy::create($r->all());
        return redirect()->route('cancellation-policies.index')->with('success','Cancellation Policy created.');
    }
    public function edit(CancellationPolicy $policy)
    {
        return view('admin.cancellation_policies.edit', compact('policy')); 
    }
    public function update(Request $r, CancellationPolicy $policy)
    {
        $r->validate(['name'=>'required|string|max:255']);
        $policy->update($r->all());
        return redirect()->route('cancellation-policies.index')->with('success','Cancellation Policy updated.');
    }
    public function destroy(CancellationPolicy $policy)
    {
        $policy->delete(); return redirect()->route('cancellation-policies.index')->with('success','Cancellation Policy deleted.'); 
    }
}

