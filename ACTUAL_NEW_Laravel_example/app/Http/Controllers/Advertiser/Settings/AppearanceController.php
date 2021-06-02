<?php

namespace App\Http\Controllers\Advertiser\Settings;

use App\Helpers\PartnerProgramStorage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppearanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pp = PartnerProgramStorage::getPP();
        return view('advertiser.settings.appearance.index', [
            'pp' => $pp,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'short_name' => 'nullable|string',
            'long_name' => 'nullable|string',
            'logo' => 'image|max:2048',
            'favicon' => 'max:2048|mimes:ico',
        ]);

        $pp = PartnerProgramStorage::getPP();
        $pp->short_name = $request->short_name;
        $pp->long_name = $request->long_name;
        $lang = [];
        foreach (config('app.locales') as $key => $item) {
            $lang[$key] = false;
        }
        foreach ($request->get('lang') as $key => $value) {
            foreach (config('app.locales') as $configKey => $item) {
                if ($configKey == $value) {
                    $lang[$configKey] = true;
                }
            }
        }
        $pp->lang = $lang;
        $pp->color1 = $request->color1;
        $pp->color2 = $request->color2;
        $pp->color3 = $request->color3;
        $pp->color4 = $request->color4;

        if ($request->file('logo') && $request->file('logo')->isValid()) {
            $file_name = 'logo_' . $pp->id . '.' . $request->file('logo')->getClientOriginalExtension();
            $filePath = $request->file('logo')->storeAs('logo', $file_name, 'public');
            $pp->logo = '/storage/' . $filePath;
        }

        if ($request->file('favicon') && $request->file('favicon')->isValid()) {
            $file_name = 'favicon_' . $pp->id . '.' . $request->file('favicon')->getClientOriginalExtension();
            $filePath = $request->file('favicon')->storeAs('favicon', $file_name, 'public');
            $pp->favicon = '/storage/' . $filePath;
        }

        $pp->save();

        $countLangs = 0;
        $currentLang = 'ru';
        foreach (array_reverse($lang) as $key => $item) {
            if ($item) {
                $countLangs += 1;
                $currentLang = $key;
            }
        }
        if ($countLangs == 1) {
            return redirect(route('locale', ['locale' => $currentLang]))->withSuccess(['Данные успешно обновлены!']);
        }

        return redirect()->back()->withSuccess(['Данные успешно обновлены!']);
    }
}
