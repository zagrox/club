<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        return view('pages.settings.account');
    }

    /**
     * Show the security settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function security()
    {
        return view('pages.settings.security');
    }

    /**
     * Handle the change password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('settings.security')
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()
            ->route('settings.security')
            ->with('success', 'Password changed successfully!');
    }

    /**
     * Show the notifications settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function notifications()
    {
        return view('pages.settings.notifications');
    }

    /**
     * Show the connections settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function connections()
    {
        return view('pages.settings.connections');
    }
}
