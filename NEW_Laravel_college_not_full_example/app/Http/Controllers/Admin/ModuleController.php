<?php

namespace App\Http\Controllers\Admin;

use App\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class ModuleController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request)
    {
        return view('admin.pages.modules.list');
    }


    /**
     * Ajax answer
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAjax(Request $request)
    {
        $searchData = Module::getListForAdmin(
            $request->input('search')['value'],
            $request->input('start', 0),
            $request->input('length', 10),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'asc'
        );

        return Response::json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $searchData['recordsTotal'],
            'recordsFiltered' => $searchData['recordsFiltered'],
            'data' => $searchData['data']
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function info($id)
    {
        $model = null;

        if ($id == 'new') {
            $model = new Module();
        } elseif (is_numeric($id)) {
            $model = Module
                ::with('disciplines')
                ->where('id', $id)->first();
        }

        if (!$model) {
            abort(404);
        }

        return view('admin.pages.modules.form', ['module' => $model]);
    }

    /**
     * @param $id
     * @param Request $request
     */
    public function updatePost($id, Request $request)
    {
        $model = null;

        if ($id == 'new') {
            $model = new Module();
        } elseif (is_numeric($id)) {
            $model = Module
                ::with('disciplines')
                ->where('id', $id)->first();
        }

        if (!$model) {
            abort(404);
        }

        $model->fill($request->all());
        $model->save();
        $model->disciplines()->sync($request->input('disciplines'));

        \Session::flash('flash_message', 'Изменения сохранены');

        if ($id == 'new') {
            return redirect()->route('adminModuleEdit', ['id' => $model->id]);
        }
        return redirect()->back();
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $model = Module::where('id', $id)->first();

        if (!$model) {
            abort(404);
        }

        $model->delete();
        \Session::flash('flash_message', 'Запись удалена');

        return redirect()->back();
    }
}
