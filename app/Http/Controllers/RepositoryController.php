<?php

namespace App\Http\Controllers;

use App\Jobs\FetchIssuesInRepoQueue;
use App\Jobs\FetchLanguagesInRepoQueue;
use App\Jobs\FetchRepositories;
use App\Models\Repository;
use App\Models\RepositoryLanguage;
use App\Services\UserDataService;
use Illuminate\Support\Facades\Auth;

class RepositoryController extends Controller
{
    public function repositories_view()
    {
        $data['user_data'] = Auth::user();
        $data['user_data']->first_letter = substr($data['user_data']->name, 0, 1);

        $user_id = Auth::id();
        // TODO: Validate whether token is valid before dispatching job
        FetchRepositories::dispatch($user_id);
        $data['repositories'] = Repository::select(['id', 'name', 'description', 'issues_count', 'date_updated_online', 'date_created_online'])->get();
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

        $repositoryFullname = $data['repository']->fullname;
        $repoId = $data['repository']->id;
        $userId = $data['user_data']->id;

        FetchLanguagesInRepoQueue::dispatch($repositoryFullname, $userId, $repoId)
            // ->delay(now()->addSeconds(5))
        ;
        FetchIssuesInRepoQueue::dispatch($repoId, $repositoryFullname, $userId);

        return view('specific_repository')->with($data);
    }
}
