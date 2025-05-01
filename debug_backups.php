<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check if Mailzila directory exists
$disk = Storage::disk('local');
var_dump($disk->exists('Mailzila'));

// List files in Mailzila directory
$files = $disk->files('Mailzila');
var_dump($files);

// Test BackupController directly
$controller = new App\Http\Controllers\BackupController();
$backups = $controller->getBackups();
print_r($backups); 