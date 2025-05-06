<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class MigrateEnvToSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:migrate-env';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate settings from .env file to the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Migrating Zibal settings from .env to database...');
        
        // Get Zibal settings from .env
        $settings = [
            'zibal_merchant' => env('ZIBAL_MERCHANT'),
            'zibal_sandbox' => env('ZIBAL_SANDBOX'),
            'zibal_mock' => env('ZIBAL_MOCK'),
            'zibal_callback_url' => env('ZIBAL_CALLBACK_URL'),
            'zibal_description_prefix' => env('ZIBAL_DESCRIPTION_PREFIX'),
            'zibal_log_enabled' => env('ZIBAL_LOG_ENABLED'),
            'zibal_log_channel' => env('ZIBAL_LOG_CHANNEL'),
        ];
        
        $migrated = 0;
        
        foreach ($settings as $key => $value) {
            if ($value !== null) {
                Setting::set($key, $value, 'zibal');
                $this->info("Migrated {$key} with value: " . (strlen($value) > 30 ? substr($value, 0, 30) . '...' : $value));
                $migrated++;
            }
        }
        
        Setting::clearGroupCache('zibal');
        
        if ($migrated > 0) {
            $this->info("Successfully migrated {$migrated} settings from .env to database.");
        } else {
            $this->warn("No Zibal settings found in .env file to migrate.");
        }
        
        return 0;
    }
} 