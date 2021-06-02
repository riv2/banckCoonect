<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\PixelLog;
use Illuminate\Http\Request;

class PixelViewController extends Controller
{
    public function index(Request $request)
    {
        $collection = PixelLog::query()
            ->where('pp_id', '=', auth()->user()->pp_id)
            ->simplePaginate(50);

        return view('advertiser.integration.pixel', [
            'collection' => $collection,
        ]);
    }
}
