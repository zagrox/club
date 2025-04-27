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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->enum('category', ['system', 'update', 'reminder', 'custom'])->default('system');
            $table->enum('status', ['draft', 'scheduled', 'sent', 'archived'])->default('draft');
            $table->string('audience')->default('all'); // all, roles, users
            $table->json('recipients')->nullable(); // ids of roles or users
            $table->json('delivery_methods')->nullable(); // push, email, sms, web
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
        
        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_reads');
        Schema::dropIfExists('notifications');
    }
}; 