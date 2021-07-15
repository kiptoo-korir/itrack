<?php

namespace App\Jobs;

use App\Services\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchRepositoryIssues implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected $tok_service;
    protected $user_id;
    protected $repo_id;

    /**
     * Create a new job instance.
     *
     * @param mixed $user
     * @param mixed $repository_id
     */
    public function __construct($repository_id, $user)
    {
        $this->tok_service = new TokenService();
        $this->repo_id = $repository_id;
        $this->user_id = $user;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // $latest_date = Issue::where('owner', $this->user)
        //     ->orderBy('date_created_online', 'desc')->limit(1)->pluck('date_created_online')->first();
        // $latest_date = strtotime($latest_date);

        // $latest_updated = Repository::where('owner', $this->user)
        //     ->orderBy('date_updated_online', 'desc')->limit(1)->pluck('date_updated_online')->first();

        // $count = Repository::where('owner', $this->user)->count();

        // $repos = json_decode($response->body());

        $bulk_insert = [];
        $bulk_update = [];
    }
}
