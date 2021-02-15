<?php

namespace App\Console\Commands\Test;

use App\Services\Auth;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class TestKeycloakUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:user:keycloak {--email=null} {--from=1} {--to=1} {--type=all}';

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
        if(!env('APP_DEBUG_PASSWORD'))
        {
            $this->error('app debug password not found');
            return;
        }

        Log::useDailyFiles(storage_path('logs/test_module_loade_' . date('Y_m_d', time()) . '.log'));

        $email = $this->option('email');
        $from = (int)$this->option('from');
        $to = (int)$this->option('to');
        $type = $this->option('type');

        if($email == 'null')
        {
            $this->runList($from, $to, $type);
        }
        else
        {
            $this->runSingle($email);
        }

    }

    public function runList($from, $to, $type)
    {
        $userList = User::where('keycloak', true);

        if($from != $to)
        {
            $userList
                ->offset($from - 1)
                ->limit($to - $from - 1);
        }

        if($type == User::IMPORT_TYPE_ENG_TEST || User::IMPORT_TYPE_GOS_TEST)
        {
            $userList = $userList->where('import_type', $type);
        }

        $userList = $userList->get();

        foreach ($userList as $user)
        {
            exec('php artisan test:user:keycloak --email=' . $user->email . ' > /dev/null 2>/dev/null &');
        }
    }

    public function runSingle($email)
    {
        /*$user = User::where('email', $email);

        if(!$user)
        {
            Log::warning('test:user:keycloak User not found', ['email' => $email]);
            return false;
        }*/
        sleep(rand(1,5));
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

        Log::info('test:user:keycloak', [
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

        Log::info('test:user:keycloak', [
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
                'password'  => env('APP_DEBUG_PASSWORD', null)/*,
                '_token'    => $token*/
            ])
            ->withHeader('User-Agent: ' . $userAgent)
            ->setCookieFile($cookieFile)
            ->setCookieJar($cookieFile)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->allowRedirect()
            ->post();

        Log::info('test:user:keycloak', [
            'url' => $url,
            'method' => 'post',
            'response' => $response->status,
            'email'    => $email
        ]);

        if($response->status != 200)
        {
            return;
        }

        /*Get student page*/
        $url = $this->host . '/finances';
        $response = Curl::to($url)
            ->withHeader('User-Agent: ' . $userAgent)
            ->setCookieFile($cookieFile)
            ->setCookieJar($cookieFile)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->allowRedirect()
            ->get();

        Log::info('test:user:keycloak', [
            'url' => $url,
            'method' => 'get',
            'response' => $response->status,
            'email'    => $email
        ]);

        $matches = '';
        $url = preg_match_all('"' . $this->host . '/quize/[0-9]+"', $response->content, $matches);

        if(isset($matches[0][0]))
        {
            /*Start test*/
            $url = $matches[0][0];
            $response = Curl::to($url)
                ->withHeader('User-Agent: ' . $userAgent)
                ->setCookieFile($cookieFile)
                ->setCookieJar($cookieFile)
                ->withResponseHeaders()
                ->returnResponseObject()
                ->allowRedirect()
                ->get();

            Log::info('test:user:keycloak', [
                'url' => $url,
                'method' => 'get',
                'response' => $response->status,
                'email'    => $email
            ]);

            sleep(2);
        }
        else
        {
            Log::warning('test:user:keycloak', [
                'url' => $url,
                'error' => 'Discipline link not found',
                'email'    => $email
            ]);
        }

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

        Log::info('test:user:keycloak', [
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
