<?php

namespace App\Http\Controllers\Admin;

use App\HelpRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList()
    {
        $helpList = HelpRequest::get();

        return view('admin.pages.help.list', ['helpList' => $helpList ?? []]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function info($id)
    {
        $help = HelpRequest::where('id', $id)->
        with(['user' => function($query){
            $query->with(['bcApplication' => function($query1){
                $query1->with('nationality');
            }]);
            $query->with(['mgApplication' => function($query1){
                $query1->with('nationality');
            }]);
        }])->
        first();

        if(!$help)
        {
            abort(404);
        }

        return view('admin.pages.help.info', ['help' => $help]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $help = HelpRequest::where('id', $id)->first();

        if(!$help)
        {
            abort(404);
        }

        $help->delete();

        return redirect()->route('adminHelpList');
    }
}
