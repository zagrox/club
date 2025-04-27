<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
                'v1.5.0' => [
                    'published_at' => date('Y-m-d'), // Today's date
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

        return view('pages.change-logs', [
            'pageTitle' => 'Change Logs',
            'releases' => $tags
        ]);
    }
} 