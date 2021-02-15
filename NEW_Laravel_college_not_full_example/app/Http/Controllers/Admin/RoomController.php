<?php

namespace App\Http\Controllers\Admin;

use App\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MirasApi;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function getList(Request $request)
    {
        $roomList = MirasApi::request(MirasApi::ROOM_LIST);

        return view('admin.pages.rooms.list', ['roomList' => $roomList ?? []]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $buildingList = MirasApi::request(MirasApi::BUILDING_LIST);
        $stuffList    = MirasApi::request(MirasApi::STUFF_LIST);

        if($id == 'add')
        {
            return view('admin.pages.rooms.edit', [
                'buildingList' => $buildingList,
                'stuffList'    => $stuffList
            ]);
        }
        elseif( $id != 'add' && !is_numeric($id) )
        {
            return view('errors.404');
        }

        $room = MirasApi::request(MirasApi::ROOM_INFO, ['id' => $id]);

        if(!$room)
        {
            return view('errors.404');
        }

        $room->stuffIds = Room::getStuffIdList($room->stuff);

        return view('admin.pages.rooms.edit', [
            'room' => $room,
            'buildingList' => $buildingList,
            'stuffList' => $stuffList
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
            'building_id'    => 'required',
            'type'           => ['required', Rule::in([
                'lecture',
                'computing',
                'laboratory_chemical',
                'laboratory_bio',
                'laboratory_physical',
                'sport',
                'multimedia'
            ])],
            'number'         => 'required|max:250',
            'floor'          => 'required|integer',
            'seats_count'    => 'required|integer',
            'conditioner'    => 'required|boolean',
            'cost'           => 'required|numeric|min:10',
            'status'         => ['required', Rule::in(['active', 'block'])],
            'stuff'          => 'nullable|array'
        ];

        $validator = \Validator::make($request->all(), $ruleList);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages());
        }

        $params = $request->all();
        $params['stuff'] = Room::compareStuff($request->input('stuff'));

        if($id == 'add')
        {
            $apiResult = MirasApi::request(MirasApi::ROOM_CREATE, $params);
            if(isset($apiResult->id))
            {
                $request->session()->flash('flash_message', 'Изменения сохранены.');
                return redirect()->route('adminRoomEdit', ['id' => $apiResult->id]);
            }
        }
        elseif( $id != 'add' && !is_numeric($id) )
        {
            return view('errors.404');
        }
        elseif( $id != 'add' && is_numeric($id) )
        {
            $params['id'] = $id;
            $apiResult = MirasApi::request(MirasApi::ROOM_UPDATE, $params);

            if($apiResult)
            {
                $request->session()->flash('flash_message', 'Изменения сохранены.');
                return redirect()->route('adminRoomEdit', ['id' => $id]);
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
        MirasApi::request(MirasApi::ROOM_DELETE, ['id' => $id]);

        return redirect()->route('adminRoomList');
    }
}
