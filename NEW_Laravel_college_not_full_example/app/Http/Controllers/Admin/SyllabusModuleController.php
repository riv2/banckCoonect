<?php

namespace App\Http\Controllers\Admin;

use App\Discipline;
use App\SyllabusModule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Syllabus\ModuleEdit as ModuleEditRequest;
use App\Http\Requests\Syllabus\ModuleStore as ModuleStoreRequest;
use App\Http\Requests\Syllabus\ModuleCreate as ModuleCreateRequest;

class SyllabusModuleController extends Controller
{
    /**
     * @param ModuleCreateRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(ModuleCreateRequest $request) {
        $discipline = Discipline::getById($request->route('disciplineId'));

        return view('admin.pages.syllabus.module.store', [
            'discipline' => $discipline,
            'language' => $request->get('language'),
        ]);
    }

    /**
     * @param ModuleEditRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(ModuleEditRequest $request) {
        $discipline = Discipline::getById($request->route('disciplineId'));
        $module = SyllabusModule::find($request->route('module_id'));

        return view('admin.pages.syllabus.module.store', [
            'discipline'    => $discipline,
            'module'        => $module,
            'language'      => $request->get('language'),
        ]);
    }

    /**
     * @param ModuleStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ModuleStoreRequest $request) {
        if ($request->route('module_id') !== null) {
            $module = SyllabusModule::find($request->route('module_id'));
        } else {
            $module = new SyllabusModule();
        }

        $module->discipline_id  = $request->route('disciplineId');
        $module->name           = $request->get('name');
        $module->language       = $request->get('language');
        $module->save();

        return redirect()->route('admin.syllabus.module.edit', [
            'disciplineId'  => $request->route('disciplineId'),
            'module_id'     => $module->id,
            'language'      => $request->get('language')
        ]);
    }

    /**
     * @param int $module_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($discipline_id, $module_id) {
        SyllabusModule::findOrFail($module_id)->delete();

        return back()->with('messages', [
            [
                'class' => 'alert-success',
                'message' => 'Модуль успешно удален',
            ],
        ]);
    }
}
