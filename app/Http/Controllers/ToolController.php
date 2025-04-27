<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ToolController extends Controller
{
    /**
     * Display the tools page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('pages.tools', [
            'pageTitle' => 'Tools'
        ]);
    }
} 