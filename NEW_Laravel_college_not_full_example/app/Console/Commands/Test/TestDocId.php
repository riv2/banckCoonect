<?php

namespace App\Console\Commands\Test;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class TestDocId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:profile:create {--email=null} {--from=1} {--to=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public $host = 'https://miras.app';
    //public $host = 'http://miras.loc';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->option('email');
        $from = (int)$this->option('from');
        $to = (int)$this->option('to');

        if($email == 'null')
        {
            $this->runList($from, $to);
        }
        else
        {
            $this->runSingle($email);
        }
    }

    public function runList($from, $to)
    {
        $email = 'auto_test_#i@mail.ru';

        for($i = $from; $i <= $to; $i++)
        {
            exec('php artisan test:profile:create --email=' . str_replace('#i', $i, $email . ' > /dev/null 2>/dev/null &'));
        }
    }

    public function runSingle($email)
    {
        $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';
        $cookieFile = storage_path('cookie-' . $email . '.txt');

        /*Logout*/
        $url = $this->host . '/logout';
        $response = Curl::to($url)
            ->withHeader('User-Agent: ' . $userAgent)
            ->setCookieFile($cookieFile)
            ->setCookieJar($cookieFile)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->allowRedirect()
            ->get();

        Log::info('test:profile:create', [
            'url' => $url,
            'method' => 'get',
            'response' => $response->status,
            'email'    => $email
        ]);

        if(\File::exists($cookieFile))
        {
            \File::delete($cookieFile);
        }

        /*Get login page*/
        $url = $this->host . '/login';
        $response = Curl::to($url)
            ->withHeader('User-Agent: ' . $userAgent)
            ->setCookieFile($cookieFile)
            ->setCookieJar($cookieFile)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->allowRedirect()
            ->get();

        Log::info('test:profile:create', [
            'url' => $url,
            'method' => 'get',
            'response' => $response->status,
            'email'    => $email
        ]);

        /*Send auth*/
        $url = $this->host . '/login';
        $response = Curl::to($url)
            ->withData([
                'email'     => $email,
                'password'  => '123123'/*,
                '_token'    => $token*/
            ])
            ->withHeader('User-Agent: ' . $userAgent)
            ->setCookieFile($cookieFile)
            ->setCookieJar($cookieFile)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->allowRedirect()
            ->post();

        Log::info('test:profile:create', [
            'url' => $url,
            'method' => 'post',
            'response' => $response->status,
            'email'    => $email
        ]);

        /*Send scan*/
        $url = $this->host . '/profile/id';
        $response = Curl::to($url)
            ->withHeader('User-Agent: ' . $userAgent)
            ->withFile('back',
                resource_path('/for_test/katya-backjpg-43b5eb40edc963dcd27aae8b64649419-b-b.jpg'),
                //public_path('/images/uploads/backid/katya-backjpg-43b5eb40edc963dcd27aae8b64649419-b-b.jpg'),
                'image/jpeg',
                'katya-backjpg-43b5eb40edc963dcd27aae8b64649419-b-b.jpg'
            )
            ->withFile('front',
                resource_path('/for_test/katya-frontjpg-c8f0800e7af89be790d235d1f635c1e6-f-b.jpg'),
                //public_path('/images/uploads/frontid/katya-frontjpg-c8f0800e7af89be790d235d1f635c1e6-f-b.jpg'),
                'image/jpeg',
                'katya-frontjpg-c8f0800e7af89be790d235d1f635c1e6-f-b.jpg'
            )
            ->withResponseHeaders()
            ->returnResponseObject()
            ->setCookieFile($cookieFile)
            ->setCookieJar($cookieFile)
            ->allowRedirect()
            ->post();

        $getError = false;
        if($response->status == 200 && mb_strpos($response->content, 'alert alert-danger'))
        {
            $getError = true;
        }

        Log::info('test:profile:create', [
            'url' => $url,
            'method' => 'post',
            'response' => $response->status,
            'email'    => $email,
            'getError'  => $getError
        ]);

        /*Logout*/
        $url = $this->host . '/logout';
        $response = Curl::to($url)
            ->withHeader('User-Agent: ' . $userAgent)
            ->setCookieFile($cookieFile)
            ->setCookieJar($cookieFile)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->allowRedirect()
            ->get();

        Log::info('test:profile:create', [
            'url' => $url,
            'method' => 'get',
            'response' => $response->status,
            'email'    => $email
        ]);

        if(\File::exists($cookieFile))
        {
            \File::delete($cookieFile);
        }
    }
}
