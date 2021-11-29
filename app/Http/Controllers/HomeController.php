<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $userId = Auth::id();

        $data['repositories'] = DB::table('repositories', 'repos')
            ->leftJoin('platforms as ptf', 'repos.platform', '=', 'ptf.id')
            ->select(['repos.id', 'repos.name', 'ptf.name as platform'])
            ->where('owner', $userId)->get();
        $projects = collect(DB::select(DB::raw('
            with project_info AS
            (SELECT proj.*, repos.name as repository_name
            FROM projects as proj
            LEFT JOIN project_repository as pivot ON proj.id = pivot.project_id
            LEFT JOIN repositories as repos ON pivot.repository_id = repos.id
            WHERE proj.owner = :id
            )

            SELECT distinct id, name, description,
            to_char(created_at, \'Dy DD Mon YYYY\') as created_at, created_at as created_at_original,
            (SELECT jsonb_agg (repo_info) as repositories FROM 
            (
            SELECT repository_name FROM project_info b
            WHERE a.id = b.id
            ) repo_info)
            FROM project_info a
            ORDER BY created_at_original DESC
        '), [
            'id' => $userId,
        ]));

        $projectsProcessed = $projects->map(function ($project) {
            $project->repositories = json_decode($project->repositories);

            return $project;
        });
        $data['projects'] = $projectsProcessed;

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

    public function markNotificationAsRead($notificationId)
    {
        $notification = DB::table('notifications')->where('id', $notificationId)
            ->update([
                'read_at' => now(),
            ])
        ;
        $notificationCount = User::findOrFail(Auth::id())->unreadNotifications->count();

        return response()->json(['message' => 'Notification marked as read.', 'notificationCount' => $notificationCount], 200);
    }
}
