<?php

namespace App\Services;

class NOBDApi
{
	const LOGIN 		 = '/login';
	const IMPORT_STUDENT = '/api/public/passport/student/import';

	public function auth($login, $pass, $url){
		$client = new \GuzzleHttp\Client();

        try {
            $authData = ['username' => $login, 'password' => $pass];
            $responseAUTH = $client->request('POST', $url.self::LOGIN, ['verify' => false, 'headers' =>['content-type' => 'application/json'],'body' => json_encode($authData)]);
            $responseData = json_decode($responseAUTH->getBody()->getContents(), true);

            return $responseData;
        } catch (Exception $e) {
            return false;
        }
	}

	public function importStudent($data, $login, $pass, $url){
		$responseData = $this->auth($login, $pass, $url);

		if($responseData == false){
			return false;
		}

		if($responseData['status'] = 'SUCCESS'){
            $client = new \GuzzleHttp\Client();
            foreach ($data as $key => $value) {
                try {
                    $response = $client->request(
                        'POST',
                        $url.self::IMPORT_STUDENT,
                        [
                            'headers' =>
                                [
                                    'Authorization' => "Bearer ".$responseData['accessToken']."",
                                    'content-type' => 'application/json'
                                ],
                            'body' => json_encode($value),
                            'verify' => false
                        ]
                    );
                } catch (Exception $e) {
                    return false;
                }
            }

            return true;
        }else{
            return false;
        }
	}
}