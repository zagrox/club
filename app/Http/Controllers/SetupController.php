<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SetupController extends Controller
{
    /**
     * Initialize users (admin and regular users)
     *
     * @return \Illuminate\Http\Response
     */
    public function initializeUsers()
    {
        // Check if table exists
        if (!Schema::hasTable('users')) {
            return response()->json([
                'error' => 'Users table does not exist. Run migrations first.'
            ], 500);
        }

        try {
            // Admin user
            $admin = User::updateOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Admin User',
                    'password' => Hash::make('password123'),
                    'role' => 'admin',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
            
            // Regular users
            $regularUsers = [
                [
                    'name' => 'User One',
                    'email' => 'user1@example.com',
                    'password' => Hash::make('user123'),
                    'role' => 'user',
                    'status' => 'active',
                ],
                [
                    'name' => 'User Two',
                    'email' => 'user2@example.com',
                    'password' => Hash::make('user123'),
                    'role' => 'user',
                    'status' => 'active',
                ],
                [
                    'name' => 'User Three',
                    'email' => 'user3@example.com',
                    'password' => Hash::make('user123'),
                    'role' => 'user',
                    'status' => 'active',
                ]
            ];

            foreach ($regularUsers as $userData) {
                User::updateOrCreate(
                    ['email' => $userData['email']],
                    array_merge($userData, ['email_verified_at' => now()])
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Users created successfully',
                'admin' => $admin->email,
                'users' => collect($regularUsers)->pluck('email')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create users: ' . $e->getMessage()
            ], 500);
        }
    }
} 