<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Project;
use App\Models\Reminder;
use App\Services\ProjectRepositoryLinkingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

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

            $linkedRepositories = $this->fetchLinkedRepositoriesNames($repos);

            $this->logCreateProject($project);

            return response()->json(['success' => 'Project created successfully.', 'project' => $project, 'linkedRepositories' => $linkedRepositories], 200);
        }

        return response()->json(['error' => 'An error seems to have occurred, please try again.'], 400);
    }

    public function remove_project(Request $request)
    {
        $request->validate([
            'projectId' => 'required|exists:projects,id',
        ]);

        if (DB::table('projects')->where('id', $request->projectId)->delete()) {
            DB::table('project_repository')->where('project_id', $request->projectId)->delete();
            DB::table('notes')->where('project', $request->projectId)->delete();
            DB::table('reminders')->where('project', $request->projectId)->delete();

            return response()->json(['success' => 'Project has been removed successfully.'], 200);
        }

        return response()->json(['error' => 'An error seems to have occurred, please try again.'], 400);
    }

    public function specificProject($projectId)
    {
        $userId = Auth::id();
        $project = Project::findOrFail($projectId);

        if ($userId !== $project->owner) {
            abort(403);
        }

        $data['projectInfo'] = $project;
        $data['notes'] = Note::where('project', $projectId)->get();
        $data['reminder'] = Reminder::where('project', $projectId)->get();
        $data['repositories'] = DB::table('repositories', 'repos')
            ->leftJoin('platforms as ptf', 'repos.platform', '=', 'ptf.id')
            ->select(['repos.id', 'repos.name', 'ptf.name as platform'])
            ->where('owner', $userId)->get();

        return view('project')->with($data);
    }

    public function getLinkedRepositories($projectId)
    {
        $linkedRepositories = $this->fetchLinkedRepositories($projectId);

        return response()->json(['linkedRepos' => $linkedRepositories], 200);
    }

    public function changeLinkedRepositories(Request $request)
    {
        $request->validate([
            'projectId' => 'required|integer|exists:projects,id',
            'repositories' => 'required|array',
        ]);

        $projectId = $request->projectId;
        $repositoriesList = $request->repositories;

        $repositoriesLinked = DB::table('project_repository')
            ->where('project_id', $projectId)
            ->pluck('repository_id')
            ->toArray()
        ;

        $newRepositories = array_diff($repositoriesList, $repositoriesLinked);
        $repositoriesToUnlink = array_diff($repositoriesLinked, $repositoriesList);

        $linkingService = new ProjectRepositoryLinkingService();
        $linkingResult = $linkingService->linkNewRepositories($newRepositories, $projectId);
        $unlinkingResult = $linkingService->unlinkRepositories($repositoriesToUnlink, $projectId);

        if (!$linkingResult || !$unlinkingResult) {
            return response()->json(['message' => 'Something seems to have gone wrong, please contact admin and try again.'], 400);
        }

        return response()->json(['message' => 'Changes made successfully.'], 200);
    }

    public function getLinkedReminders($projectId)
    {
        $user_id = Auth::id();
        // Timezone set manually
        $data = Reminder::where(['owner' => $user_id, 'project' => $projectId])
            ->select('id', 'title', 'message', 'project', 'created_at as c_at', 'due_date as d_d')
            ->selectRaw('to_char(due_date, \'Dy DD Mon, YYYY at HH:MI AM \') as due_date')
            ->selectRaw('to_char(created_at at time zone \'Africa/Nairobi\', \'Dy DD Mon, YYYY at HH:MI AM\') as created')
            ->orderByDesc('c_at')->get();

        return DataTables::of($data)
            ->addColumn('order_created', function ($row) {
                return strtotime($row->c_at);
            })
            ->addColumn('order_due', function ($row) {
                return strtotime($row->d_d);
            })
            ->rawColumns(['order_date', 'order_created'])
            ->make(true)
        ;
    }

    public function editProject($projectId)
    {
        $project = Project::findOrFail($projectId);
        $linkedRepositories = $this->fetchLinkedRepositories($projectId);

        return response()->json(['project' => $project, 'linkedRepositories' => $linkedRepositories], 200);
    }

    public function updateProject(Request $request)
    {
        $request->validate([
            'projectId' => 'required|integer|exists:projects,id',
        ]);

        $project = Project::findOrFail($request->projectId);
        $project->name = $request->name;
        $project->description = $request->description;

        if ($project->save()) {
            $repositoriesList = $request->repositories;

            $repositoriesLinked = $this->fetchLinkedRepositories($request->projectId);

            $newRepositories = array_diff($repositoriesList, $repositoriesLinked);
            $repositoriesToUnlink = array_diff($repositoriesLinked, $repositoriesList);

            $linkingService = new ProjectRepositoryLinkingService();
            $linkingResult = $linkingService->linkNewRepositories($newRepositories, $request->projectId);
            $unlinkingResult = $linkingService->unlinkRepositories($repositoriesToUnlink, $request->projectId);

            if (!$linkingResult || !$unlinkingResult) {
                return response()->json(['message' => 'Something seems to have gone wrong, please contact admin and try again.'], 400);
            }

            $linkedRepoNames = $this->fetchLinkedRepositoriesNames($repositoriesList);

            return response()->json(['message' => 'Project updated successfully.', 'linkedRepoNames' => $linkedRepoNames], 200);
        }
    }

    protected function fetchLinkedRepositories(int $projectId): array
    {
        return DB::table('project_repository')
            ->where('project_id', $projectId)
            ->pluck('repository_id')
            ->toArray()
        ;
    }

    protected function fetchLinkedRepositoriesNames(array $repositoryIds): array
    {
        return DB::table('repositories')
            ->whereIn('id', $repositoryIds)
            ->pluck('name')
            ->toArray()
        ;
    }

    private function logCreateProject(Project $project)
    {
        $user = Auth::user();
        activity('create-project')
            ->causedBy($user)
            ->performedOn($project)
            ->withProperties([
                'action' => 'Successful',
                'project' => $project,
            ])
            ->log("project - {$project->name} created")
        ;
    }
}
