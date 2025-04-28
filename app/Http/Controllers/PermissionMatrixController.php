<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionMatrixController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('can:manage-permissions'); // Uncomment if you have a permission check
    }

    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('pages.permissions.matrix', compact('roles', 'permissions'));
    }

    public function update(Request $request)
    {
        $role = Role::findOrFail($request->role_id);
        $permission = Permission::findOrFail($request->permission_id);
        $action = $request->action;

        if ($action === 'attach') {
            $role->givePermissionTo($permission);
        } elseif ($action === 'detach') {
            $role->revokePermissionTo($permission);
        }

        return response()->json(['success' => true]);
    }
} 