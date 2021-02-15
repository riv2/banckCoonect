<?php

namespace App\Http\Controllers\Student;

use App\QrCode;
use App\Services\Auth;
use App\StudentCheckin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class CheckinController extends Controller
{
    public function qrPage()
    {
        return view('checkin.qr');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function qrCheck(Request $request)
    {
        $codeModel = QrCode::get($request->input('code'));

        if(!$codeModel)
        {
            return Response::json(['message' => __('QR code not found')]);
        }

        $codeMeta = json_decode(base64_decode($codeModel->meta), true);

        if(isset($codeMeta['teacher_id']))
        {
            $checkinModel = StudentCheckin
                ::where('student_id', Auth::user()->id)
                ->where('teacher_id', $codeMeta['teacher_id'])
                ->where('created_at', '>=', date('Y-m-d', time()))
                ->first();

            if(!$checkinModel)
            {
                $checkIn = new StudentCheckin();
                $checkIn->student_id = Auth::user()->id;
                $checkIn->teacher_id = $codeMeta['teacher_id'];
                $checkIn->save();
            }

            return Response::json();
        }

        return Response::json(['message' => __('QR code not found2')]);
    }

    public function numericCodeCheck(Request $request)
    {
        if (empty($request->input('code'))) {
            return Response::json([
                'success' => false,
                'error' => __('Empty code')
            ]);
        } elseif (!is_numeric($request->input('code'))) {
            return Response::json([
                'success' => false,
                'error' => __('Code has to be numeric')
            ]);
        }

        $codeModel = QrCode::getByNumericCode($request->input('code'));

        if (empty($codeModel)) {
            return Response::json([
                'success' => false,
                'error' => __('Numeric code not found or invalid')
            ]);
        }

        $codeMeta = json_decode(base64_decode($codeModel->meta), true);

        if (!empty($codeMeta['teacher_id'])) {
            $checked = StudentCheckin::checkedToday(Auth::user()->id, $codeMeta['teacher_id']);

            if (!$checked) {
                StudentCheckin::add(Auth::user()->id, $codeMeta['teacher_id']);
            }

            return Response::json(['success' => true]);
        }

        return Response::json([
            'success' => true,
            'message' => __('Code not found2')
        ]);
    }
}
