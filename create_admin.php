<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Check if admin user already exists
$admin = User::where('email', 'admin@admin.com')->first();

if (!$admin) {
    // Create new admin user
    $admin = new User();
    $admin->name = 'Admin';
    $admin->email = 'admin@admin.com';
    $admin->password = Hash::make('password');
    $admin->save();
    
    echo "Admin user created successfully!\n";
    echo "Email: admin@admin.com\n";
    echo "Password: password\n";
} else {
    echo "Admin user already exists!\n";
    echo "Email: admin@admin.com\n";
} 