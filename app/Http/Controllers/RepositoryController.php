<?php

namespace App\Http\Controllers;

use App\Jobs\FetchRepositories;
use App\Jobs\FetchRepositoryIssues;
use App\Models\Repository;
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
        $data['repositories'] = Repository::select(['name', 'description', 'issues_count', 'date_updated_online', 'date_created_online'])->get();
        $data['notification_count'] = UserDataService::fetch_notifications_count();

        return view('repositories')->with($data);
    }

    public function specific_repository($id)
    {
        $data['user_data'] = Auth::user();
        $data['user_data']->first_letter = substr($data['user_data']->name, 0, 1);
        $data['repository'] = Repository::findOrFail($id);
        FetchRepositoryIssues::dispatch($data['repository']->repository_id, $data['user_data']->id);
    }
}
