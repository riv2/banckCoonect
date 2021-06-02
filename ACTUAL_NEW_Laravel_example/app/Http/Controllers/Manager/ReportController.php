<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Pp;
use App\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('manager.report', [
            'advertisers_count' => $this->getAdvertisersCount(),
            'partners_count' => $this->getPartnersCount(),
            'tariff_count' => $this->getTariffCount(),
            'pps' => $this->getPps(),
        ]);
    }

    private function getAdvertisersCount()
    {
        return User::advertiser()->active()->count();
    }

    private function getPartnersCount()
    {
        return User::partner()->active()->count();
    }

    private function getTariffCount()
    {
        return Pp::query()
            ->selectRaw('count(*) as aggregate')
            ->addSelect('tariff')
            ->groupBy('tariff')
            ->get()
            ->pluck('aggregate', 'tariff');
    }

    private function getPps()
    {
        return Pp::query()
            ->withCount('users')
            ->orderBy('id', 'DESC' )
            // ->advertiser()
            // ->active()
            ->orderByDesc('id', 'desc')
            ->paginate(100);
    }
}
