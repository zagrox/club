<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Carbon\Carbon;
use App\Models\BackupNote;

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
    public function index(Request $request)
    {
        // Get the custom backup path from the request or use the default path
        $backupPath = $request->session()->get('custom_backup_path', '/Applications/MAMP/htdocs/backups/club');
        
        $backups = $this->getBackups($backupPath);
        
        return view('pages.backup-management', [
            'pageTitle' => 'Backup Management',
            'backups' => $backups,
            'backupPath' => $backupPath
        ]);
    }
    
    /**
     * Update the custom backup path
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBackupPath(Request $request)
    {
        $request->validate([
            'backup_path' => 'required|string'
        ]);
        
        $backupPath = $request->input('backup_path');
        
        // Check if the directory exists, create it if it doesn't
        if (!File::exists($backupPath)) {
            try {
                File::makeDirectory($backupPath, 0775, true);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to create backup directory: ' . $e->getMessage());
            }
        }
        
        // Check if the directory is writable
        if (!is_writable($backupPath)) {
            return redirect()->back()->with('error', 'The backup directory is not writable. Please check permissions.');
        }
        
        // Update the filesystems configuration
        config(['filesystems.disks.backup.root' => $backupPath]);
        
        // Save the path in session
        $request->session()->put('custom_backup_path', $backupPath);
        
        return redirect()->route('backup.index')->with('success', 'Backup path updated successfully to: ' . $backupPath);
    }
    
    /**
     * Get the list of backup files
     *
     * @param string $backupPath
     * @return array
     */
    public function getBackups($backupPath = null)
    {
        $backups = [];
        
        // Use the provided backup path or the default one
        $backupPath = $backupPath ?: '/Applications/MAMP/htdocs/backups/club';
        
        // Update the backup disk configuration dynamically
        config(['filesystems.disks.backup.root' => $backupPath]);
        
        // Get backup files from the backup disk
        $backupDisk = Storage::disk('backup');
        
        // Debug information for backup disk
        \Log::info('BackupController checking backup disk', [
            'diskExists' => true,
            'backupPath' => $backupPath,
            'files' => $backupDisk->files('/'),
        ]);
        
        // Get all zip files from the root of the backup disk
        $files = $backupDisk->files('/');
        
        foreach ($files as $file) {
            if (substr($file, -4) === '.zip') {
                $fileName = basename($file);
                
                // Get notes for this backup file
                $note = BackupNote::findByFilename($fileName);
                
                $backups[] = [
                    'file_name' => $fileName,
                    'file_size' => $this->formatFileSize($backupDisk->size($file)),
                    'last_modified' => Carbon::createFromTimestamp($backupDisk->lastModified($file))->diffForHumans(),
                    'date' => Carbon::createFromTimestamp($backupDisk->lastModified($file)),
                    'file_path' => $file,
                    'disk' => 'backup',
                    'note_title' => $note ? $note->title : null,
                    'note' => $note ? $note->note : null,
                    'has_note' => $note !== null
                ];
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
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startBackup(Request $request)
    {
        try {
            // Get the custom backup path from the session
            $backupPath = $request->session()->get('custom_backup_path', '/Applications/MAMP/htdocs/backups/club');
            
            // Update the backup disk configuration dynamically
            config(['filesystems.disks.backup.root' => $backupPath]);
            
            // Get optional note and title
            $title = $request->input('backup_title');
            $note = $request->input('backup_note');
            
            // Run the backup command
            $output = Artisan::call('backup:run');
            \Log::info('Backup command output: ' . Artisan::output());
            
            // Try to extract the filename from the output
            $filename = $this->extractFilenameFromOutput(Artisan::output());
            
            // If a title or note was provided, save it
            if (($title || $note) && $filename) {
                BackupNote::updateOrCreate(
                    ['filename' => $filename, 'disk' => 'backup'],
                    [
                        'path' => $backupPath,
                        'title' => $title,
                        'note' => $note
                    ]
                );
            }
            
            return redirect()->back()->with('success', 'Backup process has been completed successfully.');
        } catch (\Exception $e) {
            \Log::error('Backup failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Extract the filename from the backup command output
     *
     * @param string $output
     * @return string|null
     */
    private function extractFilenameFromOutput($output)
    {
        // Try to find a pattern like "2025-05-05-10-21-31.zip" in the output
        preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}\.zip/', $output, $matches);
        
        return $matches[0] ?? null;
    }
    
    /**
     * Update notes for a backup file
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateNote(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'title' => 'nullable|string|max:255',
            'note' => 'nullable|string'
        ]);
        
        $filename = $request->input('filename');
        $title = $request->input('title');
        $note = $request->input('note');
        $disk = $request->input('disk', 'backup');
        
        // Get the custom backup path from the session
        $backupPath = $request->session()->get('custom_backup_path', '/Applications/MAMP/htdocs/backups/club');
        
        // Update or create the note
        BackupNote::updateOrCreate(
            ['filename' => $filename, 'disk' => $disk],
            [
                'path' => $backupPath,
                'title' => $title,
                'note' => $note
            ]
        );
        
        return redirect()->back()->with('success', 'Backup notes updated successfully.');
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
            $diskName = $request->input('disk', 'backup');
            $fileName = basename($filePath);
            
            // Get the custom backup path from the session
            $backupPath = $request->session()->get('custom_backup_path', '/Applications/MAMP/htdocs/backups/club');
            
            // Update the backup disk configuration dynamically
            config(['filesystems.disks.backup.root' => $backupPath]);
            
            if (Storage::disk($diskName)->exists($filePath)) {
                // Delete the file
                Storage::disk($diskName)->delete($filePath);
                
                // Also delete any associated notes
                BackupNote::where('filename', $fileName)
                    ->where('disk', $diskName)
                    ->delete();
                
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
     * @param Request $request
     * @param string $fileName
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadBackup(Request $request, $fileName)
    {
        // Get the custom backup path from the session
        $backupPath = $request->session()->get('custom_backup_path', '/Applications/MAMP/htdocs/backups/club');
        
        // Update the backup disk configuration dynamically
        config(['filesystems.disks.backup.root' => $backupPath]);
        
        // Check if the file exists in the backup disk
        $backupDisk = Storage::disk('backup');
        if ($backupDisk->exists($fileName)) {
            return $backupDisk->download($fileName);
        }
        
        return redirect()->back()->with('error', 'Backup file not found.');
    }
    
    /**
     * Run the backup cleanup task
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cleanupBackups(Request $request)
    {
        try {
            // Get the custom backup path from the session
            $backupPath = $request->session()->get('custom_backup_path', '/Applications/MAMP/htdocs/backups/club');
            
            // Update the backup disk configuration dynamically
            config(['filesystems.disks.backup.root' => $backupPath]);
            
            // Run the backup cleanup command
            $output = Artisan::call('backup:clean');
            \Log::info('Backup cleanup command output: ' . Artisan::output());
            
            return redirect()->back()->with('success', 'Backup cleanup completed successfully.');
        } catch (\Exception $e) {
            \Log::error('Backup cleanup failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Backup cleanup failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Clean up the old backups in storage/app/private
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cleanupOldBackups(Request $request)
    {
        try {
            $disk = Storage::disk('local');
            $oldBackupZips = [];
            
            // Get all zip files in private directory (including root level)
            if ($disk->exists('private')) {
                // Find all zip files in the root of private directory
                foreach ($disk->files('private') as $file) {
                    if (substr($file, -4) === '.zip') {
                        $oldBackupZips[] = $file;
                    }
                }
                
                // Find all directories in private
                $directories = $disk->directories('private');
                
                // Check each directory for zip files
                foreach ($directories as $dir) {
                    if ($disk->exists($dir)) {
                        foreach ($disk->files($dir) as $file) {
                            if (substr($file, -4) === '.zip') {
                                $oldBackupZips[] = $file;
                            }
                        }
                    }
                }
            }
            
            // Delete all found backup zip files
            $deletedCount = 0;
            foreach ($oldBackupZips as $file) {
                $disk->delete($file);
                $deletedCount++;
            }
            
            \Log::info('Cleaned up old backups', [
                'deletedCount' => $deletedCount,
                'files' => $oldBackupZips
            ]);
            
            return redirect()->back()->with('success', 'Cleaned up ' . $deletedCount . ' old backup files from storage/app/private.');
        } catch (\Exception $e) {
            \Log::error('Failed to clean up old backups: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to clean up old backups: ' . $e->getMessage());
        }
    }
} 