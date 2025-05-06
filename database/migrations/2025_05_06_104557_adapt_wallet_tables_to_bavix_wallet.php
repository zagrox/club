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
        // Check if wallets table exists
        if (!Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
                $table->id();
                $table->morphs('holder');
                $table->string('name');
                $table->string('slug')->index();
                $table->uuid('uuid')->unique();
                $table->decimal('balance', 64, 0)->default(0);
                $table->unsignedSmallInteger('decimal_places')->default(2);
                $table->string('description')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
                
                $table->unique(['holder_type', 'holder_id', 'slug']);
            });
            
            // Import existing wallet data if available
            if (Schema::hasTable('wallets_old')) {
                DB::statement('INSERT INTO wallets (holder_id, holder_type, name, slug, uuid, balance, decimal_places, description, meta, created_at, updated_at) 
                    SELECT holder_id, holder_type, name, slug, uuid, balance * 100, decimal_places, description, meta, created_at, updated_at FROM wallets_old');
            }
        } else {
            // If wallets table already exists, add any missing columns to match bavix structure
            Schema::table('wallets', function (Blueprint $table) {
                if (!Schema::hasColumn('wallets', 'uuid')) {
                    $table->uuid('uuid')->unique()->after('slug');
                }
                
                if (!Schema::hasColumn('wallets', 'decimal_places')) {
                    $table->unsignedSmallInteger('decimal_places')->default(2)->after('balance');
                }
                
                if (!Schema::hasColumn('wallets', 'meta')) {
                    $table->json('meta')->nullable()->after('description');
                }
            });
        }
        
        // Check if transactions table exists (for bavix wallet)
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->morphs('payable');
                $table->unsignedBigInteger('wallet_id')->index();
                $table->enum('type', ['deposit', 'withdraw'])->index();
                $table->decimal('amount', 64, 0);
                $table->boolean('confirmed');
                $table->json('meta')->nullable();
                $table->uuid('uuid')->unique();
                $table->timestamps();
                
                $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            });
            
            // Import existing wallet transaction data if available
            if (Schema::hasTable('wallet_transactions')) {
                // Map existing transaction types to bavix types
                DB::table('wallet_transactions')->orderBy('id')->each(function ($transaction) {
                    $type = $transaction->type;
                    $amount = $transaction->amount;
                    
                    // Convert to bavix transaction type
                    $bavixType = ($type === 'deposit' || $amount > 0) ? 'deposit' : 'withdraw';
                    
                    // For withdraw transactions, ensure amount is positive in bavix (amount is stored as absolute value)
                    $bavixAmount = abs($amount) * 100; // Multiply by 100 to convert to cents/lowest denomination
                    
                    // Create a bavix transaction record
                    DB::table('transactions')->insert([
                        'payable_type' => 'App\\Models\\User',
                        'payable_id' => DB::table('wallets')->where('id', $transaction->wallet_id)->value('holder_id') ?? 0,
                        'wallet_id' => $transaction->wallet_id,
                        'type' => $bavixType,
                        'amount' => $bavixAmount,
                        'confirmed' => true,
                        'meta' => json_encode([
                            'original_transaction_id' => $transaction->id,
                            'description' => $transaction->description,
                            'old_metadata' => $transaction->metadata,
                            'payment_id' => $transaction->payment_id
                        ]),
                        'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->updated_at
                    ]);
                });
            }
        }
        
        // Check if transfers table exists
        if (!Schema::hasTable('transfers')) {
            Schema::create('transfers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('deposit_id');
                $table->unsignedBigInteger('withdraw_id');
                $table->enum('status', ['exchange', 'transfer', 'paid', 'refund', 'gift'])->default('transfer');
                $table->json('meta')->nullable();
                $table->uuid('uuid')->unique();
                $table->timestamps();
                
                $table->foreign('deposit_id')->references('id')->on('transactions')->onDelete('cascade');
                $table->foreign('withdraw_id')->references('id')->on('transactions')->onDelete('cascade');
            });
        }
        
        // Rename existing wallet tables to keep them as backup
        if (Schema::hasTable('wallet_transactions') && !Schema::hasTable('wallet_transactions_old')) {
            Schema::rename('wallet_transactions', 'wallet_transactions_old');
        }
        
        if (Schema::hasTable('wallets') && Schema::hasTable('wallets_old')) {
            // Wallets table is already created by bavix, no need to rename
            // Just keep the backup made earlier
        } else if (Schema::hasTable('wallets') && !Schema::hasTable('wallets_old')) {
            // Make a backup of the wallets table
            DB::statement('CREATE TABLE wallets_old AS SELECT * FROM wallets');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore original wallet tables if backups exist
        if (Schema::hasTable('wallet_transactions_old') && !Schema::hasTable('wallet_transactions')) {
            Schema::rename('wallet_transactions_old', 'wallet_transactions');
        }
        
        if (Schema::hasTable('wallets_old') && !Schema::hasTable('wallets')) {
            Schema::rename('wallets_old', 'wallets');
        }
        
        // Drop bavix wallet tables
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('transactions');
        
        // If we didn't rename the wallets table, drop it
        if (Schema::hasTable('wallets') && Schema::hasTable('wallets_old')) {
            Schema::dropIfExists('wallets');
            Schema::rename('wallets_old', 'wallets');
        }
    }
};
