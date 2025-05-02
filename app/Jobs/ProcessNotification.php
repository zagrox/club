<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ProcessNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The notification instance.
     *
     * @var \App\Models\Notification
     */
    protected $notification;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff;
    
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
        $this->queue = Config::get('notifications.queue.name', 'notifications');
        $this->tries = Config::get('notifications.queue.tries', 3);
        $this->backoff = Config::get('notifications.queue.backoff', 60);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('Processing notification: ' . $this->notification->id);

        try {
            // Simulate sending notification
            sleep(2); // Simulate processing time
            
            // Mark notification as processed in the database
            // This is just an example - adjust based on your notification model structure
            $this->notification->status = 'sent';
            $this->notification->sent_at = now();
            $this->notification->save();

            Log::info('Notification processed successfully: ' . $this->notification->id);
        } catch (\Exception $e) {
            Log::error('Failed to process notification: ' . $e->getMessage());
            
            // If this is the final retry, mark the notification as failed
            if ($this->attempts() >= $this->tries) {
                $this->notification->status = 'failed';
                $this->notification->save();
                
                Log::error('Notification marked as failed after ' . $this->tries . ' attempts: ' . $this->notification->id);
            }
            
            throw $e; // Re-throw the exception to trigger job failure
        }
    }
    
    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        // This is called when the job fails after all retries
        Log::error('Notification job failed completely: ' . $this->notification->id . ' - ' . $exception->getMessage());
        
        // Update the notification status to failed
        $this->notification->status = 'failed';
        $this->notification->save();
    }
}
