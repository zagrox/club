<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin and regular users directly in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating users...');

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
        
        $this->info('Admin user created: ' . $admin->email);

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
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, ['email_verified_at' => now()])
            );
            $this->info('Regular user created: ' . $user->email);
        }

        $this->info('All users created successfully!');
    }
} 