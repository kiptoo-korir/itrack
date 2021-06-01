<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use App\Services\TokenService;
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
        // $response = $this->tok_service->client()
        //     ->get('https://api.github.com/user/repos?sort=created')
        // ;
        // dd(json_decode($response->body()));
        $response = Http::get('https://api.github.com/users/Liyengwas/repos?per_page=1');
        $header = $response->headers()['Link'][0];
        $start = strrpos($header, 'e=') + 2;
        $end = strrpos($header, '>;');
        $length = $end - $start;
        $num = intval(substr($header, $start, $length));
        dd($header, $start, $end, $num, $length);
        dd($header);

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
}
