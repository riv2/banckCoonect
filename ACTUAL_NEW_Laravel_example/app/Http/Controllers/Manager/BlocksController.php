<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlocksController extends Controller
{
    public function index(Request $request)
    {
        return view("manager.blocks");
    }
}