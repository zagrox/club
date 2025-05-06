<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupEnvBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:cleanup {--keep=3 : Number of recent backups to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old .env backup files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $keepCount = (int) $this->option('keep');
        $envFile = app()->environmentFilePath();
        $pattern = $envFile . '.backup-*';
        
        $this->info("Searching for .env backup files...");
        $backupFiles = glob($pattern);
        
        if (empty($backupFiles)) {
            $this->info("No backup files found.");
            return 0;
        }
        
        $this->info("Found " . count($backupFiles) . " backup files.");
        
        // Sort by filename (which includes timestamp) in descending order
        usort($backupFiles, function($a, $b) {
            return strcmp($b, $a); // Reverse comparison to get descending order
        });
        
        // Keep the first $keepCount files, delete the rest
        if (count($backupFiles) > $keepCount) {
            $toDelete = array_slice($backupFiles, $keepCount);
            $this->info("Deleting " . count($toDelete) . " old backup files...");
            
            $bar = $this->output->createProgressBar(count($toDelete));
            $bar->start();
            
            foreach ($toDelete as $oldBackup) {
                // Skip special backups like the zibal test backup
                if (strpos($oldBackup, '.zibal-test-backup') !== false) {
                    $this->comment("Skipping special backup file: " . basename($oldBackup));
                    continue;
                }
                
                if (@unlink($oldBackup)) {
                    $bar->advance();
                } else {
                    $this->error("Failed to delete: " . $oldBackup);
                }
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("Cleanup completed. Kept the " . $keepCount . " most recent backups.");
        } else {
            $this->info("No files to delete. Already have " . count($backupFiles) . " files, which is less than or equal to the keep count (" . $keepCount . ").");
        }
        
        return 0;
    }
} 