<?php

namespace App\Http\Controllers\Student;

use App\Services\Auth;
use App\WifiTariff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WifiController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList()
    {
        $wifiTariff = WifiTariff::whereNull('deleted_at')->get();
        $userWifiList = Auth::user()->wifi;

        if($userWifiList)
        {
            foreach ($wifiTariff as $k => $tariff)
            {
                foreach ($userWifiList as $userWifi)
                {
                    $wifiTariff[$k]->active = null;

                    if($userWifi->value == $tariff->value)
                    {
                        $wifiTariff[$k]->active = $userWifi;
                    }
                }
            }
        }

        return view('pages.wifi',[
            'user' => Auth::user()->id,
            'wifiTariff' => $wifiTariff
        ]);
    }
}
