<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Carbon\Carbon;
use App\{
    EmployeesPosition,
    NomenclatureFolder,
    NomenclatureFolderTemplate,
    NomenclatureFileVoteUsers,
    NomenclatureTemplateFiles,
    Validators\NomenclatureNewTemplateFileValidator
};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Validators\NomenclatureFileValidator;

class NomenclatureController extends Controller
{
    public function index($years = null){
        $auditUser = 0;
        $data = new NomenclatureFolder;
        if(!$years){
            $years = '2019-2020';
        }
        $folders = $data->ctreateOuptutFolders($years);
        $positions = EmployeesPosition::select('id', 'name')->get()->toArray();
        $checkAudit = Auth::user()->hasRole('auditor');
        if($checkAudit){
            $auditUser = 1;
        }

        return view(
            'admin.pages.nomenclature.index',
            compact('folders', 'positions', 'auditUser', 'authPositions', 'years')
        );
    }

    public function addFolder(Request $request){
        NomenclatureFolder::create([
            'parent_id' => $request['parent_id'],
            'name' => $request['name'],
            'status' => 'new',
            'years' => $request['years']?? ''
        ]);

        return back();
    }

    public function getFolderContent(Request $request){
        $folder = NomenclatureFolder::where('years', $request['years'])->where('id', $request['id'])->first();
        $templates = $folder->templates()->with('files')->with('files.votesList')->get();
        $authPositions = Auth::user()->positions()->pluck('position_id')->toArray();
        $showVote = [];

        foreach($templates as $template){
            if(count($template->files) == 0 && !$template['load_date']->gt(Carbon::now())){
                $template['status'] = NomenclatureFolder::$statuses['expired_date'];
            } elseif(count($template->files) == 0 && $template['load_date']->gt(Carbon::now())){
                $template['status'] = NomenclatureFolder::$statuses['new'];
            }
            foreach ($template['files'] as $file){
                foreach($file['votesList'] as $vote){
                    if(in_array($vote['position_id'], $authPositions)){
                        array_push($showVote, $file['id']);
                    }
                }
                if($file->name != null){
                    $template['status'] = NomenclatureFolder::$statuses['has_files'];
                }
            }
        }

        $folder['status'] = NomenclatureFolder::$statuses[$folder['status']];

        return response()->json(['status' => 'success', 'folder' => $folder, 'templates' => $templates, 'votes' => $showVote]);
    }

    public function addFileToFolder(Request $request){
        $validator = NomenclatureFileValidator::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        $array = [
            'folder_id' => $request['folder_id'],
            'name' => $request['name'],
            'load_date' => $request['load_date'],
        ];

        $nomenclature = new NomenclatureFolderTemplate();

        if($request->has('template')){
            $array['template'] = $nomenclature->saveFile($request['template']);
        }

        $nomenclatureRecord = NomenclatureFolderTemplate::create($array);

        foreach ($request['votes_list'] as $position) {
            NomenclatureFileVoteUsers::create([
                'file_id' => $nomenclatureRecord->id,
                'position_id' => $position
            ]);
        }

        return back();
    }

    public function downloadFile($fileName){
        $file = new NomenclatureFolderTemplate();

        $path = $file->getFile($fileName);

        return response()->download($path);
    }

    public function uploadFile(Request $request){
        $nomenclatureTemplate = NomenclatureFolderTemplate::where('id', $request['template_id'])->first();

        if($request->has('files')){
            foreach($request['files'] as $file){
                $fileName = $nomenclatureTemplate->saveFile($file);

                NomenclatureTemplateFiles::create([
                    'template_id' => $nomenclatureTemplate->id,
                    'name' => $fileName
                ]);
            }
            $nomenclatureTemplate->update([
                'isset_files' => true
            ]);
        }

        if(!empty($nomenclatureTemplate->files())){
            $nomenclatureTemplate->folder()->update([
                'status' => 'has_files'
            ]);
        }

        return response()->json(['status' => 'success', 'folder_id' => $nomenclatureTemplate['folder_id']]);
    }

    public function vote(Request $request){
        $nomenclatureFile = NomenclatureTemplateFiles::where('id', $request['id'])->first();
        $nomenclatureFile->update([
            'checked' => true
        ]);
        $files = $nomenclatureFile->template->files()->pluck('checked')->toArray();
        $folder = $nomenclatureFile->template->folder;
        if(!in_array('false', $files, true)){
            $folder->update([
                'status' => 'has_agreement'
            ]);
        }


        return response()->json(['status' => 'success', 'folder_id' => $folder['id']]);
    }

    public function auditorCheck(Request $request){
        $folder = NomenclatureFolder::where('id', $request['id'])->first();
        $folder->update([
            'status' => 'has_auditor_agreement'
        ]);

        return response()->json(['status' => 'success', 'folder_id' => $folder->id]);
    }

    public function deleteFolder($id){
        $folder = NomenclatureFolder::find($id);
        $templates = $folder->templates;

        foreach($templates as $template){
            foreach($template->files as $file){
                $file->votesList()->delete();
                $file->delete();
            }
            $template->delete();
        }

        $folder->delete();

        return back();
    }

    public function deleteTemplate($id){
        $template = NomenclatureFolderTemplate::find($id);

        foreach($template->files as $file){
            $file->votesList()->delete();
            $file->delete();
        }
        $template->delete();

        return back();
    }

    public function deleteFile($fileName){
        $file = new NomenclatureFolderTemplate();
        $file->deleteFile($fileName);

        return back();
    }

    public function editName(Request $request){
        if($request['nameType'] == 'folder'){
            $folder = NomenclatureFolder::find($request['nameId']);
            $folder->update([
                'name' => $request['name']
            ]);
        } else {
            $template = NomenclatureFolderTemplate::find($request['nameId']);
            $template->update([
                'name' => $request['name']
            ]);
            $folder = $template->folder;
        }

        return response()->json(['status' => 'success', 'folder_id' => $folder['id']]);
    }

    public function uploadTemplateFile(Request $request) {
        $validator = NomenclatureNewTemplateFileValidator::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        $nomenclature = NomenclatureFolderTemplate::where('id', $request['template_id'])->first();
        if($request->has('new_template_file')){

            $file = $request['new_template_file'];
            $fileName = time().'_'.$file->getClientOriginalName();

            $file->move(storage_path('app/nomenclature/files/'), $fileName);
            $oldFileName = storage_path('app/nomenclature/files/'). $nomenclature->template;
            if (!is_null($nomenclature->template) && file_exists($oldFileName)) unlink($oldFileName);

            $nomenclature->template = $fileName;
            $nomenclature->save();
        }

        response()->json(['status' => 'success', 'folder_id' => $nomenclature->folder_id]);
    }
}
