<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiViewController extends Controller
{
    public function index(Request $request)
    {
        return view("advertiser.integration.api");
    }
}
