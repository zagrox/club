<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\TestZibalPayment::class,
        Commands\AssignAdminRole::class,
        Commands\CheckAdminRole::class,
        Commands\CleanupRoleUserRelations::class,
        Commands\CleanupAdminsAndUsers::class,
        Commands\CreateUsers::class,
        Commands\ExtractTranslations::class,
        Commands\CleanupEnvBackups::class,
        Commands\CreateWalletsForUsers::class,
        Commands\PublishFonts::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Clean up env backup files daily, keep last 3
        $schedule->command('env:cleanup --keep=3')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
        
        // Check and create wallets weekly for any users who don't have them
        $this->schedule(new \App\Console\Commands\CreateWalletsForUsers)
            ->weekly();
    }
} 