<?php

namespace App\Jobs;

use App\Events\RepositoriesFetched;
use App\Models\Repository;
use App\Services\ApiCallsService;
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
    protected $tokenService;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param mixed $user_id
     */
    public function __construct(int $user_id)
    {
        $this->tokenService = new TokenService();
        $this->user = $user_id;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $url = 'https://api.github.com/user/repos?sort=created&page=1&per_page=50';

        $invalidTokenService = new InvalidTokenService();
        $apiService = (new ApiCallsService($this->tokenService, $invalidTokenService));

        $repositories = $apiService->githubCallsHandler($url, $this->user);

        if (0 === count($repositories)) {
            return;
        }

        $latestUpdated = Repository::where('owner', $this->user)
            ->orderBy('date_updated_online', 'desc')
            ->limit(1)
            ->pluck('date_updated_online')
            ->first()
        ;

        $latestUpdatedUnix = strtotime($latestUpdated);

        $existingRepoIds = DB::table('repositories')->where(['owner' => $this->user])
            ->select('repository_id')
            ->distinct()
            ->pluck('repository_id')
            ->toArray()
        ;

        $count = Repository::where('owner', $this->user)->count();

        $newRepos = [];
        $reposToUpdate = [];

        $incomingRepoIds = array_map(function ($repository) {
            return $repository->id;
        }, $repositories);

        $newRepoIds = array_diff($incomingRepoIds, $existingRepoIds);
        $reposToRemoveIds = array_diff($existingRepoIds, $incomingRepoIds);

        foreach ($repositories as $repository) {
            // First time issues are added to repository
            if (0 === $count) {
                array_push($newRepos, $this->createArr($repository));

                continue;
            }

            // Get Newly Created Issues
            if (in_array($repository->id, $newRepoIds)) {
                array_push($newRepos, $this->createArr($repository));

                continue;
            }

            // Updated issues pushed to a single array
            if (isset($latestUpdatedUnix) && strtotime($repository->updated_at) > $latestUpdatedUnix) {
                array_push($reposToUpdate, $this->updateArr($repository));

                continue;
            }
        }

        if (!empty($newRepos)) {
            Repository::insert($newRepos);
            RepositoriesFetched::dispatch($newRepos, $this->user);
        }

        if (!empty($reposToUpdate)) {
            foreach ($reposToUpdate as $update) {
                DB::table('repositories')->where('repository_id', $update['repository_id'])
                    ->update($update)
                ;
            }
        }

        if (count($reposToRemoveIds) > 0) {
            $this->deleteRepositories($reposToRemoveIds);
        }
    }

    protected function createArr($repository): array
    {
        return [
            'owner' => $this->user,
            'platform' => 1,
            'name' => $repository->name,
            'fullname' => $repository->full_name,
            'repository_id' => $repository->id,
            'description' => $repository->description,
            'date_created_online' => $repository->created_at,
            'date_pushed_online' => $repository->pushed_at,
            'date_updated_online' => $repository->updated_at,
            'issues_count' => $repository->open_issues_count,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function updateArr($repository): array
    {
        return [
            'name' => $repository->name,
            'fullname' => $repository->full_name,
            'repository_id' => $repository->id,
            'description' => $repository->description,
            'date_updated_online' => $repository->updated_at,
            'issues_count' => $repository->open_issues_count,
        ];
    }

    protected function deleteRepositories(array $repositoryIds): void
    {
        if (count($repositoryIds) > 0) {
            $itrackRepoIds = DB::table('repositories')->whereIn('repository_id', $repositoryIds)
                ->pluck('id')
                ->toArray()
            ;

            DB::table('repositories')->whereIn('repository_id', $repositoryIds)
                ->delete()
            ;

            DB::table('project_repository')->whereIn('repository_id', $itrackRepoIds)
                ->delete()
            ;

            DB::table('repository_languages')->whereIn('repository_id', $itrackRepoIds)
                ->delete()
            ;

            DB::table('issues')->whereIn('repository', $itrackRepoIds)
                ->delete()
            ;
        }
    }
}
