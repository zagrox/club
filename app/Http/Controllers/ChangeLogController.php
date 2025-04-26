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
        // Fetch releases from GitHub API (can be replaced with actual repository)
        try {
            $response = Http::get('https://api.github.com/repos/laravel/laravel/releases');
            $releases = $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            $releases = [];
        }

        return view('pages.change-logs', [
            'pageTitle' => 'Change Logs',
            'releases' => $releases
        ]);
    }
} 