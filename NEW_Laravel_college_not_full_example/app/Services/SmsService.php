<?php


namespace App\Services;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SmsService
{
    /**
     * @param $phoneNumber
     * @param $message
     * @return false|string
     */

    const KAZ_CODE_LIST = [
        '701',
        '702',
        '705',
        '777',
        '712',
        '713',
        '717',
        '718',
        '721',
        '725',
        '726',
        '727',
        '700',
        '701',
        '702',
        '707',
        '700',
        '747',
        '774',
        '764',
        '771',
        '776',
        '763',
        '775',
        '778',
        '760',
        '708',
        '764'
    ];

    static function send($phoneNumber, $message)
    {
        if (env('API_SMS_EMULATED', false)) {
            Session::put('sms_was_sent', ['number' => $phoneNumber, 'message' => $message]);
            return true;
        }

        $phoneNumber = preg_replace('~[^0-9\+]+~', '', $phoneNumber);

        if (strlen($phoneNumber) == 11) {
            $phoneNumber = '+7' . substr($phoneNumber, 1, 10);
        }

        if (self::isKazNumber($phoneNumber)) {
            $answer = self::viaKannel($phoneNumber, $message);
        } else {
            $answer = self::viaDevice($phoneNumber, $message);
        }

        return $answer;
    }

    /**
     * @param $phoneNumber
     * @param $message
     */
    static function viaKannel($phoneNumber, $message)
    {
        $params = [
            'user' => 'user',
            'pass' => '1q2w3e4r',
            'to' => $phoneNumber,
            'coding' => 0,
            'text' => $message
        ];

        $url = env('KANNEL_HOST') . '/cgi-bin/sendsms?' . http_build_query($params);
        $answer = file_get_contents($url);

        Log::info('Send sms', ['type'=>'kannel', 'url' => $url, 'answer' => $answer]);

        return $answer;
    }

    static function viaDevice($phoneNumber, $message)
    {
        $params = [
            'username' => 'smsmiras',
            'password' => '1Q2w.miras_sms',
            'to' => $phoneNumber,
            'coding' => 0,
            'smsc' => 'huawei',
            'from' => 'Miras',
            'text' => $message
        ];

        //$url = 'http://2.135.220.22:13003/cgi-bin/sendsms?' . http_build_query($params);
        $url = 'http://188.127.37.12:13003/cgi-bin/sendsms?' . http_build_query($params);

        $answer = file_get_contents($url);

        Log::info('Send sms', ['type'=>'device', 'url' => $url, 'answer' => $answer]);

        return $answer;
    }

    /**
     * @param $phoneNumber
     * @return bool
     */
    static function isKazNumber($phoneNumber)
    {
        preg_match('/^(\+7|8)(' . implode('|', self::KAZ_CODE_LIST) . ')/', $phoneNumber, $matches);

        return (bool)count($matches);
    }
}