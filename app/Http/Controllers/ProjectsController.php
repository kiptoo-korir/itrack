<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Project;
use App\Models\Reminder;
use App\Services\UserDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectsController extends Controller
{
    public function create_project(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $user_id = Auth::id();
        $project_arr = [
            'name' => $request->name,
            'description' => $request->description,
            'owner' => $user_id,
        ];

        if ($project = Project::create($project_arr)) {
            $repos = $request->repositories;
            $insert_arr = [];
            foreach ($repos as $repo) {
                array_push($insert_arr, [
                    'project_id' => $project->id,
                    'repository_id' => $repo,
                    'owner' => $user_id,
                ]);
            }
            DB::table('project_repository')->insert($insert_arr);

            return response()->json(['success' => 'Project created successfully.', 'project' => $project], 200);
        }

        return response()->json(['error' => 'An error seems to have occurred, please try again.'], 400);
    }

    public function remove_project(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
        ]);

        if (DB::table('projects')->where('id', $request->project_id)->delete()) {
            return response()->json(['success' => 'Project has been removed successfully.'], 200);
        }

        return response()->json(['error' => 'An error seems to have occurred, please try again.'], 400);
    }

    public function specificProject($projectId)
    {
        $data['user_data'] = Auth::user();
        $data['user_data']->first_letter = substr($data['user_data']->name, 0, 1);
        $data['projectInfo'] = Project::findOrFail($projectId);
        $data['notification_count'] = UserDataService::fetch_notifications_count();
        $data['notes'] = Note::where('project', $projectId)->get();
        $data['reminder'] = Reminder::where('project', $projectId)->get();

        return view('project')->with($data);
    }
}
