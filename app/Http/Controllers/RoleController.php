<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        // Use DB::raw to get accurate user count, excluding deleted users
        $roles = Role::withCount(['users' => function ($query) {
            $query->whereNotNull('users.id');
        }])->get();
        
        return view('pages.roles.index', compact('roles'));
    }
    
    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = $this->getAvailablePermissions();
        
        return view('pages.roles.create', compact('permissions'));
    }
    
    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web',
                'description' => $request->description,
                'is_default' => false,
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();
            activity()->log("Created role {$role->name}");
            return redirect()->route('users.roles.index')->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating role: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        // Load only users that exist in the database
        $role->load(['users' => function ($query) {
            $query->whereNotNull('users.id')->orderBy('name');
        }]);
        
        // Get accurate count using DB query
        $userCount = DB::table('model_has_roles')
            ->where('role_id', $role->id)
            ->where('model_type', 'App\\Models\\User')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereRaw('users.id = model_has_roles.model_id');
            })
            ->count();
        
        return view('pages.roles.show', compact('role', 'userCount'));
    }
    
    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = $this->getAvailablePermissions();
        
        return view('pages.roles.edit', compact('role', 'permissions'));
    }
    
    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
        ]);

        if ($role->isSystem()) {
            return back()->with('error', 'System roles cannot be modified.');
        }

        DB::beginTransaction();
        try {
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();
            activity()->log("Updated role {$role->name}");
            return redirect()->route('users.roles.index')->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating role: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return redirect()->back()
                ->with('error', 'System roles cannot be deleted.');
        }
        
        DB::beginTransaction();
        
        try {
            // Log the role deletion
            Log::info('Role deleted', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'deleted_by' => auth()->id(),
            ]);
            
            $role->delete();
            
            DB::commit();
            
            return redirect()->route('users.roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }
    
    /**
     * Assign users to a role.
     */
    public function assignUsers(Request $request, Role $role)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);
        
        DB::beginTransaction();
        
        try {
            $role->users()->syncWithoutDetaching($request->user_ids);
            
            // Log the user assignment
            Log::info('Users assigned to role', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'user_ids' => $request->user_ids,
                'assigned_by' => auth()->id(),
            ]);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Users assigned to role successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to assign users to role: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove a user from a role.
     */
    public function removeUser(Request $request, Role $role, User $user)
    {
        DB::beginTransaction();
        
        try {
            $role->users()->detach($user->id);
            
            // Log the user removal
            Log::info('User removed from role', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'removed_by' => auth()->id(),
            ]);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'User removed from role successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to remove user from role: ' . $e->getMessage());
        }
    }
    
    /**
     * Get available permissions for roles.
     */
    private function getAvailablePermissions()
    {
        return [
            'Users' => [
                'users.view' => 'View Users',
                'users.create' => 'Create Users',
                'users.edit' => 'Edit Users',
                'users.delete' => 'Delete Users',
            ],
            'Roles' => [
                'roles.view' => 'View Roles',
                'roles.create' => 'Create Roles',
                'roles.edit' => 'Edit Roles',
                'roles.delete' => 'Delete Roles',
            ],
            'Settings' => [
                'settings.access' => 'Access Settings',
            ],
            'Notifications' => [
                'notifications.manage' => 'Manage Notifications',
            ],
            'Logs' => [
                'logs.view' => 'View Logs',
            ],
        ];
    }
} 