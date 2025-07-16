<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('roles_permissions.index', compact('roles', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.permissions.index')->with('success', 'Permissions updated successfully.');
    }
}
