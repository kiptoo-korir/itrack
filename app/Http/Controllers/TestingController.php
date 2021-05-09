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
        $response = $this->tok_service->client()
            ->get('https://api.github.com/user/repos')
        // ->get('https://api.github.com/repos/Brian-Nduhiu/Store/languages')
        ;
        dd(json_decode($response->body()));
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
