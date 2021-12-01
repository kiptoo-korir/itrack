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
        $url = 'https://api.github.com/repos/'.$this->repoFullname.'/issues?sort=created&page=1&per_page=50&state=all';

        $invalidTokenService = new InvalidTokenService();
        $apiService = (new ApiCallsService($this->tokenService, $invalidTokenService));

        $issuesInRepository = $apiService->githubCallsHandler($url, $this->userId);

        $count = Issue::where(['owner' => $this->userId, 'repository' => $this->repoId])->count();

        // Early return if the api call produced an error
        if ('array' !== gettype($issuesInRepository)) {
            return;
        }

        // Checks if all issues have been removed from the repository yet they exist in DB
        // If the check is true, removes the issues
        if ($count > 0 && 0 === count($issuesInRepository)) {
            DB::table('issues')->where('repository', $this->repoId)
                ->delete()
            ;
        }

        // Early If API calls returns no issues
        if (0 === count($issuesInRepository)) {
            return;
        }

        $latestUpdated = Issue::where(['owner' => $this->userId, 'repository' => $this->repoId])
            ->orderBy('date_updated_online', 'desc')
            ->limit(1)->pluck('date_updated_online')->first();
        $latestUpdatedUnix = strtotime($latestUpdated);

        $existingIssueNos = DB::table('issues')->where(['owner' => $this->userId, 'repository' => $this->repoId])
            ->select('issue_no')
            ->distinct()
            ->pluck('issue_no')
            ->toArray()
        ;

        $newIssues = [];
        $issuesToUpdate = [];

        // Get the issue nos of the issues fetched from online
        $incomingIssueNos = array_map(function ($issue) {
            return $issue->number;
        }, $issuesInRepository);

        // Get the issue nos of issues that are not in DB
        $newIssuesNos = array_diff($incomingIssueNos, $existingIssueNos);

        // Get the issue nos of issues that are in DB but not in online records
        $issuesToRemoveNos = array_diff($existingIssueNos, $incomingIssueNos);

        foreach ($issuesInRepository as $issue) {
            // First time issues are added to repository
            if (0 === $count) {
                array_push($newIssues, $this->createArr($issue));

                continue;
            }

            // Get Newly Created Issues
            if (in_array($issue->number, $newIssuesNos)) {
                array_push($newIssues, $this->createArr($issue));

                continue;
            }

            // Updated issues pushed to a single array
            if (($latestUpdatedUnix) && strtotime($issue->updated_at) > $latestUpdatedUnix) {
                array_push($issuesToUpdate, $this->updateArr($issue));

                continue;
            }
        }

        // Insert new issues into database
        if (!empty($newIssues)) {
            Issue::insert($newIssues);
            FetchIssuesInRepoEvent::dispatch($this->repoId, $newIssues);
        }

        // Update existing issues that have been changed
        if (!empty($issuesToUpdate)) {
            foreach ($issuesToUpdate as $update) {
                DB::table('issues')->where(
                    [
                        'repository' => $this->repoId,
                        'issue_no' => $update['issue_no'],
                        'owner' => $this->userId,
                    ]
                )
                    ->update($update)
                ;
            }
        }

        // Remove issues from DB that have been removed online
        if (count($issuesToRemoveNos) > 0) {
            DB::table('issues')->where('repository', $this->repoId)
                ->whereIn('issue_no', $issuesToRemoveNos)
                ->delete()
            ;
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

    protected function updateArr($issue): array
    {
        return [
            'issue_no' => $issue->number,
            'state' => $issue->state,
            'title' => $issue->title,
            'body' => $issue->body,
            'date_updated_online' => $issue->updated_at,
            'labels' => json_encode($issue->labels),
            'date_closed_online' => $issue->closed_at,
            'updated_at' => now(),
        ];
    }
}
