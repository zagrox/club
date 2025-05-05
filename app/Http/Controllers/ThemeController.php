<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ThemeController extends Controller
{
    /**
     * Display theme settings page
     */
    public function settings()
    {
        return view('pages.theme-settings');
    }

    /**
     * Update theme mode preference (light/dark/system)
     */
    public function updateMode(Request $request)
    {
        $mode = $request->input('mode');
        
        if (in_array($mode, ['light', 'dark', 'system'])) {
            Cookie::queue('theme-mode', $mode, 60 * 24 * 365); // 1 year
            return response()->json(['success' => true, 'mode' => $mode]);
        }
        
        return response()->json(['success' => false, 'message' => 'Invalid mode'], 400);
    }
    
    /**
     * Toggle RTL mode
     */
    public function toggleRtl(Request $request)
    {
        $isRtl = $request->input('is_rtl') === 'true';
        
        Cookie::queue('theme-rtl', $isRtl ? 'true' : 'false', 60 * 24 * 365); // 1 year
        
        return response()->json(['success' => true, 'is_rtl' => $isRtl]);
    }
    
    /**
     * Toggle menu collapsed state
     */
    public function toggleMenuCollapsed(Request $request)
    {
        $isCollapsed = $request->input('is_collapsed') === 'true';
        
        Cookie::queue('theme-menu-collapsed', $isCollapsed ? 'true' : 'false', 60 * 24 * 365); // 1 year
        
        return response()->json(['success' => true, 'is_collapsed' => $isCollapsed]);
    }
} 