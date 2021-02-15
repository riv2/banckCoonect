<?php

namespace App\Http\Controllers\Admin;

use App\PracticeSpeciality;
use App\Scan;
use Storage;
use App\Practice;
use App\Speciality;
use Yajra\DataTables\DataTables;
use App\Http\Requests\PracticeStore;
use App\Http\Controllers\Controller;

class PracticeController extends Controller
{
    public function index()
    {
        return view('admin.pages.practice.index');
    }

    public function create()
    {
        $specialitiesGroup = [];
        $specialities = Speciality::select(['id', 'year', 'name'])->orderBy('year', 'DESC')->get();

        foreach ($specialities as $speciality) {
            $specialitiesGroup[$speciality['year']][] = $speciality;
        }

        return view('admin.pages.practice.store', compact(
            'specialitiesGroup'
        ));
    }

    public function edit($practice_id)
    {
        $practice = Practice::find($practice_id);

        if (empty($practice)) {
            abort(404);
        }

        $practiceSpecialities = [];
        $specialitiesGroup = [];
        $specialities = Speciality::select(['id', 'year', 'name'])->orderBy('year', 'DESC')->get();

        foreach ($practice->specialities as $practiceSpeciality) {
            $practiceSpecialities[] = $practiceSpeciality->id;
        }

        foreach ($specialities as $speciality) {
            $speciality['selected'] = in_array($speciality['id'], $practiceSpecialities);
            $specialitiesGroup[$speciality['year']][] = $speciality;
        }

        $scans = $practice->scans;
        return view('admin.pages.practice.store', compact(
            'specialitiesGroup',
            'practice',
                'scans'
        ));
    }

    public function store(PracticeStore $request, $practice_id)
    {
        if ($practice_id == 0) {
            $practice = new Practice();
        } else {
            $practice = Practice::find($practice_id);
        }

        $practice->organization_name            = $request->organization_name;
        $practice->organization_activity_type   = $request->organization_activity_type;
        $practice->contract_number              = $request->contract_number;
        $practice->contract_start_date          = $request->contract_start_date;
        $practice->contract_end_date            = $request->contract_end_date;
        $practice->capacity                     = $request->capacity;
        $practice->save();

        if (!empty($request->file('scan'))){
            foreach ($request->file('scan') as $file) {
                $scan = new Scan();

                $scan->add($practice->id, $file);
            }
        }
        $practice->specialities()->sync($request->specialities);
        
        return redirect()->route('admin.practice.edit.show', ['practice_id' => $practice->id]);
    }

    public function all()
    {
        $practices = Practice::all();

        return Datatables::of($practices)
            ->addColumn('action', function($practice) {
                return '<a href="' . route('admin.practice.edit.show', ['practice_id' => $practice->id]) . '" class="btn btn-default">
                            <i class="md md-edit"></i>
                        </a>
                        <a href="' . route('admin.practice.remove', ['practice_id' => $practice->id]) . '" class="btn btn-default">
                            <i class="md md-remove"></i>
                        </a>';
            })
            ->removeColumn('id')
            ->make(true);
    }

    public function remove($practice_id)
    {
        Practice::find($practice_id)->delete();
        PracticeSpeciality::where('practice_id', $practice_id)->delete();

        return redirect()->route('admin.practice.show');
    }

    public function getScan($file)
    {
        $scan = Scan::where('file_name' , $file)->first();
        if (empty($scan)){
            abort(404);
        }
        return Storage::download('practice/'. $file . '.' . $scan->extension);
    }

    public function deleteScan($file)
    {
        $scan = Scan::where('file_name' , $file)->first();
        if (empty($scan)){
            abort(404);
        }
        Storage::delete(Practice::$filePath . DIRECTORY_SEPARATOR . $file. '.'. $scan->extension);

        $scan->delete();

        return redirect()->back();
    }
}
