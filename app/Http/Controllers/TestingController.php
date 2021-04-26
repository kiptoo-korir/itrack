<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

class TestingController extends Controller
{
    public function test()
    {
        // dd('Buda');
        Mail::to('elijahkiptoo98@gmail.com')->send(new TestMail());
    }
}
