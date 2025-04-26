<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LayoutController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the fluid layout view.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function fluid()
    {
        return view('pages.layouts.fluid');
    }

    /**
     * Show the container layout view.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function container()
    {
        return view('pages.layouts.container');
    }

    /**
     * Show the without menu layout view.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function withoutMenu()
    {
        return view('pages.layouts.without-menu');
    }

    /**
     * Show the without navbar layout view.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function withoutNavbar()
    {
        return view('pages.layouts.without-navbar');
    }

    /**
     * Show the blank layout view.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function blank()
    {
        return view('pages.layouts.blank');
    }
}
