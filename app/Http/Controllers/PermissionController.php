<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();
        
        // Group permissions by their prefix (e.g., users.view -> users)
        $groupedPermissions = $permissions->groupBy(function($permission) {
            $parts = explode('.', $permission->slug);
            return $parts[0] ?? 'other';
        });
        
        return view('pages.permissions.index', compact('groupedPermissions'));
    }
    
    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        // Get existing permission groups for dropdown
        $groups = Permission::select(DB::raw('DISTINCT SUBSTRING_INDEX(slug, ".", 1) as group_name'))
            ->orderBy('group_name')
            ->pluck('group_name');
            
        return view('pages.permissions.create', compact('groups'));
    }
    
    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'group' => 'required|string|max:50',
            'action' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate the slug from group and action
            $slug = $request->group . '.' . Str::slug($request->action);
            
            $permission = Permission::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
            ]);

            DB::commit();
            activity()->log("Created permission {$permission->name}");
            return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating permission: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        
        return view('pages.permissions.show', compact('permission'));
    }
    
    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        // Get existing permission groups for dropdown
        $groups = Permission::select(DB::raw('DISTINCT SUBSTRING_INDEX(slug, ".", 1) as group_name'))
            ->orderBy('group_name')
            ->pluck('group_name');
            
        // Split the slug to get group and action
        $slugParts = explode('.', $permission->slug);
        $group = $slugParts[0];
        $action = count($slugParts) > 1 ? $slugParts[1] : '';
        
        return view('pages.permissions.edit', compact('permission', 'groups', 'group', 'action'));
    }
    
    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'group' => 'required|string|max:50',
            'action' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate the slug from group and action
            $slug = $request->group . '.' . Str::slug($request->action);
            
            $permission->update([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
            ]);

            DB::commit();
            activity()->log("Updated permission {$permission->name}");
            return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating permission: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is used by any roles
        if ($permission->roles()->count() > 0) {
            return redirect()->back()
                ->with('error', 'This permission cannot be deleted because it is assigned to one or more roles.');
        }
        
        DB::beginTransaction();
        
        try {
            // Log before deletion to capture permission details
            Log::info('Permission deleted', [
                'permission_id' => $permission->id,
                'permission_name' => $permission->name,
                'permission_slug' => $permission->slug,
                'deleted_by' => auth()->id(),
            ]);
            
            $permission->delete();
            
            DB::commit();
            
            return redirect()->route('permissions.index')
                ->with('success', 'Permission deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to delete permission: ' . $e->getMessage());
        }
    }
} 