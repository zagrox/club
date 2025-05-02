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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            
            // Detailed logging for debugging
            \Log::info('Backup access attempted', [
                'user_id' => $user ? $user->id : 'unauthenticated',
                'user_email' => $user ? $user->email : 'none',
                'has_admin_role' => $user ? $user->hasRole('admin') : false,
                'roles' => $user ? $user->getRoleNames() : [],
            ]);
            
            if (!$user || !$user->hasRole('admin')) {
                \Log::warning('Unauthorized backup access attempt', [
                    'user_id' => $user ? $user->id : 'unauthenticated',
                    'user_email' => $user ? $user->email : 'none',
                ]);
                abort(403, 'Unauthorized. Admin access required.');
            }
            
            return $next($request);
        });
    }
    
    /**
     * Show the backup management page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $backups = $this->getBackups();
        
        return view('pages.backup-management', [
            'pageTitle' => 'Backup Management',
            'backups' => $backups
        ]);
    }
    
    /**
     * Get the list of backup files
     *
     * @return array
     */
    public function getBackups()
    {
        $backups = [];
        $disk = Storage::disk('local');
        $backupPath = 'mailzila';
        
        // Debug information
        \Log::info('BackupController getBackups called', [
            'backupPath' => $backupPath,
            'pathExists' => $disk->exists($backupPath),
            'files' => $disk->exists($backupPath) ? $disk->files($backupPath) : [],
            'directories' => $disk->exists($backupPath) ? $disk->directories($backupPath) : [],
            'allContents' => $disk->exists($backupPath) ? $disk->allFiles($backupPath) : [],
        ]);
        
        if ($disk->exists($backupPath)) {
            $files = $disk->files($backupPath);
            
            foreach ($files as $file) {
                if (substr($file, -4) === '.zip') {
                    $backups[] = [
                        'file_name' => basename($file),
                        'file_size' => $this->formatFileSize($disk->size($file)),
                        'last_modified' => Carbon::createFromTimestamp($disk->lastModified($file))->diffForHumans(),
                        'date' => Carbon::createFromTimestamp($disk->lastModified($file)),
                        'file_path' => $file,
                    ];
                }
            }
        }
        
        // Debug information
        \Log::info('BackupController getBackups results', [
            'backupCount' => count($backups),
            'backups' => $backups,
        ]);
        
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
        $filePath = 'mailzila/' . $fileName;
        if (Storage::disk('local')->exists($filePath)) {
            return Storage::disk('local')->download($filePath);
        }
        
        return redirect()->back()->with('error', 'Backup file not found.');
    }
    
    /**
     * Run the backup cleanup task
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cleanupBackups()
    {
        try {
            // Run the backup cleanup command
            $output = Artisan::call('backup:clean');
            \Log::info('Backup cleanup command output: ' . Artisan::output());
            
            return redirect()->back()->with('success', 'Backup cleanup completed successfully.');
        } catch (\Exception $e) {
            \Log::error('Backup cleanup failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Backup cleanup failed: ' . $e->getMessage());
        }
    }
} 