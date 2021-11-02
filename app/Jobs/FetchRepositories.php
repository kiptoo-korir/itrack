<?php

namespace App\Jobs;

use App\Events\RepositoriesFetched;
use App\Models\Repository;
use App\Services\InvalidTokenService;
use App\Services\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class FetchRepositories implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected $tok_service;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param mixed $user_id
     */
    public function __construct(int $user_id)
    {
        $this->tok_service = new TokenService();
        $this->user = $user_id;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $response = $this->tok_service->client($this->user)
            ->get('https://api.github.com/user/repos?sort=created')
        ;

        $statusCode = $response->status();
        $invalidTokenService = new InvalidTokenService();
        $invalidTokenService->responseHandler($statusCode);

        $latest_date = Repository::where('owner', $this->user)
            ->orderBy('date_created_online', 'desc')->limit(1)->pluck('date_created_online')->first();
        $latest_date = strtotime($latest_date);

        $latest_updated = Repository::where('owner', $this->user)
            ->orderBy('date_updated_online', 'desc')->limit(1)->pluck('date_updated_online')->first();

        $count = Repository::where('owner', $this->user)->count();

        $repos = json_decode($response->body());

        $bulk_insert = [];
        $bulk_update = [];

        foreach ($repos as $repo) {
            $repo_created = strtotime($repo->created_at);
            if (0 == $count) {
                $arr = $this->create_arr($repo);
                array_push($bulk_insert, $arr);
            } elseif (isset($latest_date) && $repo_created > $latest_date) {
                $arr = $this->create_arr($repo);
                array_push($bulk_insert, $arr);
            } elseif ($repo->updated_at > $latest_updated) {
                $update_arr = [
                    'name' => $repo->name,
                    'fullname' => $repo->full_name,
                    'repository_id' => $repo->id,
                    'description' => $repo->description,
                    'date_updated_online' => $repo->updated_at,
                    'issues_count' => $repo->open_issues_count,
                ];

                array_push($bulk_update, $update_arr);
            }
        }

        if (!empty($bulk_insert)) {
            Repository::insert($bulk_insert);
            RepositoriesFetched::dispatch($bulk_insert, $this->user);
        }

        if (!empty($bulk_update)) {
            foreach ($bulk_update as $update) {
                DB::table('repositories')->where('repository_id', $update['repository_id'])
                    ->update($update)
                ;
            }
        }
    }

    protected function create_arr($repo): array
    {
        return [
            'owner' => $this->user,
            'platform' => 1,
            'name' => $repo->name,
            'fullname' => $repo->full_name,
            'repository_id' => $repo->id,
            'description' => $repo->description,
            'date_created_online' => $repo->created_at,
            'date_pushed_online' => $repo->pushed_at,
            'date_updated_online' => $repo->updated_at,
            'issues_count' => $repo->open_issues_count,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function check_number()
    {
        // $response = Http::get('https://api.github.com/users/Liyengwas/repos?per_page=1');
        // $header = $response->headers()['Link'][0];
        // $start = strrpos($header, 'e=') + 2;
        // $end = strrpos($header, '>;');
        // $length = $end - $start;
        // $num = intval(substr($header, $start, $length));
        // dd($header, $start, $end, $num, $length);
        // dd($header);
    }
}
