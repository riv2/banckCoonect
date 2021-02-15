<?php

namespace App\Http\Controllers\Admin;

use App\Profiles;
use App\Services\SearchCache;
use App\User;
use App\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class GuestController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminList()
    {
        return view('admin.pages.guests.list');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getAdminListAjax(Request $request)
    {
        $searchData = User::getGuestListForAdmin(
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
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if($user) {
            Profiles::where('user_id', $id)->delete();
            $user->forceDelete();
            UserRole::where('user_id', $id)->forceDelete();

            SearchCache::delete(User::$adminRedisGuestTable, $id);
        }

        \Session::flash('flash_message', 'Deleted');

        return redirect()->back();
    }
}
