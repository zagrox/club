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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('amount')->comment('Amount in IRR (Rials)');
            $table->string('gateway')->default('zibal')->comment('Payment gateway');
            $table->string('ref_id')->nullable()->comment('Gateway reference ID');
            $table->string('track_id')->nullable()->comment('Gateway track ID');
            $table->string('order_id')->nullable()->comment('Application order ID');
            $table->string('status')->default('pending')->comment('Payment status: pending, paid, verified, failed, canceled, refunded');
            $table->timestamp('payment_date')->nullable()->comment('When payment was made');
            $table->string('description')->nullable()->comment('Payment description');
            $table->json('metadata')->nullable()->comment('Additional payment data');
            $table->string('card_number', 16)->nullable()->comment('Masked card number');
            $table->string('card_hash')->nullable()->comment('Card hash from gateway');
            $table->timestamps();
            
            // Add indexes
            $table->index('status');
            $table->index('track_id');
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
