<?php

namespace App\Http\Controllers;

use App;
use App\Mail\TestMail;
use App\Models\Note;
use App\Models\Repository;
use App\Models\RepositoryLanguage;
use App\Models\User;
use App\Services\TokenService;
use Faker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Request;

class TestingController extends Controller
{
    protected $client;
    protected $tok_service;

    public function __construct(TokenService $tokenService)
    {
        $this->tok_service = $tokenService;
        // $this->client = $this->tok_service->client();
    }

    public function test()
    {
        // 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
        // "{"access_token":"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","token_type":"bearer","scope":"notifications,repo,user"}"
        // dd('Buda');
        // Mail::to('elijahkiptoo98@gmail.com')->send(new TestMail());
        // $client = env('GITHUB_CLIENT_ID');
        // $request = "https://github.com/login/oauth/authorize?client_id={$client}&scope=repo%20notifications%20user";
        // dd($request);
        // $response = Http::withToken('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')
        // ->get('https://api.github.com/user?perpage=1')
        // dd(strtotime('2020-09-16T07:01:51Z'), strtotime('2020-09-16 10:01:51+03'), strtotime('2020-09-16 07:01:51'));
        $response = $this->tok_service->client()
            ->get('https://api.github.com/user/repos?sort=created')
        ;
        $repos = json_decode($response);
        dd($response);

        $bulk_insert = [];
        $bulk_update = [];

        $latest_date = Repository::where('owner', Auth::id())
            ->orderBy('date_created_online', 'desc')->limit(1)->pluck('date_created_online')->first();
        $latest_updated = Repository::where('owner', Auth::id())
            ->orderBy('date_updated_online', 'desc')->limit(1)->pluck('date_updated_online')->first();
        // dd($latest_date, $latest_updated);
        foreach ($repos as $repo) {
            if ((isset($latest_date) && $repo->created_at > $latest_date)) {
                $arr = [
                    'owner' => Auth::id(),
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

        dd($bulk_insert, $bulk_update);
        // $url = 'https://api.github.com/users/repos?per_page=1';
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //     'Content-Type: application/json',
        //     'Authorization: Bearer '.$this->tok_service->github_token(),
        // ]);
        // // dd('yes');

        // $output = curl_exec($ch);
        // curl_close($ch);
        // dd($output);
        // $response = $this->tok_service->client()->get('https://api.github.com/user?perpage=1');
        // dd($response);
        // $user = Auth::user();
        // $token_records = $user->access_token()->where(['platform' => 1, 'verified' => true])->first();
        // $token = $token_records->access_token;
        // dd($user);
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
        // $count = Repository::where('owner', 1)->count();
        // dd($count);
        // $now = now();
        // dd($now->setSeconds(0)->setMicroseconds(0));
        // $now = now()->setSeconds(0)->setMicroseconds(0);
        // $reminders = User::rightJoin('reminders as r', 'users.id', '=', 'r.owner')
        //     ->leftJoin('repositories as repo', 'r.repository', '=', 'repo.id')
        //     ->select('users.name', 'users.id', 'users.email', 'r.title', 'r.message', 'repo.id as repo_id', 'repo.fullname')
        //     ->selectRaw('to_char(r.due_date, \'Dy DD Mon, YYYY at HH:MI AM \') as due_date')
        //     // ->where('r.due_date', '=', $now)
        //     ->get()
        // ;

        // // dd($reminders);
        // if ($reminders->isNotEmpty()) {
        //     foreach ($reminders as $reminder) {
        //         return new App\Mail\ReminderMail($reminder);
        //     }
        // }

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
        // dd(now());
        dd(strtotime(now()));
        // $faker = Faker\Factory::create();
        // $arr = [];

        // for ($i = 0; $i < 10; ++$i) {
        //     $arr['title'] = $faker->sentence(4);
        //     $arr['message'] = $faker->sentence(50);
        //     $arr['owner'] = 4;
        //     $arr['type'] = 'GENERAL';
        //     Note::create($arr);
        // }

        // dd('Yes');
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
}
