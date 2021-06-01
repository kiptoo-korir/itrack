<?php

namespace App\Jobs;

use App\Services\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchRepositories implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected $tok_service;

    /**
     * Create a new job instance.
     */
    public function __construct(TokenService $tokenService)
    {
        $this->tok_service = $tokenService;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
    }

    protected function check_number()
    {
        // $response = Http::get('https://api.github.com/users/Liyengwas/repos?per_page=1');
        // $header = $response->headers()['Link'][0];
        // $start = strrpos($header, 'e=') + 2;
        // $end = strrpos($header, '>;');
        // $length = $end - $start;
        // $num = intval(substr($header, $start, $length));
        // dd($header, $start, $end, $num, $length);
        // dd($header);
    }
}
