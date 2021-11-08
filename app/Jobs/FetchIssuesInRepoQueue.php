<?php

namespace App\Jobs;

use App\Events\FetchIssuesInRepoEvent;
use App\Models\Issue;
use App\Services\ApiCallsService;
use App\Services\InvalidTokenService;
use App\Services\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class FetchIssuesInRepoQueue implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected $repoId;
    protected $tokenService;
    protected $repoFullname;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $repoId, string $repoFullname, int $userId)
    {
        $this->repoId = $repoId;
        $this->tokenService = new TokenService();
        $this->repoFullname = $repoFullname;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $url = 'https://api.github.com/repos/'.$this->repoFullname.'/issues?sort=created&page=1&per_page=50';

        $invalidTokenService = new InvalidTokenService();
        $apiService = (new ApiCallsService($this->tokenService, $invalidTokenService));

        $issuesInRepository = $apiService->githubCallsHandler($url, $this->userId);

        if (0 === count($issuesInRepository)) {
            return;
        }

        $latestDate = Issue::where('owner', $this->userId)
            ->orderBy('date_created_online', 'desc')
            ->limit(1)->pluck('date_created_online')->first();
        $latestDateUnix = strtotime($latestDate);

        $latestUpdated = Issue::where('owner', $this->userId)
            ->orderBy('date_updated_online', 'desc')
            ->limit(1)->pluck('date_updated_online')->first();
        $latestUpdatedUnix = strtotime($latestUpdated);

        $count = Issue::where(['owner' => $this->userId, 'repository' => $this->repoId])->count();

        $bulkInsert = [];
        $bulkUpdate = [];

        foreach ($issuesInRepository as $issue) {
            $issueCreated = strtotime($issue->created_at);
            if (0 == $count) {
                $arr = $this->createArr($issue);
                array_push($bulkInsert, $arr);
            } elseif (isset($latestDateUnix) && $issueCreated > $latestDateUnix) {
                $arr = $this->createArr($issue);
                array_push($bulkInsert, $arr);
            } elseif ($issue->updated_at > $latestUpdatedUnix) {
                $updateArr = [
                    'issue_no' => $issue->number,
                    'state' => $issue->state,
                    'title' => $issue->title,
                    'body' => $issue->body,
                    'date_updated_online' => $issue->updated_at,
                    'labels' => json_encode($issue->labels),
                    'date_closed_online' => $issue->closed_at,
                    'updated_at' => now(),
                ];

                array_push($bulkUpdate, $updateArr);
            }
        }

        if (!empty($bulkInsert)) {
            Issue::insert($bulkInsert);
            FetchIssuesInRepoEvent::dispatch($this->repoId, $bulkInsert);
        }

        if (!empty($bulkUpdate)) {
            foreach ($bulkUpdate as $update) {
                DB::table('issues')->where('repository', $update['repository'])
                    ->update($update)
                ;
            }
        }
    }

    protected function createArr($issue): array
    {
        return [
            'owner' => $this->userId,
            'repository' => $this->repoId,
            'issue_no' => $issue->number,
            'state' => $issue->state,
            'title' => $issue->title,
            'body' => $issue->body,
            'date_created_online' => $issue->created_at,
            'date_updated_online' => $issue->updated_at,
            'labels' => json_encode($issue->labels),
            'date_closed_online' => $issue->closed_at,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
