<?php

namespace App\Http\Controllers\Teacher;

use App\QrCode;
use App\Services\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Response;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use App\Discipline;
use App\QuizAnswer;
use App\QuizQuestion;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeImage;

class QuizController extends Controller
{

    public function qr()
    {
        // FIXME temporary
        if (!in_array(Auth::user()->id, QrCode::$adminTeachers)) {
            abort(404);
        }

//        $disciplines = Auth::user()->teacherDisciplines;
        $disciplines = Discipline::orderBy('name')->get();

        return view('teacher.qr', compact('disciplines'));
    }

    public function getQR(Request $request)
    {
        // FIXME temporary
        if (!in_array(Auth::user()->id, QrCode::$adminTeachers)) {
            abort(404);
        }

        if (empty($request->input('disciplineID'))) {
            return Response::json(['success' => false, 'error' => 'Discipline ID required']);
        }

        [$code, $numericCode] = QrCode::generateCode(Auth::user()->id, $request->input('disciplineID'));
        $qrImage = QrCodeImage::format('png')->size(400)->generate($code);
        
        $disciplineName = Discipline::getLocaleNameById($request->input('disciplineID'));

        return Response::json([
            'success' => true,
            'qr' => 'data:image/png;base64,' . base64_encode($qrImage),
            'discipline_name' => $disciplineName,
            'numeric_code' => $numericCode
        ]);
    }

//    public function __construct()
//    {
//        if (!\App\User::getCurrentRole() == \App\Role::NAME_TEACHER
//            && !\App\User::getCurrentRole() == \App\Role::NAME_ADMIN) {
//            return redirect()->back();
//        }
//    }
//
//    public function quezethemes()
//    {
//        $allDisciplines = Discipline::orderBy('id')->get();
//
//        return view('teacher.quezeThemes', compact('allDisciplines'));
//    }
//
//    public function list($disciplineId)
//    {
//        $allQuestions = QuizQuestion::orderBy('id')
//            ->where('discipline_id', $disciplineId)
//            ->get();
//
//        return view('teacher.questions', compact('allQuestions', 'disciplineId'));
//    }
//
//    public function add($disciplineId)
//    {
//        $answers = [1];
//        $questionId = 'new';
//
//        return view('teacher.addeditQuestion', compact('answers', 'disciplineId', 'questionId'));
//    }
//
//    public function addedit($disciplineId, $questionId, Request $request)
//    {
//        $data = \Input::except(array('_token'));
//
//        $inputs = $request->all();
//
//        $rule = [
//            'question' => 'required',
//            'answer_1' => 'required'
//        ];
//
//        $validator = \Validator::make($data, $rule);
//
//        if ($validator->fails()) {
//            return redirect()->back()->withErrors($validator->messages());
//        }
//
//        //dd($inputs);
//
//        if (!empty($inputs['id'])) {
//            $question = QuizQuestion::findOrFail($inputs['id']);
//        } else {
//            $question = new QuizQuestion;
//        }
//
//        $question->discipline_id = $disciplineId;
//        $question->teacher_id = 0;
//        $question->total_points = 0;
//        $question->question = $inputs['question'];
//
//        $question->save();
//        /*
//       //Question image
//       $question_image = $request->file('image');
//
//       if($question_image){
//
//           \File::delete(public_path() .'/images/uploads/questions/'.$question->image.'-b.jpg');
//           \File::delete(public_path() .'/images/uploads/questions/'.$question->image.'-s.jpg');
//
//           $tmpFilePath = 'images/uploads/questions/';
//
//           $hardPath =  str_slug($inputs['name'], '-').'-'.md5(time());
//
//           $img = Image::make($question_image);
//
//           $img->fit(1240, 552)->save($tmpFilePath.$hardPath.'-b.jpg');
//           $img->fit(252, 152)->save($tmpFilePath.$hardPath. '-s.jpg');
//
//           $question->image = $hardPath;
//       }
//       */
//        $question = QuizQuestion::findOrFail($question->id);
//        $totalPoints = 0;
//
//        QuizAnswer::where('question_id', $question->id)->delete();
//
//        for ($i = 1; $i <= $inputs['count']; $i++) {
//
//            $answer = new QuizAnswer;
//
//            if (empty($inputs['answer_' . $i]) OR $inputs['answer_' . $i] == '<br>') continue;
//
//            $answer->answer = $inputs['answer_' . $i];
//
//            if (!empty($inputs['correct_' . $i]) AND $inputs['correct_' . $i] == 'on') {
//                $answer->points = $inputs['points_' . $i];
//                $totalPoints += $answer->points;
//            } else {
//                $answer->points = 0;
//            }
//            $answer->question_id = $question->id;
//
//            $answer->save();
//        }
//
//        $question->total_points = $totalPoints;
//        $question->save();
//
//        if (!empty($inputs['id'])) {
//
//            \Session::flash('flash_message', 'Changes Saved');
//
//        } else {
//
//            \Session::flash('flash_message', 'Added');
//
//        }
//
//        return redirect()->route('teacherQuestions', [$disciplineId]);
//    }
//
//    public function edit($disciplineId, $questionId)
//    {
//        $question = QuizQuestion::findOrFail($questionId);
//        $answers = QuizAnswer::where('question_id', $questionId)->get();
//
//        return view('teacher.addeditQuestion', compact('question', 'answers', 'disciplineId', 'questionId'));
//    }
//
//    public function delete($id)
//    {
//        $question = QuizQuestion::findOrFail($id);
//
//        //\File::delete(public_path() .'/images/uploads/questions/'.$question->img.'-b.jpg');
//        //\File::delete(public_path() .'/images/uploads/questions/'.$question->img.'-s.jpg');
//
//        $question->delete();
//
//        QuizAnswer::where('question_id', $id)->delete();
//
//        \Session::flash('flash_message', 'Deleted');
//
//        return redirect()->back();
//    }
}
