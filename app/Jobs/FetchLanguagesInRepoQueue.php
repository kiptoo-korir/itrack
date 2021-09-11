<?php

namespace App\Jobs;

use App\Events\FetchLanguagesInRepo;
use App\Models\Repository;
use App\Services\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchLanguagesInRepoQueue implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected $repoFullname;
    protected $userId;
    protected $tokenService;
    protected $repoId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $repoFullname, int $userId, int $repoId)
    {
        $this->repoFullname = $repoFullname;
        $this->userId = $userId;
        $this->repoId = $repoId;
        $this->tokenService = new TokenService();
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $repository = Repository::findOrFail($this->repoId);
        $response = $this->tokenService->client($this->userId)
            ->get('https://api.github.com/repos/'.$repository->fullname.'/languages')
        ;
        $languagesInRepo = (array) json_decode($response->body());
        if (count($languagesInRepo) > 0) {
            FetchLanguagesInRepo::dispatch($this->repoId, $languagesInRepo);
        }
    }
}
