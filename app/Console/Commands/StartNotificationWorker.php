<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class StartNotificationWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:worker
                            {--queue=notifications : The queue to listen on}
                            {--daemon : Run the worker in daemon mode}
                            {--sleep=3 : Number of seconds to sleep when no job is available}
                            {--tries=3 : Number of times to attempt a job before logging it failed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a dedicated worker for processing notification jobs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $queue = $this->option('queue');
        $daemon = $this->option('daemon');
        $sleep = $this->option('sleep');
        $tries = $this->option('tries');

        $this->info("Starting notification worker on queue: {$queue}");

        $command = 'queue:work';
        
        $params = [
            '--queue' => $queue,
            '--sleep' => $sleep,
            '--tries' => $tries,
        ];

        if ($daemon) {
            $params['--daemon'] = true;
        }

        $this->info('Executing: php artisan ' . $command . ' ' . implode(' ', array_map(
            function ($value, $key) {
                if (is_bool($value)) {
                    return $value ? "--{$key}" : '';
                }
                return "--{$key}={$value}";
            },
            $params,
            array_keys($params)
        )));

        // Execute the queue:work command
        Artisan::call($command, $params);

        return Command::SUCCESS;
    }
} 