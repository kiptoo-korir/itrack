<?php

namespace App\Http\Controllers;

use App\Services\UserDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $data['repositories'] = DB::table('repositories', 'repos')
            ->leftJoin('platforms as ptf', 'repos.platform', '=', 'ptf.id')
            ->select(['repos.id', 'repos.name', 'ptf.name as platform'])
            ->where('owner', $data['user_data']->id)->get();
        $data['notification_count'] = UserDataService::fetch_notifications_count();

        return view('home')->with($data);
    }

    public function get_top_three_notifications()
    {
        $user_id = Auth::id();
        $notifications = DB::table('notifications')
            ->select('id', 'data', 'created_at')
            ->where(['notifiable_id' => $user_id, 'notifiable_type' => 'App\Models\User'])
            ->whereNull('read_at')
            ->latest()
            ->limit(3)
            ->get()
        ;

        return response()->json(['notifications' => $notifications], 200);
    }
}
