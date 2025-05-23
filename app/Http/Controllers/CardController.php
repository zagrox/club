<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CardController extends Controller
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
     * Show the basic cards page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function basic()
    {
        return view('pages.cards.basic');
    }
}
