<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Log;
use Storage;
use Validator;
use App\EtxtAnswer;
use App\Services\{Auth, EtxtService};
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class EtxtController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $workTypes = EtxtService::$workTypes;

        return view('admin.pages.etxt.index', compact('workTypes'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTextsOnCheck()
    {
        $texts = EtxtAnswer::where('user_id', Auth::user()->id)
                            ->whereNull('uniq')
                            ->orderBy('id', 'desc')
                            ->paginate(env('ETXT_PAGINATE_ON_CHECK_TEXT', 10));

        $response = [
            'pagination' =>  [
                'current_page' => $texts->currentPage(),
                'last_page' => $texts->lastPage(),
            ],
            'data' => $texts
        ];

        return response()->json($response);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTextSuccess()
    {
        $texts = EtxtAnswer::where('user_id', Auth::user()->id)
                            ->whereNotNull('uniq')
                            ->orderBy('id', 'desc')
                            ->paginate(env('ETXT_PAGINATE_SUCCESS_TEXT', 10));

        $response = [
            'pagination' =>  [
                'current_page' => $texts->currentPage(),
                'last_page' => $texts->lastPage(),
            ],
            'data' => $texts
        ];

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                     => 'required|string|max:255',
            'text'                     => 'required_without:document|nullable|string|max:500000',
            'document'                 => 'required_without:text|mimes:docx',
            'compare_method'           => 'nullable|string',
            'num_samples_per_document' => 'nullable|integer',
            'num_samples'              => 'nullable|integer',
            'num_ref_per_sample'       => 'nullable|integer',
            'num_words_i_shingle'      => 'nullable|integer',
            'uniqueness_threshold'     => 'required|integer',
            'self_uniq'                => 'nullable|string',
            'ignore_citation'          => 'nullable|string'
        ]);

        if ($validator->fails()){
            return redirect()->route('etxtAntiPlagiat')->with(['flash_message' => $validator->errors()->first()]);
        }

        $filePath = null;
        if ($request->hasFile('document')){
            $type = 'file';
            $filePath = $request->file('document')->store('etxt');

            $text = EtxtService::documentParseToString(storage_path('app/' . $filePath));
        } else {
            $type = 'text';
            $text = $request->get('text');
        }

        $etxt = new EtxtService();
        $newEtxtAnswer = new EtxtAnswer();

        $newEtxtAnswer->user_id                  = Auth::user()->id;
        $newEtxtAnswer->name                     = $request->get('name');
        $newEtxtAnswer->type                     = $type;
        $newEtxtAnswer->text                     = $text;
        $newEtxtAnswer->compare_method           = $request->get('compare_method', null);
        $newEtxtAnswer->num_samples_per_document = $request->get('num_samples_per_document', null);
        $newEtxtAnswer->num_samples              = $request->get('num_samples', null);
        $newEtxtAnswer->num_ref_per_sample       = $request->get('num_ref_per_sample', null);
        $newEtxtAnswer->num_words_i_shingle      = $request->get('num_words_i_shingle', null);
        $newEtxtAnswer->uniqueness_threshold     = $request->get('uniqueness_threshold', null);
        $newEtxtAnswer->self_uniq                = $request->has('self_uniq');
        $newEtxtAnswer->ignore_citation          = $request->has('ignore_citation');
        $newEtxtAnswer->save();

        if (isset($filePath)){
            Storage::delete('app/' . $filePath);
        }

        $data = [
            [
                'type'                     => $type,
                'name'                     => $request->get('name'),
                'text'                     => $text,
                'id'                       => $newEtxtAnswer->id,
                'compare_method'           => $request->get('compare_method'),
                'num_samples_per_document' => $request->get('num_samples_per_document'),
                'num_samples'              => $request->get('num_samples'),
                'num_ref_per_sample'       => $request->get('num_ref_per_sample'),
                'num_words_i_shingle'      => $request->get('num_words_i_shingle'),
                'uniqueness_threshold'     => $request->get('uniqueness_threshold'),
                'self_uniq'                => $request->has('self_uniq') ? 'True' : '',
                'ignore_citation'          => $request->has('ignore_citation') ? 'True' : '',
            ]
        ];
        $answer = $etxt->sendRequst($data);

        if (!empty($answer['error'])) {
            return redirect()->route('etxtAntiPlagiat')->with(['flash_message' => $answer['error']]);
        } else {
            $newEtxtAnswer->xml_name = $answer;
            $newEtxtAnswer->save();
        }
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function saveAnswer(Request $request)
    {
        if (!empty($request->get('error'))){
            Log::channel('etxt')->error($request->get('error'));
        }
        if (!empty($request->get('Xml'))){
            Log::channel('etxt')->info($request->get('Xml'));

            $answer = EtxtService::decodeAnswer($request->get('Xml'));

            foreach ($answer->entry as $ans){
                $text = EtxtAnswer::where('id', $ans->id)->first();

                if (!empty($text)) {
                    File::delete(env('ETXT_XML_PATH').$text->exml_name);

                    if (!empty($ans->ftext)){
                        $text->text = base64_decode($ans->ftext);
                        $text->uniq = $ans->ftext['uniq'];
                        $text->save();
                    } else {
                        $text->uniq = 100;
                        $text->save();
                    }
                }
            }
        }
        return response('ok', 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generatePdf(Request $request) {
        $validator = Validator::make($request->all(), [
            'author'    => 'required|string|max:255',
            'text_type' => 'required|in:' . implode(',', array_keys(EtxtService::$workTypes)),
        ]);

        if ($validator->fails()){
            return redirect()->back()->with(['flash_message' => $validator->errors()->first()]);
        }

        $plagiarismChecker = EtxtAnswer::where('user_id', Auth::user()->id)
                                        ->where('id', $request->text_id)
                                        ->whereNotNull('uniq')
                                        ->first();

        if (empty($plagiarismChecker)) {
            return redirect()->back()->with(['flash_message' => 'Sorry, we can\'t find the text.']);
        }

        $fields = [
            'Автор работы' => $request->input('author'),
            'Вид письменной работы' => EtxtService::$workTypes[$request->input('text_type')],
            'Тема работы' => $plagiarismChecker->name,
            'Дата и время проверки' => $plagiarismChecker->created_at->format('d.m.Y H:i:s'),
            'Учетная запись, использованная при проверке' => Auth::user()->email,
            'Процент уникальности работы' => $plagiarismChecker->uniq . '%',
        ];

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.plagiarism', [
            'fields' => $fields,
            'text_id' => $plagiarismChecker->id,
        ]);

        return $pdf->download('plagiarism.pdf');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexPlagiarism()
    {
        return view('admin.pages.plagiarism.index');
    }

    /**
     * @param $etxt_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEtxtResultById($etxt_id)
    {
        $plagiarism = EtxtAnswer::where('id', $etxt_id)
            ->whereNotNull('uniq')
            ->first();

        if (empty($plagiarism)){
            return response()->json(['message' => 'Результат проверкки не найден.'], 422);
        }
        $plagiarismAuthor = User::find($plagiarism->user_id);
        $response = [
            'author' => $plagiarismAuthor->fio,
            'education_program' => $plagiarismAuthor->studentProfile->speciality->name ?? '',
            'work_name' => $plagiarism->name,
            'check_time' => $plagiarism->created_at->format('d.m.Y H:i:s'),
            'account' => $plagiarismAuthor->phone_number,
            'uniqueness_percentage' => $plagiarism->uniq .'%',
            'text' => $plagiarism->text,
            'message' => 'Результат проверки найден.'
        ];
        return response()->json($response);
    }

    /**
     * @param $text_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeText($text_id)
    {
        $text = EtxtAnswer::where('id', $text_id)->whereNull('uniq')->where('user_id', Auth::user()->id);

        if (!empty($text)) {
            $text->delete();
        }

        return back()->with(['success_message' => 'Текст успешно удален.']);
    }
}
