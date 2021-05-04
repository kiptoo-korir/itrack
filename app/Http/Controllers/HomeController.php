<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['user_data'] = Auth::user();
        $data['user_data']->first_letter = substr($data['user_data']->name, 0, 1);

        return view('home')->with($data);
    }
}
