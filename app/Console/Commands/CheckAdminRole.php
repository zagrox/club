<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CheckAdminRole extends Command
{
    protected $signature = 'roles:check-admin';
    protected $description = 'Check if the admin role exists and create it if it does not';

    public function handle()
    {
        $this->info('Checking for admin role...');
        
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            $this->info('Admin role exists with ID: ' . $adminRole->id);
        } else {
            $this->info('Admin role does not exist. Creating it now...');
            $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
            $this->info('Admin role created with ID: ' . $adminRole->id);
        }
        
        return Command::SUCCESS;
    }
} 