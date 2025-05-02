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
        if (!Schema::hasTable('exchange_logs')) {
            Schema::create('exchange_logs', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->unsignedBigInteger('from_wallet_id');
                $table->unsignedBigInteger('to_wallet_id');
                $table->decimal('from_amount', 64, 0);
                $table->decimal('to_amount', 64, 0);
                $table->decimal('rate', 64, 0);
                $table->timestamps();
                
                $table->foreign('from_wallet_id')
                    ->references('id')
                    ->on('wallets')
                    ->onDelete('cascade');
                
                $table->foreign('to_wallet_id')
                    ->references('id')
                    ->on('wallets')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_logs');
    }
}; 