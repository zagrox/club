<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::beginTransaction();
        
        try {
            // 1. Update permissions table
            if (Schema::hasTable('permissions')) {
                if (!Schema::hasColumn('permissions', 'guard_name')) {
                    Schema::table('permissions', function (Blueprint $table) {
                        $table->string('guard_name')->default('web');
                    });
                    
                    // Update all existing permissions to have web guard
                    DB::table('permissions')->update(['guard_name' => 'web']);
                }
                
                // Check for slug column and copy values to name if needed
                if (Schema::hasColumn('permissions', 'slug') && Schema::hasColumn('permissions', 'name')) {
                    // Copy slugs to name field
                    DB::statement('UPDATE permissions SET name = slug WHERE name != slug');
                }
            }
            
            // 2. Update roles table
            if (Schema::hasTable('roles')) {
                if (!Schema::hasColumn('roles', 'guard_name')) {
                    Schema::table('roles', function (Blueprint $table) {
                        $table->string('guard_name')->default('web');
                    });
                    
                    // Update all existing roles to have web guard
                    DB::table('roles')->update(['guard_name' => 'web']);
                }
                
                // Check for slug column and copy values to name if needed
                if (Schema::hasColumn('roles', 'slug') && Schema::hasColumn('roles', 'name')) {
                    // Copy slugs to name field
                    DB::statement('UPDATE roles SET name = slug WHERE name != slug');
                }
            }
            
            // 3. Create model_has_permissions table if it doesn't exist
            if (!Schema::hasTable('model_has_permissions')) {
                Schema::create('model_has_permissions', function (Blueprint $table) {
                    $table->unsignedBigInteger('permission_id');
                    $table->string('model_type');
                    $table->unsignedBigInteger('model_id');
                    
                    $table->foreign('permission_id')
                        ->references('id')
                        ->on('permissions')
                        ->onDelete('cascade');
                        
                    $table->primary(['permission_id', 'model_id', 'model_type']);
                });
            }
            
            // 4. Create model_has_roles table if it doesn't exist
            if (!Schema::hasTable('model_has_roles')) {
                Schema::create('model_has_roles', function (Blueprint $table) {
                    $table->unsignedBigInteger('role_id');
                    $table->string('model_type');
                    $table->unsignedBigInteger('model_id');
                    
                    $table->foreign('role_id')
                        ->references('id')
                        ->on('roles')
                        ->onDelete('cascade');
                        
                    $table->primary(['role_id', 'model_id', 'model_type']);
                });
                
                // If role_user exists, migrate data to model_has_roles
                if (Schema::hasTable('role_user')) {
                    $roleUsers = DB::table('role_user')->get();
                    
                    foreach ($roleUsers as $roleUser) {
                        $userId = isset($roleUser->user_id) ? $roleUser->user_id : $roleUser->model_id;
                        
                        DB::table('model_has_roles')->insert([
                            'role_id' => $roleUser->role_id,
                            'model_id' => $userId,
                            'model_type' => 'App\\Models\\User'
                        ]);
                    }
                }
            }
            
            // 5. Create role_has_permissions if it doesn't exist
            if (!Schema::hasTable('role_has_permissions')) {
                Schema::create('role_has_permissions', function (Blueprint $table) {
                    $table->unsignedBigInteger('permission_id');
                    $table->unsignedBigInteger('role_id');
                    
                    $table->foreign('permission_id')
                        ->references('id')
                        ->on('permissions')
                        ->onDelete('cascade');
                        
                    $table->foreign('role_id')
                        ->references('id')
                        ->on('roles')
                        ->onDelete('cascade');
                        
                    $table->primary(['permission_id', 'role_id']);
                });
                
                // If permission_role exists, migrate data to role_has_permissions
                if (Schema::hasTable('permission_role')) {
                    $permissionRoles = DB::table('permission_role')->get();
                    
                    foreach ($permissionRoles as $permissionRole) {
                        DB::table('role_has_permissions')->insert([
                            'permission_id' => $permissionRole->permission_id,
                            'role_id' => $permissionRole->role_id
                        ]);
                    }
                }
            }
            
            // 6. Mark the official Spatie migration as completed to avoid conflicts
            DB::table('migrations')->insert([
                'migration' => '2025_04_28_215831_create_permission_tables',
                'batch' => DB::table('migrations')->max('batch') + 1,
            ]);
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update permissions for Spatie package: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it modifies tables
        // and adds data. A manual restoration would be needed.
    }
};
