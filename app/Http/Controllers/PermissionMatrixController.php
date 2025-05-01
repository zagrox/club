<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PermissionMatrixController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Instead of using the permission middleware, we'll check directly in the methods
    }

    public function index()
    {
        try {
            // Check if user has permission
            if (!Auth::user()->hasRole('admin') && !Auth::user()->hasPermissionTo('permissions.manage')) {
                return redirect()->route('home')->with('error', 'You do not have permission to access this page.');
            }
            
            Log::info('Matrix controller accessed');
            
            $roles = Role::all();
            $permissions = Permission::all();
            
            // Log for debugging
            Log::info('Matrix loading with: ' . $roles->count() . ' roles and ' . $permissions->count() . ' permissions');
            
            return view('pages.permissions.matrix', compact('roles', 'permissions'));
        } catch (\Exception $e) {
            Log::error('Error loading permission matrix: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('home')->with('error', 'Error loading permission matrix: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            // Check if user has permission
            if (!Auth::user()->hasRole('admin') && !Auth::user()->hasPermissionTo('permissions.manage')) {
                return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
            }
            
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
                $role->givePermissionTo($permission);
                Log::info('Permission attached to role');
            } elseif ($action === 'detach') {
                $role->revokePermissionTo($permission);
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