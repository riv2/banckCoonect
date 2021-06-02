<?php

namespace App\Http\Controllers\Advertiser\Settings;

use App\Helpers\PartnerProgramStorage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pp = PartnerProgramStorage::getPP();
        return view('advertiser.settings.company.index', [
            'pp' => $pp,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'company_url' => 'required|url|max:255',
            'pp_target' => 'required|in:lead,products',
            'currency' => 'required|in:RUB,EUR,USD',
            'prod_domain' => 'nullable|string',
        ]);

        $pp = PartnerProgramStorage::getPP();
        $pp->company_url = $request->company_url;
        $pp->pp_target = $request->pp_target;
        $pp->currency = $request->currency;
        $pp->prod_domain = $request->prod_domain;
        $pp->save();

        return redirect()->back()->withSuccess(['Данные успешно обновлены!']);
    }
}
