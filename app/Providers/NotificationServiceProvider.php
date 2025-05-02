<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Listen for job processing events
        Queue::before(function (JobProcessing $event) {
            if (str_contains($event->job->getQueue(), 'notifications')) {
                Log::info('Processing notification job: ' . $event->job->getJobId());
            }
        });

        // Listen for job processed events
        Queue::after(function (JobProcessed $event) {
            if (str_contains($event->job->getQueue(), 'notifications')) {
                Log::info('Successfully processed notification job: ' . $event->job->getJobId());
            }
        });

        // Listen for job failure events
        Queue::failing(function (JobFailed $event) {
            if (str_contains($event->job->getQueue(), 'notifications')) {
                Log::error('Failed to process notification job: ' . $event->job->getJobId());
                Log::error('Exception: ' . $event->exception->getMessage());
            }
        });
    }
} 