<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateTestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin users
        $admins = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'System Admin',
                'email' => 'sysadmin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Create manager users
        $managers = [
            [
                'name' => 'Manager One',
                'email' => 'manager1@example.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager Two',
                'email' => 'manager2@example.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Editor One',
                'email' => 'editor1@example.com',
                'password' => Hash::make('password'),
                'role' => 'editor',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Create member users
        $members = [
            [
                'name' => 'Member One',
                'email' => 'member1@example.com',
                'password' => Hash::make('password'),
                'role' => 'member',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Member Two',
                'email' => 'member2@example.com',
                'password' => Hash::make('password'),
                'role' => 'member',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Member Three',
                'email' => 'member3@example.com',
                'password' => Hash::make('password'),
                'role' => 'member',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'User One',
                'email' => 'user1@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Author One',
                'email' => 'author1@example.com',
                'password' => Hash::make('password'),
                'role' => 'author',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Insert all users
        foreach ($admins as $admin) {
            DB::table('users')->updateOrInsert(
                ['email' => $admin['email']],
                $admin
            );
        }
        
        foreach ($managers as $manager) {
            DB::table('users')->updateOrInsert(
                ['email' => $manager['email']],
                $manager
            );
        }
        
        foreach ($members as $member) {
            DB::table('users')->updateOrInsert(
                ['email' => $member['email']],
                $member
            );
        }
        
        $this->command->info('Created ' . count($admins) . ' admin users');
        $this->command->info('Created ' . count($managers) . ' manager users');
        $this->command->info('Created ' . count($members) . ' member users');
    }
} 