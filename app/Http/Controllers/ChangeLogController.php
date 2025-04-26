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
        // Fetch releases from GitHub API for the zagrox/club repository
        try {
            $response = Http::get('https://api.github.com/repos/zagrox/club/releases');
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