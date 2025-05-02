<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignAdminRole extends Command
{
    protected $signature = 'roles:assign-admin {email}';
    protected $description = 'Assign the admin role to a user by email';

    public function handle()
    {
        $email = $this->argument('email');
        $this->info("Assigning admin role to user with email: {$email}");
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return Command::FAILURE;
        }
        
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$adminRole) {
            $this->info('Admin role does not exist. Creating it now...');
            $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        }
        
        if ($user->hasRole('admin')) {
            $this->info("User {$user->name} already has admin role.");
        } else {
            $user->assignRole('admin');
            $this->info("Admin role assigned to user {$user->name}.");
        }
        
        return Command::SUCCESS;
    }
} 