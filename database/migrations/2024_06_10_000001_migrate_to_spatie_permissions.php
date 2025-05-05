<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateToSpatiePermissions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Backup existing data before making changes
        $this->backupExistingData();

        // Step 2: Make sure Spatie's tables exist
        $this->ensureSpatieTablesExist();

        // Step 3: Migrate data from custom tables to Spatie's tables
        $this->migrateDataToSpatieTables();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't implement the down migration as it's complex to revert data
        // The backups created during the up migration can be used for manual restoration if needed
    }

    /**
     * Backup existing permission data before modifying anything
     */
    private function backupExistingData(): void
    {
        // First get the columns in the existing tables to ensure compatibility
        $permissionColumns = [];
        $roleColumns = [];
        
        // Get permission table columns if exists
        if (Schema::hasTable('permissions')) {
            $permissionColumns = Schema::getColumnListing('permissions');
        }
        
        // Get role table columns if exists
        if (Schema::hasTable('roles')) {
            $roleColumns = Schema::getColumnListing('roles');
        }
        
        // Create backup tables with matching columns
        if (!Schema::hasTable('permissions_backup') && !empty($permissionColumns)) {
            Schema::create('permissions_backup', function (Blueprint $table) use ($permissionColumns) {
                $table->id();
                
                // Add all columns from the original table except id
                foreach ($permissionColumns as $column) {
                    if ($column !== 'id') {
                        // We need to determine the column type
                        if ($column === 'created_at' || $column === 'updated_at' || $column === 'deleted_at') {
                            $table->timestamp($column)->nullable();
                        } else if ($column === 'is_default' || $column === 'is_system') {
                            $table->boolean($column)->default(false);
                        } else {
                            $table->string($column)->nullable();
                        }
                    }
                }
            });

            // Copy data using the specific columns
            $columnList = implode(', ', $permissionColumns);
            DB::statement("INSERT INTO permissions_backup ($columnList) SELECT $columnList FROM permissions");
        }

        if (!Schema::hasTable('roles_backup') && !empty($roleColumns)) {
            Schema::create('roles_backup', function (Blueprint $table) use ($roleColumns) {
                $table->id();
                
                // Add all columns from the original table except id
                foreach ($roleColumns as $column) {
                    if ($column !== 'id') {
                        // We need to determine the column type
                        if ($column === 'created_at' || $column === 'updated_at' || $column === 'deleted_at') {
                            $table->timestamp($column)->nullable();
                        } else if ($column === 'is_default' || $column === 'is_system') {
                            $table->boolean($column)->default(false);
                        } else {
                            $table->string($column)->nullable();
                        }
                    }
                }
            });

            // Copy data
            $columnList = implode(', ', $roleColumns);
            DB::statement("INSERT INTO roles_backup ($columnList) SELECT $columnList FROM roles");
        }

        if (!Schema::hasTable('permission_role_backup') && Schema::hasTable('permission_role')) {
            // First get the columns in the permission_role table
            $permissionRoleColumns = Schema::getColumnListing('permission_role');
            
            Schema::create('permission_role_backup', function (Blueprint $table) use ($permissionRoleColumns) {
                // Add all columns from the original table
                foreach ($permissionRoleColumns as $column) {
                    if ($column === 'permission_id') {
                        $table->unsignedBigInteger('permission_id');
                    } else if ($column === 'role_id') {
                        $table->unsignedBigInteger('role_id');
                    } else {
                        // Add any other columns that might exist
                        $table->string($column)->nullable();
                    }
                }
                
                // Set primary key only if all required columns exist
                if (in_array('permission_id', $permissionRoleColumns) && in_array('role_id', $permissionRoleColumns)) {
                    $table->primary(['permission_id', 'role_id']);
                }
            });

            // Copy data with specific column list
            $columnList = implode(', ', $permissionRoleColumns);
            DB::statement("INSERT INTO permission_role_backup ($columnList) SELECT $columnList FROM permission_role");
        }

        if (!Schema::hasTable('model_has_roles_backup') && Schema::hasTable('model_has_roles')) {
            // First get the columns in the model_has_roles table
            $modelHasRolesColumns = Schema::getColumnListing('model_has_roles');
            
            Schema::create('model_has_roles_backup', function (Blueprint $table) use ($modelHasRolesColumns) {
                // Add all columns from the original table
                foreach ($modelHasRolesColumns as $column) {
                    if ($column === 'role_id') {
                        $table->unsignedBigInteger('role_id');
                    } else if ($column === 'model_type') {
                        $table->string('model_type');
                    } else if ($column === 'model_id') {
                        $table->unsignedBigInteger('model_id');
                    } else {
                        // Add any other columns that might exist
                        $table->string($column)->nullable();
                    }
                }
                
                // Set primary key only if all required columns exist
                if (in_array('role_id', $modelHasRolesColumns) && 
                    in_array('model_type', $modelHasRolesColumns) && 
                    in_array('model_id', $modelHasRolesColumns)) {
                    $table->primary(['role_id', 'model_id', 'model_type']);
                }
            });

            // Copy data with specific column list
            $columnList = implode(', ', $modelHasRolesColumns);
            DB::statement("INSERT INTO model_has_roles_backup ($columnList) SELECT $columnList FROM model_has_roles");
        }
    }

    /**
     * Ensure Spatie's permission tables exist
     */
    private function ensureSpatieTablesExist(): void
    {
        // Check if role_has_permissions table exists, if not create it
        if (!Schema::hasTable('role_has_permissions')) {
            Schema::create('role_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');

                $table->primary(['permission_id', 'role_id']);
            });
        }

        // Check if model_has_permissions table exists, if not create it
        if (!Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');

                $table->primary(['permission_id', 'model_id', 'model_type']);
            });
        }

        // Update the permissions table to match Spatie's structure if needed
        if (Schema::hasTable('permissions')) {
            if (!Schema::hasColumn('permissions', 'guard_name')) {
                Schema::table('permissions', function (Blueprint $table) {
                    $table->string('guard_name')->default('web')->after('name');
                });
            }
        }
    }

    /**
     * Migrate data from custom tables to Spatie's tables
     */
    private function migrateDataToSpatieTables(): void
    {
        // Migrate permission_role to role_has_permissions
        if (Schema::hasTable('permission_role') && Schema::hasTable('role_has_permissions')) {
            // Check if there's data to migrate
            $count = DB::table('permission_role')->count();
            
            if ($count > 0) {
                // Clear existing data in the target table to avoid conflicts
                DB::table('role_has_permissions')->truncate();
                
                // Copy data from permission_role to role_has_permissions
                DB::statement('INSERT INTO role_has_permissions (permission_id, role_id) SELECT permission_id, role_id FROM permission_role');
            }
        }

        // Update permissions to ensure they have guard_name
        if (Schema::hasTable('permissions') && Schema::hasColumn('permissions', 'guard_name')) {
            DB::table('permissions')
                ->whereNull('guard_name')
                ->update(['guard_name' => 'web']);
        }
    }
} 