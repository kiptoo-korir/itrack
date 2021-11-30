<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use App\Models\Note;
use App\Models\RepositoryLanguage;
use App\Models\User;
use App\Notifications\ReminderNotification;
use App\Services\ApiCallsService;
use App\Services\InvalidTokenService;
use App\Services\TokenService;
use Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Request;

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

        $issues = $apiService->githubCallsHandler('https://api.github.com/repos/kiptoo-korir/church-frontend/issues?sort=created&page=1&per_page=50', 4);
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
}
