<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\{
    Discipline,
    LibraryReport,
    LibraryVisitStatistic,
    LibraryKnowledgeSection,
    LibraryLiteratureCatalog,
    LibraryCatalogDiscipline,
    LibratyCatalogKnowledgeSection
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Services\LibrarySaveFile;
use App\Http\Controllers\Controller;
use App\Validators\LibraryNewLiteratureCatalogValidator;

class LibraryController extends Controller
{
    public function index(){
    	return view('admin.pages.library.index');
    }

    public function catalogDatatable(Request $request){
    	$records = LibraryLiteratureCatalog::all();

    	return Datatables::of($records)
            ->addColumn('action', function($record){
                return '<a href="'.route("add.literature.to.catalog.page", ["id" => $record->id]).'">
                            <button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Редактировать">
                                <i class="md md-edit"></i>
                            </button>
                        </a>
                        <a href="'.route("library.delete.catalog", ["id" => $record->id]).'">
                            <button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Редактировать">
                                <i class="md md-delete"></i>
                            </button>
                        </a>';
            })
            ->make(true);
    }

    public function addLiteraturePage($id = null){
        $literature = null;

        if($id){
            $literature = LibraryLiteratureCatalog::find($id);
        }

        $knowledge_section = LibraryKnowledgeSection::all();

    	return view('admin.pages.library.add_literature', compact('knowledge_section', 'literature'));
    }

    public function disciplineDatatable(Request $request){
        $records = Discipline::all();
        $disciplines = null;

        if(isset($request->id) && $request->id != 0){
            $literature = LibraryLiteratureCatalog::find($request->id);
            $disciplines = $literature->disciplines;
        }

        return Datatables::of($records)
            ->addColumn('action', function($record) use ($disciplines){
                $checked = '';
                if(isset($disciplines) && in_array($record->id, $disciplines->pluck('id')->toArray())){
                    $checked = 'checked';
                }

                return '<div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                value="'.$record->id.'" 
                                name="discipline[]" 
                                id="defaultCheck'.$record->id.'" 
                                '.$checked.'
                            >
                            <label class="form-check-label" for="defaultCheck'.$record->id.'">
                                Выбрать
                            </label>
                        </div>';
            })
            ->make(true);
    }

    public function addLiterature(Request $request){
        $validator = LibraryNewLiteratureCatalogValidator::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        $catalog = $request->catalog;
        
        if(isset($request->catalog['e_books_name'])){
            $file = new LibrarySaveFile();
            $fileName = $file->saveFile($request->catalog['e_books_name']);
            $catalog['e_books_name'] = $fileName;
        }

        $catalog['publication_year'] = date('Y', strtotime($catalog['publication_year']));
        $catalogRecord = LibraryLiteratureCatalog::create($catalog);

        foreach ($request->knowledge_section as $value) {
            LibratyCatalogKnowledgeSection::create([
                'literature_catalog_id' => $catalogRecord->id,
                'knowledge_section_id'  => $value
            ]);
        }

        if(isset($request->discipline)){
            foreach ($request->discipline as $value) {
                LibraryCatalogDiscipline::create([
                    'literature_catalog_id' => $catalogRecord->id,
                    'discipline_id'         => $value
                ]);
            }
        }

        return redirect()->route('library.page');
    }

    public function editLiterature(Request $request){
        $validator = LibraryNewLiteratureCatalogValidator::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        $catalog = $request->catalog;
        $literature = LibraryLiteratureCatalog::find($request->literature_id);

        if(isset($request->catalog['e_books_name'])){
            $file = new LibrarySaveFile();
            $fileName = $file->saveFile($request->catalog['e_books_name']);
            $catalog['e_books_name'] = $fileName;
        }

        $catalog['publication_year'] = date('Y', strtotime($catalog['publication_year']));
        $literature->update($catalog);

        LibratyCatalogKnowledgeSection::where('literature_catalog_id', $literature->id)->delete();
        foreach ($request->knowledge_section as $value) {
            LibratyCatalogKnowledgeSection::create([
                'literature_catalog_id' => $literature->id,
                'knowledge_section_id'  => $value
            ]);
        }

        LibraryCatalogDiscipline::where('literature_catalog_id', $literature->id)->delete();
        if(isset($request->discipline)){
            foreach ($request->discipline as $value) {
                LibraryCatalogDiscipline::create([
                    'literature_catalog_id' => $literature->id,
                    'discipline_id'         => $value
                ]);
            }
        }

        return redirect()->route('library.page')->with('literature_success_add', 'Запись успешно сохранена');
    }

    public function knowledgeSectionPage(){
        return view('admin.pages.library.knowledge_section');
    }

    public function addRecordToKnowledgeSection(Request $request){
        LibraryKnowledgeSection::create([
            'name' => $request->name
        ]);

        return redirect()->back()->with('knowledge_section_success_add', 'Запись добавлена успешно');
    }

     public function liveSearch(Request $request){
        $literature = LibraryLiteratureCatalog::where('name', 'like', '%'.$request['search'].'%')
            ->orWhere('author', 'like', '%'.$request['search'].'%')
            ->select('id', 'name', 'author', 'publication_year')
            ->get()
            ->toArray();

        return response()->json(['literature' => $literature]);
    }

    public function statisticPage(){
        return view('admin.pages.library.statistic');
    }

    public function statisticChart(){
        setlocale(LC_TIME, 'ru_RU');
        $visits = LibraryVisitStatistic::whereBetween('created_at', [ Carbon::now()->subYears(10)->toDateTimeString(), Carbon::now()->toDateTimeString() ])
                                    ->select(
                                        DB::raw('count(*) as count'), 
                                        DB::raw('DATE_FORMAT(created_at, "%Y-%m") as date')
                                    )
                                    ->groupBy('date')
                                    ->orderBy('date', 'desc')
                                    ->get()
                                    ->toArray();

        foreach($visits as $visit){
            $array[] = [Carbon::parse($visit['date'])->formatLocalized('%b'), $visit['count']];
        }
        $array[] = ['Месяц', 'Посещения'];
        $array = array_reverse($array);
        
        return response()->json(['chart' => $array]);
    }

    public function downloadsStatisticDatatable(Request $reqeust){
        $records = LibraryReport::where('action_type', 'download')->get();

        return Datatables::of($records)
            ->addColumn('name', function($record){
                return $record->user->name;
            })
            ->make(true);
    }

    public function reportsPage(){
        return view('admin.pages.library.reports');
    }

    public function reportsDatatable(Request $request){
        $records = LibraryReport::where('action_type', 'order')->get();

        return Datatables::of($records)
            ->addColumn('name', function($record){
                return $record->user->name;
            })
            ->addColumn('status', function($record){
                return LibraryReport::$statuses[$record->status];
            })
            ->addColumn('action', function($record){
                $str = '';
                switch ($record->status) {
                        case 'order':
                            return '<a href="'.route("library.report.set.status", ["id" => $record->id, "status" => "pending"]).'">
                                        <button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Литература выдана">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </a>';
                            break;
                        
                        case 'pending':
                            return '<a href="'.route("library.report.set.status", ["id" => $record->id, "status" => "returned"]).'">
                                        <button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Литература возвращена">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </a>';
                            break;
                        
                        case 'returned':
                            return '';
                            break;
                        
                        default:
                            return '';
                            break;
                    }
                return $str;
            })
            ->make(true);
    }

    public function setStatus($id, $status){
        LibraryReport::where('id', $id)->update([
            'status' => $status
        ]);

        return back();
    }

    public function downloadFile($fileName){
        $file = new LibrarySaveFile();

        $path = $file->getFile($fileName);

        return response()->download($path);
    }

    public function deleteCatalog($id){
        LibraryLiteratureCatalog::where('id', $id)->delete();

        return back();
    }
}
