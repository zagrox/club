<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupRoleUserRelations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:cleanup {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up orphaned role-user relationships in the model_has_roles table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of orphaned role-user relationships...');
        
        // Get all role-user relationships where the user doesn't exist
        $orphanedRelations = DB::table('model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereRaw('users.id = model_has_roles.model_id');
            })
            ->get();
        
        $count = $orphanedRelations->count();
        
        if ($count === 0) {
            $this->info('No orphaned role-user relationships found.');
            return 0;
        }
        
        $this->info("Found {$count} orphaned role-user relationships.");
        
        // Confirm deletion unless --force is specified
        if (!$this->option('force') && !$this->confirm('Do you want to proceed with deleting these orphaned relationships?')) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        // Delete the orphaned relationships
        foreach ($orphanedRelations as $relation) {
            DB::table('model_has_roles')
                ->where('role_id', $relation->role_id)
                ->where('model_id', $relation->model_id)
                ->where('model_type', $relation->model_type)
                ->delete();
            
            $this->line("Deleted: Role ID {$relation->role_id} - User ID {$relation->model_id}");
        }
        
        $this->info("Successfully cleaned up {$count} orphaned role-user relationships.");
        
        // Log the cleanup
        Log::info("Cleaned up {$count} orphaned role-user relationships.");
        
        return 0;
    }
}
