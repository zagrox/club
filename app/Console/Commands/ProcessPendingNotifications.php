<?php

namespace App\Console\Commands;

use App\Jobs\ProcessNotification;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessPendingNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:process {--count=10 : Number of notifications to process at once}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending notifications by dispatching them to the queue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        
        $this->info("Looking for up to {$count} pending notifications...");
        
        // Get pending notifications
        $pendingNotifications = Notification::where('status', 'pending')
            ->whereNull('sent_at')
            ->limit($count)
            ->get();
            
        $total = $pendingNotifications->count();
        
        if ($total === 0) {
            $this->info('No pending notifications found.');
            return Command::SUCCESS;
        }
        
        $this->info("Found {$total} pending notifications. Dispatching jobs...");
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        foreach ($pendingNotifications as $notification) {
            try {
                // Dispatch the job to the queue
                ProcessNotification::dispatch($notification);
                
                // Update notification status to processing
                $notification->status = 'processing';
                $notification->save();
                
                $bar->advance();
            } catch (\Exception $e) {
                Log::error('Failed to dispatch notification job: ' . $e->getMessage());
                $this->error("Error dispatching notification #{$notification->id}: {$e->getMessage()}");
            }
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Successfully dispatched {$total} notification jobs to the queue.");
        
        return Command::SUCCESS;
    }
}
