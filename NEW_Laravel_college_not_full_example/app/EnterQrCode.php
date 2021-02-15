<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnterQrCode extends Model
{
    protected $table = 'enter_qr_code';


    public static function deleteOldQrCode() 
    {
        $date = time() - 50;
        $date = date('Y-m-d H:i:s', $date);

        EnterQrCode::where( 'created_at', '<=', $date )->delete();
    }

}
