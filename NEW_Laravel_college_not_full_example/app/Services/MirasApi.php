<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 30.10.18
 * Time: 11:19
 */

namespace App\Services;


use Illuminate\Support\Facades\Log;

class MirasApi
{
    const BUILDING_LIST         = 'building/list';
    const BUILDING_INFO         = 'building/info';
    const BUILDING_CREATE       = 'building/create';
    const BUILDING_UPDATE       = 'building/update';
    const BUILDING_DELETE       = 'building/delete';

    const STUFF_LIST            = 'stuff/list';

    const ROOM_LIST             = 'room/list';
    const ROOM_INFO             = 'room/info';
    const ROOM_CREATE           = 'room/create';
    const ROOM_UPDATE           = 'room/update';
    const ROOM_DELETE           = 'room/delete';
    const ROOM_RESERVE          = 'room/reserve';
    const ROOM_RESERVE_INFO     = 'room/reserve/info';
    const ROOM_RESERVE_DELETE   = 'room/reserve/delete';
    const ROOM_CHANGE_STUFF     = 'room/reserve/change/stuff';

    /**
     * @param string $command
     * @param array $params
     * @return bool|mixed
     */
    private function getData(string $command, array $params = [])
    {
        if(!env('MIRAS_API_HOST', ''))
        {
            Log::error('.env MIRAS_API_HOST not found' );
            return false;
        }

        if(!env('APP_KEY', ''))
        {
            Log::error('.env APP_KEY not found' );
            return false;
        }

        $url = env('MIRAS_API_HOST', '') . '/' . $command;
        $result = false;

        $params['app_key'] = env('APP_KEY', '');

        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
            $result = curl_exec($curl);
            curl_close($curl);
        }

        try
        {
            $result = json_decode($result);
        }
        catch (\Exception $exception)
        {
            Log::error($exception->getMessage(), ['answer' => $result]);
            return false;
        }

        return $result;
    }

    /**
     * @param string $command
     * @param array $params
     * @return bool
     */
    static function request(string $command, array $params = [])
    {
        $mirasApi = new self();
        $answer = $mirasApi->getData($command, $params);

        if(!isset($answer->status))
        {
            return false;
        }

        if($answer->status == false)
        {
            $message        = isset($answer->message) ? $answer->message : '';
            $answerParams   = isset($answer->params) ? $answer->params : [];
            Log::error('MirasApi error', ['message' => $message, 'params' => $answerParams]);

            return false;
        }

        if($answer->status == true)
        {
            if(isset($answer->response) && (is_array($answer->response) || is_object($answer->response)))
            {
                return $answer->response;
            }

            return true;
        }

        return false;
    }
}