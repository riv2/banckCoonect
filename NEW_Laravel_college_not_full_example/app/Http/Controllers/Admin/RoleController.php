<?php

namespace App\Http\Controllers\Admin;

use App\ProjectSection;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList()
    {
        $roleList = Role
            ::where('id', '>', 3)
            ->get();

        return view('admin.pages.role.list', ['roleList' => $roleList ?? []]);
    }

    /**
     * @param $trendId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($roleId)
    {
        $role = Role::where('id', $roleId)->first();

        if (!$role && $roleId == 'add') {
            $role = new Role();
        } elseif ($roleId != 'add' && !is_numeric($roleId)) {
            abort(404);
        }

        $sectionAdminList = ProjectSection
            ::where('project', ProjectSection::PROJECT_ADMIN)
            ->get();

        return view(
            'admin.pages.role.edit',
            [
                'role' => $role,
                'sectionAdminList' => $sectionAdminList
            ]
        );
    }

    /**
     * @param Request $request
     * @param $trendId
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editPost(Request $request, $roleId)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'title_ru' => 'required'
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        $role = Role::where('id', $roleId)->first();

        if (!$role && $roleId == 'add') {
            $role = new Role();
        } elseif ($roleId != 'add' && !is_numeric($roleId)) {
            return view('errors.404');
        }

        $role->fill($request->all());
        $role->can_set_pay_in_orcabinet = $request->input('can_set_pay_in_orcabinet', false);
        $role->can_upload_student_docs = $request->input('can_upload_student_docs', false);
        $role->can_create_student_comment = $request->input('can_create_student_comment', false);
        $role->can_add_aditional_service_to_user = $request->input('can_add_aditional_service_to_user', false);
        $role->save();
        $role->syncRights($request->input('sectionRights', []));

        $request->session()->flash('flash_message', 'Изменения сохранены.');
        return redirect()->route('adminRoleEdit', ['id' => $role->id]);
    }

    /**
     * @param $trendId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($roleId)
    {
        $role = Role::where('id', $roleId)->first();

        if (!$role) {
            abort(404);
        }

        $role->delete();

        return redirect()->route('adminRoleList');
    }
}
