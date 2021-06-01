<?php

namespace App\Http\Controllers;

use App\Jobs\FetchRepositories;
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

        return view('repositories')->with($data);
    }
}
