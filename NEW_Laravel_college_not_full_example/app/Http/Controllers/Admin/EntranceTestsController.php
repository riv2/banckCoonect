<?php

namespace App\Http\Controllers\Admin;

use App\EntranceTest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EntranceTestsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList()
    {
        $entranceList = EntranceTest::get();

        return view('admin.pages.entrance_test.list',compact('entranceList'));
    }

    /**
     * @param $trendId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($entranceTestId)
    {
        $entrance = EntranceTest::where('id', $entranceTestId)->first();

        if(!$entrance && $entranceTestId == 'add')
        {
            $entrance = new EntranceTest();
        }
        elseif( $entranceTestId != 'add' && !is_numeric($entranceTestId) )
        {
            return view('errors.404');
        }

        return view('admin.pages.entrance_test.edit', [
            'entrance'          => $entrance,
            'quizeQuestionList' => $entrance->quizeQuestions,
            'questionEditUrl'   => route('adminEntranceTestsEditPost',['id'=>$entranceTestId])
        ]);
    }

    /**
     * @param Request $request
     * @param $trendId
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editPost(Request $request, $entranceId)
    {
        $ruleList = [
            'name' => 'required',
        ];

        $validator = \Validator::make($request->all(), $ruleList);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages());
        }

        $entrance = EntranceTest::where('id', $entranceId)->first();

        if(!$entrance && $entranceId == 'add')
        {
            $entrance = new EntranceTest();
        }
        elseif( $entranceId != 'add' && !is_numeric($entranceId) )
        {
            return view('errors.404');
        }

        $entrance->fill($request->all());
        $entrance->save();
        $entrance->attachQuizeQuestions($request->input('questions'));

        $request->session()->flash('flash_message', 'Изменения сохранены.');
        return redirect()->route('adminEntranceTestsEdit', ['id' => $entrance->id]);
    }

    /**
     * @param $trendId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($entranceId)
    {
        $entrance = EntranceTest::where('id', $entranceId)->first();

        if(!$entrance)
        {
            abort(404);
        }

        $entrance->delete();

        return redirect()->route('adminEntranceTestsList');
    }
}
