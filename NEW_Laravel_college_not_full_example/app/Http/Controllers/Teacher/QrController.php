<?php

namespace App\Http\Controllers\Teacher;

use App\QrCode;
use App\Services\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class QrController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('teacher.qr_generate');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate()
    {
        [$code, $numericCode] = QrCode::generate(['teacher_id' => Auth::user()->id]);

        $qr = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(900)->generate($code);

        return Response::json([
            'qr' => 'data:image/png;base64,' . base64_encode($qr),
            'success' => true,
            'numeric_code' => $numericCode
        ]);
    }
}
