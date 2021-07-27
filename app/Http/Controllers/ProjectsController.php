<?php

namespace App\Http\Controllers;

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
        $insert_arr = [
            'name' => $request->name,
            'description' => $request->description,
            'owner' => $user_id,
            'repository' => $request->repository,
        ];

        if ($project = DB::table('projects')->insert($insert_arr)) {
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
}
