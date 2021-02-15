<?php

namespace App\Http\Controllers\Admin;

use App\News;
use App\Services\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList(Request $request)
    {
        $newsList = News::select([
            'id',
            'title',
            'created_at'
        ])->get();

        return view('admin.pages.news.list', compact('newsList'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        if($id == 'add')
        {
            $newsModel = new News();
        }
        else
        {
            $newsModel = News::where('id', $id)->first();
        }

        if(!$newsModel)
        {
            abort(404);
        }

        return view('admin.pages.news.edit', compact('newsModel'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editPost(Request $request, $id)
    {
        if($id == 'add')
        {
            $newsModel = new News();
        }
        else
        {
            $newsModel = News::where('id', $id)->first();
        }

        if(!$newsModel)
        {
            abort(404);
        }

        $newsModel->fill($request->all());
        $newsModel->user_id = Auth::user()->id;
        $newsModel->save();

        return redirect()->back()->with('flash_message', 'Changes Saved');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePost(Request $request, $id)
    {
        $newsModel = News::where('id', $id)->first();

        if(!$newsModel)
        {
            abort(404);
        }

        $newsModel->delete();

        return redirect()->back();
    }
}
