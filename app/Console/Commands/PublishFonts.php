<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishFonts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fonts:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish font files from resources to public directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Publishing fonts...');
        
        // Check if the source font exists
        $sourceFile = resource_path('fonts/zagrox.ttf');
        if (!File::exists($sourceFile)) {
            $this->error('Source font file not found at: ' . $sourceFile);
            return Command::FAILURE;
        }
        
        // Create target directory if it doesn't exist
        $targetDir = public_path('fonts');
        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
            $this->info('Created fonts directory in public path');
        }
        
        // Copy the TTF font
        $targetFile = $targetDir . '/zagrox.ttf';
        try {
            File::copy($sourceFile, $targetFile);
            $this->info('Font published successfully to: ' . $targetFile);
            
            // Set proper permissions
            chmod($targetFile, 0644);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to publish font file: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 