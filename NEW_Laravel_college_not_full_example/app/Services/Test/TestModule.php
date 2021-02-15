<?php

namespace App\Services\Test;

use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Log;

class TestModule
{
    public $userAgent   = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';
    public $cookieFile  = '';
    public $host        = '';
    public $testName    = '';

    private $email      = '';
    private $password   = '';

    public function __construct($testName, $host)
    {
        $this->cookieFile   = storage_path('cookie-' . str_random(15) . '.txt');
        $this->host         = $host;
        $this->testName     = $testName;
    }

    /**
     * @param $url
     * @param string $type
     * @param array $params
     * @return mixed
     */
    public function sendRequest($url, $type = 'get', $params = [], $format = '')
    {
        $url = strpos($url, '://') > 0 ? $url : $this->host . $url;

        $response = Curl::to($url)
            ->withHeader('User-Agent: ' . $this->userAgent)
            ->setCookieFile($this->cookieFile)
            ->setCookieJar($this->cookieFile)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->allowRedirect();

        if($params)
        {
            if($format == 'json')
            {
                $response->withHeader('Content-Type: application/json');
                $response->withData(json_encode($params));
            }
            else
            {
                $response->withData($params);
            }
        }

        $time = time();
        $response = $type == 'get' ? $response->get() : $response->post();

        Log::info($this->testName, [
            'url'       => $url,
            'method'    => $type,
            'response'  => $response->status,
            'email'     => $this->email,
            'time'      => time() - $time . ' sec'
        ]);

        return $response;
    }

    /**
     * @param $login
     * @param $password
     * @param bool $withLogout
     */
    public function login($login, $password, $withLogout = true)
    {
        $this->email    = $login;
        $this->password = $password;

        if($withLogout)
        {
            $this->sendRequest('/logout');
        }

        $this->sendRequest('/login');

        return $this->sendRequest('/login', 'post', [
            'email'     => $this->email,
            'password'  => $this->password
        ]);
    }

    /**
     * @return bool
     */
    public function logout()
    {
        $this->sendRequest('/logout');
        $this->deleteCookieFile();

        return true;
    }

    /**
     * @param $pattern
     * @param $host
     * @param string $type
     * @param array $params
     */
    public function pregMatchAll($pattern, $url, $type = 'get', $params = [])
    {
        $response = $this->sendRequest($url, $type, $params);
        $matches = [];

        if( isset($response->content) )
        {
            preg_match_all($pattern, $response->content, $matches);
        }

        return $matches[0] ?? [];
    }

    public function deleteCookieFile()
    {
        if(\File::exists($this->cookieFile))
        {
            \File::delete($this->cookieFile);
        }
    }

}