<?php

namespace App\Console\Commands\Test;

use App\QuizeResultKge;
use App\Services\Auth;
use App\Services\Test\TestModule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\User;

class TestCheckKeycloakUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:user:keycloak:check {--type=all}';

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
        Log::useDailyFiles(storage_path('logs/test_module_' . date('Y_m_d', time()) . '.log'));

        if(!env('APP_DEBUG_PASSWORD'))
        {
            $this->error('app debug password not found');
            return;
        }

        $type = $this->option('type');

        if($type == 'all')
        {
            $this->warn('--type=all not support');
            return;
        }

        $userList = User::where('keycloak', true);

        if($type == User::IMPORT_TYPE_ENG_TEST || $type == User::IMPORT_TYPE_GOS_TEST)
        {
            $userList = $userList->where('import_type', $type);
        }

        $userList = $userList->get();

        foreach ($userList as $user)
        {
            if($type == User::IMPORT_TYPE_ENG_TEST)
            {
                $checkStatus = $this->runSingleEng($user->email);
            }
            if($type == User::IMPORT_TYPE_GOS_TEST)
            {
                $checkStatus = $this->runSingleGos($user->email);
            }

            if(!$checkStatus)
            {
                $this->error('User ' . $user->email . ' has error');
            }
        }

        $this->info('Check complete for ' . count($userList) . ' users');
    }

    /**
     * @param $email
     * @return bool
     */
    public function runSingleEng($email)
    {
        $hasError = false;

        unset($testModule);
        $testModule = new TestModule('test:user:keycloak:check', $this->host);
        $response = $testModule->login($email, env('APP_DEBUG_PASSWORD'));

        if($response->status != 200)
        {
            return false;
        }

        $disciplineUrlList = $testModule->pregMatchAll('"' . $this->host . '/quize/[0-9]+"', '/finances');
        if(!$disciplineUrlList)
        {
            Log::warning('test:user:keycloak:check', [
                'message' => 'Discipline url list is empty',
                'email' => $email
            ]);

            return false;
        }

        foreach ($disciplineUrlList as $url)
        {
            $tag = (bool)$testModule->pregMatchAll('/#main-test-form/', $url);

            if(!$tag)
            {
                Log::warning('test:user:keycloak:check', [
                    'message'   => 'Test not found',
                    'email'     => $email,
                    'url'       => $url
                ]);

                $hasError = true;
            }
        }

        $testModule->logout();
        unset($testModule);

        return !$hasError;
    }

    /**
     * @param $email
     * @return bool
     */
    public function runSingleGos($email)
    {
        $hasError = false;

        unset($testModule);
        $testModule = new TestModule('test:user:keycloak:check', $this->host);
        $response = $testModule->login($email, env('APP_DEBUG_PASSWORD'));

        if ($response->status != 200) {
            return false;
        }

        $user = User::where('email', $email)->first();

        $disciplineIdList = [];
        $disciplineIdUserList = [];
        $disciplineList = $user->studentProfile->speciality->disciplines;
        $studentDisciplineList = $user->studentProfile->disciplines;
        $kgeDisciplineCount = 0;

        foreach ($disciplineList as $discipline) {
            $disciplineIdList[] = $discipline->id;

            if($discipline->pivot->exam)
            {
                $kgeDisciplineCount++;
            }
        }

        foreach ($studentDisciplineList as $discipline) {
            $disciplineIdUserList[] = $discipline->discipline_id;
        }

        if ($disciplineIdList != $disciplineIdUserList) {
            Log::warning('test:user:keycloak:check', [
                'message' => 'Disciplines not equal with speciality',
                'email' => $email,
                'speciality disciplines' => $disciplineIdList,
                'user disciplines' => $disciplineIdUserList,
            ]);

            $hasError = true;
        }
        else {
            /*Check block by debt*/
            $hasBlock = $testModule->pregMatchAll( '/block-by-debt/', '/finances');

            if(count($hasBlock) > 0)
            {
                $blockByFinance = $user->balance() < 0;
                $blockByAcademic = $user->hasAcademDebt();

                Log::warning('test:user:keycloak:check', [
                    'message' => 'Has debt',
                    'email' => $email,
                    'finance debt' => $blockByFinance,
                    'academic debt' => $blockByAcademic
                ]);

                $messageParts = [
                    0 => '"' . $user->name . '"',
                    1 => '"' . $user->email . '"',
                    2 => '"' . ($blockByFinance === true ? 'yes' : 'no') . '"',
                    3 => '"' . ($blockByAcademic === true ? 'yes' : 'no') . '"'
                ];
                $this->warn(implode(';', $messageParts));

                $testModule->logout();
                unset($testModule);
                return true;
            }

            /*Check discipline list*/
            $disciplineUrlList = $testModule->pregMatchAll('"' . $this->host . '/quize/[0-9]+"', '/finances');
            if (count($disciplineUrlList) > 0) {
                foreach ($disciplineUrlList as $url) {
                    $tag = (bool)$testModule->pregMatchAll('/#main-test-form/', $url);

                    if (!$tag) {
                        Log::warning('test:user:keycloak:check', [
                            'message' => 'Test not found',
                            'email' => $email,
                            'url' => $url
                        ]);

                        $hasError = true;
                    }
                }
            } else {
                if (count($disciplineIdList) != $kgeDisciplineCount) {
                    Log::warning('test:user:keycloak:check', [
                        'message' => 'Discipline url list is empty',
                        'email' => $email
                    ]);
                    $hasError = true;
                }
            }


            /*Check kge*/

            $resultKge = QuizeResultKge::where('user_id', $user->id)->count();

            if($resultKge == 0)
            {
                $kgeLink = $testModule->pregMatchAll('"' . $this->host . '/quiz/kge"', '/finances');
                $url = $kgeLink[0] ?? '';

                if ($url) {

                    $tag = (bool)$testModule->pregMatchAll('/#main-test-form/', $url);

                    if (!$tag) {
                        Log::warning('test:user:keycloak:check', [
                            'message' => 'KGE not found on finances',
                            'email' => $email
                        ]);

                        $hasError = true;
                    }
                } else {
                    Log::warning('test:user:keycloak:check', [
                        'message' => 'KGE not found',
                        'email' => $email,
                        'url' => $url
                    ]);

                    $hasError = true;
                }
            }
            else
            {
                Log::warning('test:user:keycloak:check', [
                    'message' => 'Has result',
                    'email' => $email
                ]);

                $this->info('Has result: ' . $email);
            }
        }

        $testModule->logout();
        unset($testModule);

        return !$hasError;
    }
}
