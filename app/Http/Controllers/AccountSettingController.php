<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountSettingController extends Controller
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
     * Show the account settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function account()
    {
        return view('pages.account-settings.account');
    }

    /**
     * Show the notifications settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function notifications()
    {
        return view('pages.account-settings.notifications');
    }

    /**
     * Show the connections settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function connections()
    {
        return view('pages.account-settings.connections');
    }
}
