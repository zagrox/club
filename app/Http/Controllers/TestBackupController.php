<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BackupController;

class TestBackupController extends Controller
{
    /**
     * Show a test page with backup information
     */
    public function index()
    {
        $backupController = new BackupController();
        $backups = $backupController->getBackups();
        
        // Dump for debugging
        echo '<pre>Backups found: ' . count($backups) . "\n";
        print_r($backups);
        echo '</pre>';
        
        return view('pages.change-logs', [
            'pageTitle' => 'Test Backups Page',
            'releases' => [],
            'backups' => $backups,
            'showBackupSection' => true
        ]);
    }
} 