<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionMatrixController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('can:manage-permissions'); // Uncomment if you have a permission check
    }

    public function index()
    {
        try {
            Log::info('Matrix controller accessed');
            
            $roles = Role::all();
            $permissions = Permission::all();
            
            // Log for debugging
            Log::info('Matrix loading with: ' . $roles->count() . ' roles and ' . $permissions->count() . ' permissions');
            
            // More detailed logging
            Log::info('Roles loaded', ['roles' => $roles->pluck('name', 'id')->toArray()]);
            Log::info('Permissions loaded', ['permissions' => $permissions->pluck('name', 'id')->toArray()]);
            
            return view('pages.permissions.matrix', compact('roles', 'permissions'));
        } catch (\Exception $e) {
            Log::error('Error loading permission matrix: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Error loading permission matrix: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            Log::info('Matrix update accessed', $request->all());
            
            $role = Role::findOrFail($request->role_id);
            $permission = Permission::findOrFail($request->permission_id);
            $action = $request->action;
            
            Log::info('Permission matrix update', [
                'role' => $role->name,
                'permission' => $permission->name,
                'action' => $action
            ]);

            if ($action === 'attach') {
                DB::table('permission_role')->updateOrInsert(
                    ['permission_id' => $permission->id, 'role_id' => $role->id],
                    ['permission_id' => $permission->id, 'role_id' => $role->id]
                );
                Log::info('Permission attached to role');
            } elseif ($action === 'detach') {
                DB::table('permission_role')->where('permission_id', $permission->id)
                    ->where('role_id', $role->id)
                    ->delete();
                Log::info('Permission detached from role');
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error updating permission: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 