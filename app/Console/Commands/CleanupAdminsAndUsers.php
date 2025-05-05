<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class CleanupAdminsAndUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup-admins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all admin users except admin@example.com and set all users status to Active';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $adminEmail = 'admin@example.com';
        $adminRole = Role::where('slug', 'admin')->first();
        if (!$adminRole) {
            $this->error('Admin role not found.');
            return 1;
        }

        // Remove all admin users except admin@example.com
        $admins = User::whereHas('roles', function($q) use ($adminRole) {
            $q->where('roles.id', $adminRole->id);
        })->where('email', '!=', $adminEmail)->get();

        foreach ($admins as $user) {
            $user->roles()->detach($adminRole->id);
            $user->delete();
            $this->info('Deleted admin user: ' . $user->email);
        }

        // Set all users' status to 'Active'
        User::query()->update(['status' => 'Active']);
        $this->info('All users status set to Active.');
        return 0;
    }
} 