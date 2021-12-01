<?php

namespace App\Jobs;

use App\Events\FetchLanguagesInRepo;
use App\Models\Repository;
use App\Models\RepositoryLanguage;
use App\Services\ApiCallsService;
use App\Services\InvalidTokenService;
use App\Services\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
        $url = 'https://api.github.com/repos/'.$repository->fullname.'/languages';

        $invalidTokenService = new InvalidTokenService();
        $apiService = (new ApiCallsService($this->tokenService, $invalidTokenService));
        $languagesInRepo = $apiService->githubCallsHandler($url, $this->userId);

        // Early return if API calls produces an error
        if ('array' !== gettype($languagesInRepo)) {
            return;
        }

        // Early return if the API calls produces empty result set
        if (0 === count($languagesInRepo)) {
            return;
        }

        if (count($languagesInRepo) > 0) {
            FetchLanguagesInRepo::dispatch($this->repoId, $languagesInRepo);

            $languagesInsertArr = [];
            $languagesInStore = RepositoryLanguage::where('repository_id', $this->repoId)
                ->pluck('value', 'name')->toArray()
            ;

            $languagesSorted = $this->filterLanguagesArray($languagesInRepo, $languagesInStore);

            $languagesInsertArr = $languagesSorted['insertArray'];
            $languagesEditedArr = $languagesSorted['editArray'];

            RepositoryLanguage::insert($languagesInsertArr);

            foreach ($languagesEditedArr as $key => $value) {
                DB::table('repository_languages')->where([
                    'repository_id' => $this->repoId,
                    'name' => $key,
                ])->update([
                    'value' => $value,
                ]);
            }
        }
    }

    private function filterLanguagesArray(array $incomingLanguages, array $existingLanguages): array
    {
        $insertArr = [];
        $editArr = [];

        if (0 === count($existingLanguages)) {
            foreach ($incomingLanguages as $key => $language) {
                array_push($insertArr, $this->createArray($key, $language));
            }

            return [
                'insertArray' => $insertArr,
                'editArray' => $editArr,
            ];
        }

        $langsAlreadyExisting = array_intersect_key($incomingLanguages, $existingLanguages);
        $newLanguages = array_diff_key($incomingLanguages, $existingLanguages);
        $editArr = array_diff($langsAlreadyExisting, $existingLanguages);

        foreach ($newLanguages as $key => $value) {
            array_push($insertArr, $this->createArray($key, $value));
        }

        return [
            'insertArray' => $insertArr,
            'editArray' => $editArr,
        ];
    }

    private function createArray(string $language, int $valueInBytes): array
    {
        return [
            'name' => $language,
            'value' => $valueInBytes,
            'repository_id' => $this->repoId,
        ];
    }
}
