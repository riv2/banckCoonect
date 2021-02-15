<?php

namespace App\Http\Controllers\Student;

use Storage;
use Validator;
use Carbon\Carbon;
use App\EtxtAnswer;
use App\Services\{
    Auth,
    EtxtService
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class PlagiarismController extends Controller
{
    public function show() {
        $answersToday = EtxtAnswer::where('user_id', Auth::user()->id)
                            ->whereYear('created_at', Carbon::now()->format('Y'))
                            ->whereMonth('created_at', Carbon::now()->format('m'))
                            ->whereDay('created_at', Carbon::now()->format('d'))
                            ->count();

        $workTypes = EtxtService::$workTypes;

        return view('student.plagiarism', compact('answersToday', 'workTypes'));

    }

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
            return redirect()->back()->withErrors($validator->errors()->messages());
        }

        $answersToday = EtxtAnswer::where('user_id', Auth::user()->id)
                        ->whereYear('created_at', Carbon::now()->format('Y'))
                        ->whereMonth('created_at', Carbon::now()->format('m'))
                        ->whereDay('created_at', Carbon::now()->format('d'))
                        ->count();

        if($answersToday > 0) {
            return redirect()->back()->with([
                'message' => __('Notice: You can check only 1 text per day'),
                'alert-class' => 'alert-error',
            ]);
        }
        $filePath = null;
        if ($request->hasFile('document')){
            $type = 'file';
            $filePath = $request->file('document')->store('etxt');

            $documentText = EtxtService::documentParseToString(storage_path('app/' . $filePath));
            $text = $documentText;
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
            return redirect()->back()->with([
                'message' => $answer['error'],
                'alert-class' => 'alert-error',
            ]);
        } else {
            $newEtxtAnswer->xml_name = $answer;
            $newEtxtAnswer->save();
        }

        return redirect()->back();
    }

    public function generatePdf(Request $request) {
        $validator = Validator::make($request->all(), [
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
            __('Work Author') => Auth::user()->fio,
            __('Educational program') => Auth::user()->studentProfile->speciality->name,
            __('Work Type') => EtxtService::$workTypes[$request->input('text_type')],
            __('Work theme') => $plagiarismChecker->name,
            __('Check time') => $plagiarismChecker->created_at->format('d.m.Y H:i:s'),
            __('Account') => Auth::user()->phone_number,
            __('Uniqueness Percentage') => $plagiarismChecker->uniq . '%',
        ];

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.plagiarism', [
            'fields' => $fields,
            'text_id' => $plagiarismChecker->id,
        ]);

        return $pdf->download('plagiarism.pdf');
    }

    public function delete($text_id) {
        $text = EtxtAnswer::where('id', $text_id)->whereNull('uniq')->where('user_id', Auth::user()->id);

        if (!empty($text)) {
            $text->delete();
        }

        return redirect()->back()->with(['success_message' => 'Текст успешно удален.']);
    }
}
