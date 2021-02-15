<?php

namespace App\Http\Controllers\Admin;

use App\Services\MirasApi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList(Request $request)
    {
        $buildingList = MirasApi::request(MirasApi::BUILDING_LIST);

        return view('admin.pages.buildings.list', ['buildingList' => $buildingList]);
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
            return view('admin.pages.buildings.edit');
        }
        elseif( $id != 'add' && !is_numeric($id) )
        {
            return view('errors.404');
        }

        $building = MirasApi::request(MirasApi::BUILDING_INFO, ['id' => $id]);

        if(!$building)
        {
            return view('errors.404');
        }

        return view('admin.pages.buildings.edit', [
            'building' => $building
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editPost(Request $request, $id)
    {
        $ruleList = [
            'name'         => 'required|string|max:250',
            'address'      => 'string|max:400'
        ];

        $validator = \Validator::make($request->all(), $ruleList);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages());
        }

        $apiResult = false;
        if($id == 'add')
        {
            $apiResult = MirasApi::request(MirasApi::BUILDING_CREATE, $request->all());
            if(isset($apiResult->id))
            {
                $request->session()->flash('flash_message', 'Изменения сохранены.');
                return redirect()->route('adminBuildingEdit', ['id' => $apiResult->id]);
            }
        }
        elseif( $id != 'add' && !is_numeric($id) )
        {
            return view('errors.404');
        }
        elseif( $id != 'add' && is_numeric($id) )
        {
            $params = $request->all();
            $params['id'] = $id;
            $apiResult = MirasApi::request(MirasApi::BUILDING_UPDATE, $params);
            if($apiResult)
            {
                $request->session()->flash('flash_message', 'Изменения сохранены.');
                return redirect()->route('adminBuildingEdit', ['id' => $id]);
            }
        }

        abort(500);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, $id)
    {
        $result = MirasApi::request(MirasApi::BUILDING_DELETE, ['id' => $id]);

        if(!$result)
        {
            $request->session()->flash('flash_message', 'Не удалось удалить запись. Скорее всего есть связанные аудитории.');
        }

        return redirect()->route('adminBuildingList');
    }
}
