<?php

namespace App\Jobs;

use App\Events\RepositoriesFetched;
use App\Models\Repository;
use App\Services\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $latest_date = Repository::select('date_created_online')->orderBy('date_created_online', 'desc')->limit(1)->get();
        $repos = json_decode($response->body());
        $bulk_insert = [];
        foreach ($repos as $repo) {
            $arr = [
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
            ];

            array_push($bulk_insert, $arr);
        }
        RepositoriesFetched::dispatch($bulk_insert, $this->user);
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
