<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('backup_notes', function (Blueprint $table) {
            $table->id();
            $table->string('filename')->index();
            $table->string('disk')->default('backup');
            $table->string('path')->nullable();
            $table->string('title')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            
            // Combined unique index to prevent duplicate entries for the same file
            $table->unique(['filename', 'disk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_notes');
    }
};
