<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::with('roles')->get();
        
        return response()->json([
            'users' => $users
        ]);
    }

    /**
     * Display the specified user
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        $user->load('roles');
        $permissions = $user->getAllPermissions()->pluck('name');
        
        return response()->json([
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'permissions' => $permissions
        ]);
    }
} 