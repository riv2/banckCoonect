<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeedsController extends Controller
{
    public function index(Request $request)
    {
        return view("partner.feeds");
    }
}