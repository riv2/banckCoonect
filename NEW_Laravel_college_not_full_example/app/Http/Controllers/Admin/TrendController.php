<?php

namespace App\Http\Controllers\Admin;

use App\Trend;
use App\TrendQualification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Validators\AdminTrendAddValidator;

class TrendController extends Controller
{
    /**
     * @param $trendId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList()
    {
        $trendList = Trend::get();

        return view('admin.pages.trends.list',compact('trendList'));
    }

    /**
     * @param $trendId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($trendId)
    {
        $trend = Trend::where('id', $trendId)->first();

        if(!$trend && $trendId == 'add')
        {
            $trend = new Trend();
        }
        elseif( $trendId != 'add' && !is_numeric($trendId) )
        {
            return view('errors.404');
        }

        return view('admin.pages.trends.edit', [
            'trend' => $trend,
        ]);
    }

    /**
     * @param Request $request
     * @param $trendId
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editPost(Request $request, $trendId)
    {

        // validation data
        $obValidator = AdminTrendAddValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return redirect()->back()->withErrors( $obValidator->errors() )->withInput();
        }

        $trend = Trend::where('id', $trendId)->first();

        if(!$trend && $trendId == 'add')
        {
            $trend = new Trend();
        }
        elseif( $trendId != 'add' && !is_numeric($trendId) )
        {
            return view('errors.404');
        }

        $trend->fill($request->all());
        $trend->save();

        $trend->qualifications()->delete();

        $trendQualifications = [];

        foreach ($request->input('qualifications', []) as $qualification) {
            $trendQualifications[] = [
                'trend_id' => $trend->id,
                'name_ru' => $qualification['name_ru'],
                'name_kz' => $qualification['name_kz'],
                'name_en' => $qualification['name_en'],
            ];
        }

        TrendQualification::insert($trendQualifications);

        $request->session()->flash('flash_message', 'Изменения сохранены.');
        return redirect()->route('adminTrendEdit', ['id' => $trend->id]);
    }

    /**
     * @param $trendId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($trendId)
    {
        $trend = Trend::where('id', $trendId)->first();

        if(!$trend)
        {
            abort(404);
        }

        $trend->delete();

        return redirect()->route('adminTrendList');
    }
}
