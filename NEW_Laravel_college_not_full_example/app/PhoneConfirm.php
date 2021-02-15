<?php

namespace App;

use App\Services\SmsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PhoneConfirm extends Model
{
    protected $table = 'phone_confirm';

    public function sendSms()
    {
        $message = 'MirasEducation code ' . $this->code;

        return SmsService::send($this->phone_number, $message);
    }

    static function checkCode($phone, $code)
    {
        $code = self
            ::where('phone_number', 'like', '%' . substr($phone, 2))
            ->where('code', $code)
            ->where('confirm', false)
            ->first();

        if($code)
        {
            $code->confirm = true;
            $code->save();

            return true;
        }

        return false;
    }
}
