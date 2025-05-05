<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'John Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'plan' => 'Enterprise',
            'status' => 'Active',
        ]);

        // Create editor user
        User::create([
            'name' => 'Jane Editor',
            'email' => 'editor@example.com',
            'password' => Hash::make('password'),
            'role' => 'Editor',
            'plan' => 'Team',
            'status' => 'Active',
        ]);

        // Create author user
        User::create([
            'name' => 'Bob Author',
            'email' => 'author@example.com',
            'password' => Hash::make('password'),
            'role' => 'Author',
            'plan' => 'Basic',
            'status' => 'Active',
        ]);

        // Create subscriber user (inactive)
        User::create([
            'name' => 'Sam Subscriber',
            'email' => 'subscriber@example.com',
            'password' => Hash::make('password'),
            'role' => 'Subscriber',
            'plan' => 'Basic',
            'status' => 'Inactive',
        ]);

        // Create pending user
        User::create([
            'name' => 'Tina Pending',
            'email' => 'pending@example.com',
            'password' => Hash::make('password'),
            'role' => 'Editor',
            'plan' => 'Team',
            'status' => 'Pending',
        ]);
    }
}
