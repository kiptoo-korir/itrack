<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use App\Models\Platform;
use Illuminate\Support\Facades\Mail;

class TestingController extends Controller
{
    public function test()
    {
        // dd('Buda');
        // Mail::to('elijahkiptoo98@gmail.com')->send(new TestMail());
        $platform = Platform::where('name', 'Github')->select('id')->get()[0]->id;
        dd($platform);
    }
}
