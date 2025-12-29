<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth',);
        $this->middleware('web',);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
}
