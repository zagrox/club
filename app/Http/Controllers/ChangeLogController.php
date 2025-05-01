<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Auth;

class ChangeLogController extends Controller
{
    /**
     * Show the change logs page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Fetch tags from GitHub API for the zagrox/club repository
        try {
            $response = Http::get('https://api.github.com/repos/zagrox/club/tags');
            $tags = $response->successful() ? $response->json() : [];
            
            // Add hardcoded tag descriptions since GitHub API doesn't provide messages with tags
            $tagDetails = [
                'v1.10.0' => [
                    'published_at' => date('Y-m-d'), // Today's date
                    'body' => 'Implemented API Authentication with Laravel Sanctum:
- Added Laravel Sanctum package for token-based API authentication
- Created API controllers for authentication, user management, and token management
- Implemented API routes with proper middleware and permission checks
- Added ability to create, list, and delete API tokens
- Created comprehensive API documentation page at /api-docs
- Added support for token abilities (scopes) for fine-grained access control
- Integrated Spatie permissions with API endpoints for role-based access
- Updated navigation menu with API Resources section',
                ],
                'v1.9.0' => [
                    'published_at' => '2025-05-01', // Previous date
                    'body' => 'Improved Spatie Permission System Integration with Matrix UI:
- Enhanced permission matrix UI with checkmark toggles for each role assignment
- Fixed permissions display in the matrix page
- Added ability to assign permissions to roles through the matrix interface
- Improved user role assignments with proper database relationships
- Added visual indicators for permission status in the matrix view
- Fixed user count display on roles page to accurately show assigned users',
                ],
                'v1.8.0' => [
                    'published_at' => '2025-04-30', // Previous date
                    'body' => 'Added backup system with Spatie Laravel-backup package:
- Integrated Spatie Laravel-backup package for robust backup functionality
- Created BackupController for managing backups through a user interface
- Added UI for creating, downloading, and deleting backups
- Implemented backup directory monitoring and logging
- Added backup routes with authentication protection
- Restricted backup access to admin users only
- Enhanced user experience with intuitive backup management',
                ],
                'v1.7.0' => [
                    'published_at' => '2025-04-29', // Previous date
                    'body' => 'Integrated Spatie role-permission package and fixed permissions system:
- Migrated to Spatie Laravel-permission package for robust RBAC functionality
- Fixed permission matrix interface to properly manage role-permission relationships
- Added direct /matrix route for improved accessibility of permission management
- Fixed 404 errors in permissions management interface
- Updated permission and role models to work with both custom and Spatie systems
- Improved permission assignment UI with better error handling and feedback',
                ],
                'v1.6.0' => [
                    'published_at' => '2025-04-28',
                    'body' => 'Added Role and Permission Management System:
- Created Role and Permission models with many-to-many relationship
- Added UI for managing roles, permissions, and their assignments
- Implemented permission-based access control throughout the application
- Added permission matrix for easy permission management',
                ],
                'v1.5.0' => [
                    'published_at' => '2025-04-27',
                    'body' => 'Added user data protection and bulk user creation tools:
- Modified user table migration to prevent data loss during checkpoint restoration
- Added UserSeeder for creating bulk users through database seeding
- Created a custom Artisan command (users:create) for command-line user creation
- Added a SetupController with web access for initializing users',
                ],
                'v1.4.0' => [
                    'published_at' => '2025-04-27',
                    'body' => 'Added notification center:
- Created comprehensive notification system with multiple delivery methods
- Added support for scheduling notifications
- Implemented notification management UI with drafts, sent, and archived views
- Added support for audience targeting by user, role, or all users',
                ],
                'v1.3.0' => [
                    'published_at' => '2025-04-26',
                    'body' => 'Added dark mode toggle support',
                ],
                'v1.2.0' => [
                    'published_at' => '2025-04-26',
                    'body' => 'Updated password reset pages with Sneat template styling',
                ],
                'v1.1.0' => [
                    'published_at' => '2025-04-26',
                    'body' => 'Updated registration page with Sneat template styling',
                ],
                'v1.0.0' => [
                    'published_at' => '2025-04-26',
                    'body' => 'First release with Laravel project setup and admin user',
                ],
            ];
            
            // Enhance tags data with details
            foreach ($tags as $key => $tag) {
                if (isset($tagDetails[$tag['name']])) {
                    $tags[$key]['published_at'] = $tagDetails[$tag['name']]['published_at'];
                    $tags[$key]['body'] = $tagDetails[$tag['name']]['body'];
                    $tags[$key]['html_url'] = "https://github.com/zagrox/club/releases/tag/{$tag['name']}";
                } else {
                    // Add default details for tags that don't have hardcoded details
                    $tags[$key]['published_at'] = date('Y-m-d');
                    $tags[$key]['body'] = 'New version release with improvements and bug fixes.';
                    $tags[$key]['html_url'] = "https://github.com/zagrox/club/releases/tag/{$tag['name']}";
                }
            }
        } catch (\Exception $e) {
            $tags = [];
        }

        // Get backups data only for admin users
        $backups = [];
        $showBackupSection = false;
        
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            $backupController = new BackupController();
            $backups = $backupController->getBackups();
            $showBackupSection = true;
        }

        return view('pages.change-logs', [
            'pageTitle' => 'Change Logs',
            'releases' => $tags,
            'backups' => $backups,
            'showBackupSection' => $showBackupSection
        ]);
    }
} 