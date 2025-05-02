<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, convert enum to varchar to allow more status values
        DB::statement("ALTER TABLE notifications MODIFY status VARCHAR(20) NOT NULL DEFAULT 'pending'");
        
        // Update existing statuses to match our new scheme
        DB::statement("
            UPDATE notifications 
            SET status = 
                CASE 
                    WHEN status = 'draft' THEN 'draft'
                    WHEN status = 'sent' THEN 'sent'
                    WHEN status = 'scheduled' THEN 'scheduled'
                    WHEN status = 'archived' THEN 'archived'
                    ELSE 'pending'
                END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to the original enum type
        DB::statement("ALTER TABLE notifications MODIFY status ENUM('draft', 'scheduled', 'sent', 'archived') NOT NULL DEFAULT 'draft'");
    }
};
