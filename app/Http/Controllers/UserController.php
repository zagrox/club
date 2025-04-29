<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function list(Request $request)
    {
        $query = User::with('roles')
            ->select('id', 'name', 'email', 'role', 'status', 'created_at');
        
        // Apply filters if they exist in the request
        if ($request->has('role') && $request->role) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('role', 'like', "%{$searchTerm}%")
                  ->orWhere('status', 'like', "%{$searchTerm}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        if ($request->ajax()) {
            return response()->json([
                'users' => $users,
                'pagination' => view('vendor.pagination.bootstrap-5', ['paginator' => $users])->render(),
            ]);
        }
        
        $roles = \App\Models\Role::all();
        return view('pages.users.list', compact('users', 'roles'));
    }
    
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'status' => 'required|string|in:Active,Inactive,Pending',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = $request->role;
            $user->status = $request->status;
            $user->save();
            
            // Assign the user to the Admin role if specified
            if ($request->role === 'Admin') {
                $adminRole = Role::where('slug', 'admin')->first();
                if ($adminRole) {
                    $user->roles()->attach($adminRole->id);
                }
            }
            
            DB::commit();
            
            return redirect()->route('users.list')
                ->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Manage a user's profile as admin.
     */
    public function manage(User $user)
    {
        // Pass the user to the account settings view
        return view('pages.settings.account', compact('user'));
    }

    /**
     * Get user details for modal display.
     */
    public function details(User $user)
    {
        // Load user with roles and permissions
        $user->load('roles.permissions');
        
        // Return user details for ajax request
        if (request()->ajax()) {
            // Get permissions via roles safely
            $permissions = $user->getPermissionsViaRoles();
            
            return response()->json([
                'user' => $user,
                'roles' => $user->roles,
                'permissions' => $permissions
            ]);
        }
        
        // Return view for non-ajax request
        return view('pages.users.details', compact('user'));
    }

    /**
     * Update user information.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'status' => 'required|string|in:Active,Inactive,Pending',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        return redirect()->back()
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->forceDelete();
        return redirect()->route('users.list')->with('success', 'User permanently deleted!');
    }
} 