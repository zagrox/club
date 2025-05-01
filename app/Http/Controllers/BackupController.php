<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Get the list of backup files
     *
     * @return array
     */
    public function getBackups()
    {
        $backups = [];
        $disk = Storage::disk('local');
        
        if ($disk->exists('laravel-backup')) {
            $files = $disk->files('laravel-backup');
            
            foreach ($files as $file) {
                if (substr($file, -4) === '.zip') {
                    $backups[] = [
                        'file_name' => str_replace('laravel-backup/', '', $file),
                        'file_size' => $this->formatFileSize($disk->size($file)),
                        'last_modified' => Carbon::createFromTimestamp($disk->lastModified($file))->diffForHumans(),
                        'date' => Carbon::createFromTimestamp($disk->lastModified($file)),
                        'file_path' => $file,
                    ];
                }
            }
        }
        
        // Sort backups by last modified (newest first)
        usort($backups, function($a, $b) {
            return $b['date']->timestamp - $a['date']->timestamp;
        });
        
        return $backups;
    }
    
    /**
     * Format file size to human readable format
     *
     * @param int $size
     * @return string
     */
    private function formatFileSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        
        while ($size > 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }
    
    /**
     * Start a new backup
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startBackup()
    {
        try {
            // Run the backup command
            $output = Artisan::call('backup:run');
            \Log::info('Backup command output: ' . Artisan::output());
            
            return redirect()->back()->with('success', 'Backup process has been started successfully.');
        } catch (\Exception $e) {
            \Log::error('Backup failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a backup
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteBackup(Request $request)
    {
        try {
            $filePath = $request->input('file_path');
            if (Storage::disk('local')->exists($filePath)) {
                Storage::disk('local')->delete($filePath);
                return redirect()->back()->with('success', 'Backup deleted successfully.');
            }
            return redirect()->back()->with('error', 'Backup file not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }
    
    /**
     * Download a backup
     *
     * @param string $fileName
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadBackup($fileName)
    {
        $filePath = 'laravel-backup/' . $fileName;
        if (Storage::disk('local')->exists($filePath)) {
            return Storage::disk('local')->download($filePath);
        }
        
        return redirect()->back()->with('error', 'Backup file not found.');
    }
} 