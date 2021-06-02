<?php

namespace App\Http\Controllers\Advertiser;

use App\Helpers\PartnerProgramStorage;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function index(Request $request)
    {
        $partners_cnt = User::query()
            ->where('pp_id', '=', PartnerProgramStorage::getPP()->id)
            ->where('role', '=', 'partner')
            ->count();

        return view("advertiser.tariff",[
            'partners_cnt' => $partners_cnt,
        ]);
    }
}
