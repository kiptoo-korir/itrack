<?php

namespace App\Http\Controllers;

use App\Jobs\FetchIssuesInRepoQueue;
use App\Jobs\FetchLanguagesInRepoQueue;
use App\Jobs\FetchRepositories;
use App\Models\Issue;
use App\Models\Repository;
use App\Models\RepositoryLanguage;
use App\Services\UserDataService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class RepositoryController extends Controller
{
    public function repositories_view()
    {
        $data['user_data'] = Auth::user();
        $data['user_data']->first_letter = substr($data['user_data']->name, 0, 1);

        $user_id = Auth::id();
        // TODO: Validate whether token is valid before dispatching job
        FetchRepositories::dispatch($user_id);
        $data['repositories'] = Repository::select(['id', 'name', 'description', 'issues_count', 'date_updated_online', 'date_created_online'])
            ->selectRaw('to_char(date_updated_online, \'Dy Mon DD YYYY\') as date_updated_online, to_char(date_created_online, \'Dy Mon DD YYYY\') as date_created_online')
            ->get()
        ;
        $data['notification_count'] = UserDataService::fetch_notifications_count();

        return view('repositories')->with($data);
    }

    public function specific_repository($id)
    {
        $data['user_data'] = Auth::user();
        $data['user_data']->first_letter = substr($data['user_data']->name, 0, 1);
        $data['repository'] = Repository::findOrFail($id);
        $data['notification_count'] = UserDataService::fetch_notifications_count();
        $data['languages'] = RepositoryLanguage::where('repository_id', $id)
            ->pluck('value', 'name')
        ;
        $data['issuesUrl'] = route('fetch_issues_in_repo', $id);

        $repositoryFullname = $data['repository']->fullname;
        $repoId = $data['repository']->id;
        $userId = $data['user_data']->id;

        FetchLanguagesInRepoQueue::dispatch($repositoryFullname, $userId, $repoId)
            // ->delay(now()->addSeconds(5))
        ;
        FetchIssuesInRepoQueue::dispatch($repoId, $repositoryFullname, $userId);

        return view('specific_repository')->with($data);
    }

    public function fetch_issues_in_repository($repositoryId)
    {
        $issues = Issue::where('repository', $repositoryId)->get();

        return DataTables::of($issues)
            ->addColumn('labels', function ($issueData) {
                return json_decode($issueData->labels);
            })
            ->rawColumns(['labels'])
            ->make(true)
        ;
    }

    public function getRepositoriesSpecificProject($projectId)
    {
        $userId = Auth::id();
        $repositories = Repository::leftJoin('project_repository as project_repo', 'repositories.id', '=', 'project_repo.repository_id')
            ->select('repositories.*')
            ->where(['project_repo.owner' => $userId, 'project_repo.project_id' => $projectId])
            ->get()
        ;

        return response()->json(['repositories' => $repositories], 200);
    }
}
