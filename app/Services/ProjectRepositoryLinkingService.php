<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectRepositoryLinkingService
{
    public function linkNewRepositories(array $repositories, int $projectId)
    {
        $insertArray = $this->mapArrays($repositories, $projectId);

        return DB::table('project_repository')->insert($insertArray);
    }

    public function unlinkRepositories(array $repositories, int $projectId)
    {
        $userId = Auth::id();
        $returnState = true;
        foreach ($repositories as $repo) {
            $state = DB::table('project_repository')
                ->where([
                    'project_id' => $projectId,
                    'repository_id' => $repo,
                    'owner' => $userId,
                ])
                ->delete()
            ;

            if (!$state) {
                $returnState = $state;
            }
        }

        return $returnState;
    }

    protected function mapArrays(array $reposArray, int $projectId): array
    {
        $returnArray = [];
        $userId = Auth::id();
        foreach ($reposArray as $key => $repo) {
            array_push($returnArray, [
                'project_id' => $projectId,
                'repository_id' => $repo,
                'owner' => $userId,
            ]);
        }

        return $returnArray;
    }
}
