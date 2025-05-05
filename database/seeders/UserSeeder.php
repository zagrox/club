<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('slug', 'admin')->first();
        $editorRole = Role::where('slug', 'editor')->first();
        $userRole = Role::where('slug', 'user')->first();

        // Create Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin', // Keep for backward compatibility
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        
        // Assign admin role
        if ($adminRole) {
            $adminUser->assignRole($adminRole);
        }

        // Create Editor User
        $editorUser = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor User',
                'password' => Hash::make('password123'),
                'role' => 'editor',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        
        // Assign editor role
        if ($editorRole) {
            $editorUser->assignRole($editorRole);
        }

        // Create Regular Users
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
        ];

        foreach ($regularUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, ['email_verified_at' => now()])
            );
            
            // Assign user role
            if ($userRole) {
                $user->assignRole($userRole);
            }
        }
    }
} 