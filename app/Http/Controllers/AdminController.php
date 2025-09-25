<?php

namespace App\Http\Controllers;
use App\Models\Trip;
use App\Models\Booking;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Guest;
use App\Models\Agent;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{

    public function dashboard()
    {
        $agentsCount = Agent::count();
        $tripsCount = Trip::count();
        return view('dashboard', compact('agentsCount','tripsCount'));
    }

    public function index()
    {
         $users = User::all();
          $roles = Role::all();
           $companies = Company::all();
        return view('admin.users.manage_users', compact('users','roles','companies'));
    }

    public function create()
    {
         $roles = Role::all();
         $companies = Company::all();
        return view('admin.users.create',compact('roles','companies'));
    }
    public function store(Request $request)
    {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'company_id'      => $request->company_id,
        ]);

        $role = Role::findOrFail($request->role);
        $user->assignRole($role->name);
        

        return redirect()->route('users.create')->with('success', 'User created successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'company_id' => $request->company_id, // ✅ save company_id
            'password'   => $request->filled('password') 
                                ? Hash::make($request->password) 
                                : $user->password,
        ]);

        $role = Role::findOrFail($request->role);
        $user->syncRoles([$role->name]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

}
