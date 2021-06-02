<?php

namespace App\Http\Controllers\Advertiser;

use App\Helpers\PartnerProgramStorage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $pp = PartnerProgramStorage::getPP();

        if ($pp->onboarding_status=='registered') {
            return view('advertiser.welcome', [
                'pp' => $pp,
            ]);
        }

        return redirect()->route('advertiser.report');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_url' => 'required|url|max:255',
            'pp_target' => 'required|in:lead,products',
            'currency' => 'required|in:RUB,EUR,USD'
        ]);

        $pp = PartnerProgramStorage::getPP();
        $pp->company_url = $request->company_url;
        $pp->pp_target = $request->pp_target;
        $pp->currency = $request->currency;
        $pp->comment = $request->comment;
        $pp->onboarding_status = 'step1';
        $pp->save();
        return redirect()->route('advertiser.report');
    }
}
