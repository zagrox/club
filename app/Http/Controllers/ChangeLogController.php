<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
                'v1.14.0' => [
                    'published_at' => date('Y-m-d'), // Today's date
                    'body' => 'Enhanced notification center and fixed message input field:
- Fixed critical issue with notification message field not accepting text input
- Replaced Quill editor with standard textarea for improved reliability
- Enhanced form validation for notifications to prevent empty submissions
- Improved error handling with fallback alerts if SweetAlert is not available
- Added defensive programming to handle edge cases and prevent JS errors
- Optimized notification preview functionality for better error feedback
- Improved RTL support for Persian text in the notification system
- Enhanced UI with better styling and visual feedback for form interactions',
                ],
                'v1.13.0' => [
                    'published_at' => date('Y-m-d', strtotime('-1 day')), // Yesterday
                    'body' => 'Implemented wallet and payment system:
- Added wallet feature for users to deposit, withdraw, and transfer funds
- Integrated payment gateway with Zibal for secure transactions
- Updated payment processing with controller callbacks
- Improved payment verification process and error handling
- Removed outdated wallet migrations and restructured database schemas
- Added transaction history tracking for wallets
- Enhanced user experience with clear payment status messages
- Fixed security issues with payment callbacks',
                ],
                'v1.12.0' => [
                    'published_at' => date('Y-m-d', strtotime('-2 days')), // 2 days ago
                    'body' => 'Enhanced backup system and improved wallet integration:
- Optimized backup storage by excluding previous backups to prevent recursive growth
- Added dedicated backup management page separate from change logs
- Implemented backup cleanup system with configurable retention policies
- Configured smart exclusions to reduce backup size (logs, caches, vendor directories)
- Added cleanup button to manually manage backup retention
- Added wallet access link to the main navigation sidebar
- Fixed role-based permission issues for admin access
- Added diagnostic commands for role management and troubleshooting',
                ],
                'v1.11.0' => [
                    'published_at' => '2025-05-02',
                    'body' => 'Implemented notification queue system and fixed Sanctum configuration:
- Added ProcessNotification job for handling notifications asynchronously
- Added StartNotificationWorker command for managing queue workers
- Created NotificationServiceProvider for handling queue events
- Fixed Sanctum configuration for proper token abilities
- Added EventServiceProvider and RouteServiceProvider',
                ],
                'v1.10.0' => [
                    'published_at' => '2025-05-01',
                    'body' => 'Implemented Laravel Wallet package integration:
- Added virtual wallet functionality for users
- Created wallet controller with deposit, withdraw, and transfer features
- Added transaction history views
- Implemented wallet balance tracking
- Added secure fund transfer between users',
                ],
                'v1.9.0' => [
                    'published_at' => '2025-04-30',
                    'body' => 'Added backup system and change log tracking:
- Integrated Spatie Laravel Backup package
- Added backup management interface in admin area
- Created change logs page with version history
- Added GitHub integration for tag information',
                ],
                'v1.8.0' => [
                    'published_at' => '2025-04-29',
                    'body' => 'Improved permission management and user roles:
- Enhanced permission matrix interface
- Added bulk permission assignment
- Fixed role hierarchy issues
- Improved permission checking performance',
                ],
                'v1.7.0' => [
                    'published_at' => date('Y-m-d', strtotime('-3 days')), // 3 days ago
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
                }
            }
        } catch (\Exception $e) {
            $tags = [];
        }
        
        // Ensure v1.14.0 is included even if GitHub API fails
        $v1140Found = false;
        foreach ($tags as $tag) {
            if (isset($tag['name']) && $tag['name'] === 'v1.14.0') {
                $v1140Found = true;
                break;
            }
        }
        
        if (!$v1140Found) {
            // Add v1.14.0 manually if not found in GitHub response
            $tags[] = [
                'name' => 'v1.14.0',
                'published_at' => date('Y-m-d'),
                'body' => $tagDetails['v1.14.0']['body'],
                'html_url' => 'https://github.com/zagrox/club/releases/tag/v1.14.0'
            ];
        }

        // Ensure v1.13.0 is included even if GitHub API fails
        $v1130Found = false;
        foreach ($tags as $tag) {
            if (isset($tag['name']) && $tag['name'] === 'v1.13.0') {
                $v1130Found = true;
                break;
            }
        }
        
        if (!$v1130Found) {
            // Add v1.13.0 manually if not found in GitHub response
            $tags[] = [
                'name' => 'v1.13.0',
                'published_at' => date('Y-m-d'),
                'body' => $tagDetails['v1.13.0']['body'],
                'html_url' => 'https://github.com/zagrox/club/releases/tag/v1.13.0'
            ];
        }

        // Sort releases by published_at date (newest first)
        usort($tags, function($a, $b) {
            // First, compare by version number if the names start with 'v' followed by numbers
            if (preg_match('/^v(\d+)\.(\d+)\.(\d+)$/', $a['name'], $matchesA) && 
                preg_match('/^v(\d+)\.(\d+)\.(\d+)$/', $b['name'], $matchesB)) {
                
                // Compare major version
                if ($matchesA[1] != $matchesB[1]) {
                    return (int)$matchesB[1] - (int)$matchesA[1]; // Higher major version first
                }
                
                // Compare minor version
                if ($matchesA[2] != $matchesB[2]) {
                    return (int)$matchesB[2] - (int)$matchesA[2]; // Higher minor version first
                }
                
                // Compare patch version
                return (int)$matchesB[3] - (int)$matchesA[3]; // Higher patch version first
            }
            
            // Fall back to date-based comparison if version parsing fails
            $dateA = isset($a['published_at']) ? strtotime($a['published_at']) : 0;
            $dateB = isset($b['published_at']) ? strtotime($b['published_at']) : 0;
            return $dateB - $dateA;
        });

        return view('pages.change-logs', [
            'pageTitle' => 'Change Logs',
            'releases' => $tags
        ]);
    }
} 