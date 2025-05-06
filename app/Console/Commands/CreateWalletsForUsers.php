<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CreateWalletsForUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:create-for-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create wallets for all users who don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating wallets for users...');
        
        $count = 0;
        $errors = 0;
        
        User::chunk(100, function ($users) use (&$count, &$errors) {
            foreach ($users as $user) {
                try {
                    if (!$user->hasWallet()) {
                        $user->createWallet([
                            'name' => 'Default Wallet',
                            'slug' => 'default',
                            'description' => 'Default user wallet',
                        ]);
                        
                        $count++;
                        $this->info("Created wallet for user {$user->id}");
                    }
                } catch (\Exception $e) {
                    $errors++;
                    Log::error("Failed to create wallet for user {$user->id}", [
                        'error' => $e->getMessage()
                    ]);
                    $this->error("Failed to create wallet for user {$user->id}: {$e->getMessage()}");
                }
            }
        });
        
        $this->info("Completed! Created {$count} wallets with {$errors} errors.");
        
        return Command::SUCCESS;
    }
} 