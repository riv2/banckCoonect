<?php

namespace App\Http\Controllers;

use App\User;
use App\Services\Auth;
use Illuminate\Http\Request;
use App\EnterQrCode;
use App\EnterBuilding;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeImage;


class EnterController extends Controller
{

    public function enterQrPage()
    {
        return view('pages.enterByQr');
    }
    public function getEnterQR()
    {
        $code = $this->generateCode();
        
        $qrImage = QrCodeImage::format('png')->size(400)->generate($code);

        return \Response::json([
            'qr' => 'data:image/png;base64,' . base64_encode($qrImage),
            'numeric_code' => $code
        ]);
    }

    public function checkEnterQR(Request $request)
    {
        EnterQrCode::deleteOldQrCode();
        $hash = $request->input('hash');
        $buildingId = $request->input('building_id');
        $direction = $request->input('direction');

        $code = EnterQrCode::where( 'code', $hash )->first(); 

        if (isset($code)) {
            $building = new EnterBuilding;
            $building->door = $direction;
            $building->building_id = $buildingId;
            $building->user_id = $code->user_id;
            $building->save();
            return ['status' => 'success'];    
        }
        return ['status' => 'fail'];
    }

    public function generateCode()
    {
        $user = Auth::user();

        EnterQrCode::deleteOldQrCode();

        do{
            $code = rand(1000000000, 9999999999);
            $enterQrCodeExist = enterQrCode
                ::where('code', $code)
                ->exists();
        } while($enterQrCodeExist);

        $enterQrCode = new EnterQrCode;
        $enterQrCode->code = $code;
        $enterQrCode->user_id = $user->id;
        $enterQrCode->save();

        return $code;
    }

    


    

}