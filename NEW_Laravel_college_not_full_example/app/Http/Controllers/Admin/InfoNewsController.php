<?php

namespace App\Http\Controllers\Admin;

use App\InfoNews;
use App\Http\Requests\InfoNews\Store as InfoStoreRequest;
use App\Http\Controllers\Controller;
use App\Services\Auth;
use Yajra\DataTables\DataTables;

class InfoNewsController extends Controller
{
    public function getInfo() {
        return view('admin.pages.infonews.list');
    }

    public function getInfoTable() {
        $info = InfoNews::all();

        return Datatables::of($info)
            ->addColumn('action', function ($info) {
                return '<a href="' . route('admin.news.edit', ['info_id' => $info->id]) . '" class="btn btn-default">
                            <i class="md md-edit"></i>
                        </a>
                        <a href="' . route('admin.news.remove', ['info_id' => $info->id]) . '" class="btn btn-default">
                            <i class="md md-remove"></i>
                        </a>';
            })
            ->addColumn('title', function ($info) {
                return $info->title;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function createInfo() {
        return view('admin.pages.infonews.edit');
    }

    public function editInfo($info_id) {
        $info = InfoNews::find($info_id);

        if (!isset($info)) {
            abort(404);
        }

        return view('admin.pages.infonews.edit', compact('info'));
    }

    public function storeInfo(InfoStoreRequest $request, $info_id) {
        if ($info_id == 0) {
            $info = new InfoNews();
        } else {
            $info = InfoNews::find($info_id);

            if (!isset($info)) {
                abort(404);
            }
        }

        $info->fill($request->all());
        $info->is_important = $request->has('is_important');
        $info->user_id = Auth::user()->id;
        $info->save();

        return redirect()->route('admin.news.edit', ['info_id' => $info->id])->with('flash_message', 'Changes Saved');
    }

    public function removeInfo($info_id) {
        $info = InfoNews::find($info_id);

        if (isset($info)) {
            $info->delete();
        }

        return redirect()->route('admin.news.get')->with('flash_message', 'Changes Saved');
    }
}
