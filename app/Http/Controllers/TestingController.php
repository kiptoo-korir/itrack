<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use App\Models\Note;
use App\Models\Repository;
use App\Models\RepositoryLanguage;
use App\Models\User;
use App\Notifications\ReminderNotification;
use App\Services\ApiCallsService;
use App\Services\InvalidTokenService;
use App\Services\TokenService;
use Faker;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class TestingController extends Controller
{
    protected $client;
    protected $tok_service;
    protected $invalidTokenService;

    public function __construct(TokenService $tokenService, InvalidTokenService $invalidTokenService)
    {
        $this->tok_service = $tokenService;
        $this->invalidTokenService = $invalidTokenService;
        // $this->client = $this->tok_service->client();
    }

    public function test()
    {
        $apiService = new ApiCallsService($this->tok_service, $this->invalidTokenService);

        $repositories = $apiService->githubCallsHandler('https://api.github.com/user/repos?sort=created&page=1&per_page=50', 5);
        dd($repositories);

        $latestUpdated = Repository::where('owner', 4)
            ->orderBy('date_updated_online', 'desc')
            ->limit(1)
            ->pluck('date_updated_online')
            ->first()
        ;

        $latestUpdatedUnix = strtotime($latestUpdated);

        $existingRepoIds = DB::table('repositories')->where(['owner' => 4])
            ->select('repository_id')
            ->distinct()
            ->pluck('repository_id')
            ->toArray()
        ;

        $count = Repository::where('owner', 4)->count();

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

        // dd($incomingIssueNos, $existingIssueNos);
        dd($reposToUpdate, $newRepos);

        // $issueNos = $issuesCollection->map(function ($issue) {
        //     return $issue->number;
        // })->toArray();

        // $newIssueNos = array_diff($issueNos, $existingIssueNos);
        // $issuesToRemove = array_diff($existingIssueNos, $issueNos);

        // $updatedIssues = $issuesCollection->filter(function ($issue) use ($latestUpdatedUnix) {
        //     return strtotime($issue->updated_at) > $latestUpdatedUnix;
        // })->map(function ($issue) {
        //     return $this->createArr($issue);
        // });

        // dd($latestUpdatedUnix, $updatedIssues);
    }

    public function callback(Request $request)
    {
        $code = $_GET['code'];
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post('https://github.com/login/oauth/access_token', [
            'client_id' => env('GITHUB_CLIENT_ID'),
            'client_secret' => env('GITHUB_SECRET'),
            'code' => $code,
        ]);

        $body = $response->body();
        // dd(json_decode());
    }

    public function test_2()
    {
        Mail::to('elijahkiptoo98@gmail.com')->send(new TestMail());
    }

    public function get_count()
    {
        $response = Http::get('https://api.github.com/users/Liyengwas/repos?per_page=1');
        $header = $response->headers()['Link'][0];
        $start = strrpos($header, 'e=') + 2;
        $end = strrpos($header, '>;');
        $length = $end - $start;
        $num = intval(substr($header, $start, $length));
        dd($header, $start, $end, $num, $length);
        dd($header);
    }

    public function fake_data()
    {
        $faker = Faker\Factory::create();
        $arr = [];

        for ($i = 0; $i < 10; ++$i) {
            $arr['title'] = $faker->sentence(4);
            $arr['message'] = $faker->sentence(50);
            $arr['owner'] = 4;
            $arr['type'] = 'GENERAL';
            Note::create($arr);
        }
    }

    public function notify()
    {
        $languagesInStore = RepositoryLanguage::where('repository_id', 43)
            ->pluck('value', 'name')->toArray()
            ;

        dd($languagesInStore);
        $arr = [
            'PHP' => 87738,
            'HTML' => 5789,
            'Vue' => 552,
        ];

        $newarr = [
            'PHP' => 84738,
            'HTML' => 5789,
            'Vue' => 552,
            'CSS' => 332,
        ];

        $langsAlreadyExisting = array_intersect_key($newarr, $arr);
        $editedLanguages = array_diff($langsAlreadyExisting, $arr);
        dd($langsAlreadyExisting, $editedLanguages);
    }

    public function parseLinkHeader(string $header): array
    {
        if (0 == strlen($header)) {
            throw new \Exception('input must not be of zero length');
        }

        $parts = explode(',', $header);
        $links = [];

        foreach ($parts as $p) {
            $section = explode(';', $p);

            if (2 != count($section)) {
                throw new \Exception("section could not be split on ';'");
            }
            $url = trim(preg_replace('/<(.*)>/', '$1', $section[0]));
            $name = trim(preg_replace('/rel="(.*)"/', '$1', $section[1]));
            $links[$name] = $url;
        }

        return $links;
    }

    public function recursivePaginatorHandler(string $url, int $userId): array
    {
        $response = $this->tok_service->client($userId)
            ->get($url)
        ;

        $repos = json_decode($response);

        $linkHeader = $response->headers()['Link'] ?? null;

        $linksArray = ($linkHeader[0]) ? $this->parseLinkHeader($linkHeader[0]) : [];
        if (isset($linkHeader[0]) && !isset($linksArray['next']) && !isset($linksArray['last'])) {
            return $repos;
        }

        $moreRepos = $this->recursivePaginatorHandler($linksArray['next'], $userId);

        return array_merge($repos, $moreRepos);
    }

    public function printer(array $arr)
    {
        dd($arr);
    }

    public function showEmailView()
    {
        $exisitngRepositoryIds = DB::table('repositories')->where('owner', 4)
            ->pluck('repository_id')
            ->toArray()
        ;
        dd($exisitngRepositoryIds);
        $reminders = DB::table('users')->rightJoin('reminders as r', 'users.id', '=', 'r.owner')
            ->leftJoin('projects as p', 'r.project', '=', 'p.id')
            ->select('users.name', 'users.id as user_id', 'users.email', 'r.id as reminder_id', 'r.title', 'r.message', 'p.id as project_id', 'p.name as project_name')
            ->selectRaw('to_char(r.due_date, \'Dy DD Mon, YYYY at HH:MI AM \') as due_date')
            // ->where('r.due_date', '=', $now)
            ->whereNull('r.deleted_at')
            ->get()
        ;
        // dd($reminders);
        foreach ($reminders as $reminder) {
            $user = User::findOrFail($reminder->user_id);
            $user->notify(new ReminderNotification($reminder));
        }
    }

    public function getIncomingNos($issue)
    {
        return $issue->number;
    }

    public function testPDFGeneration()
    {
        $path = base_path().'vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64';

        $outputPath = base_path('public/files/reports/').'test76.pdf';
        $route = 'http://localhost/itrack/api/task-report?startDate=2021-11-01&endDate=2021-12-11&userId=4&name=Elijah%20Korir';

        $process = new Process([$path, $route, $outputPath]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return response()->download($outputPath);
    }

    public function clearReportsDirectory()
    {
        $file = new Filesystem();
        $file->cleanDirectory('public/files/reports');
    }

    protected function createArr($repo): array
    {
        return [
            'owner' => 4,
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
}
