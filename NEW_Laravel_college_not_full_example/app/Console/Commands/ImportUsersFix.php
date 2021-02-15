<?php

namespace App\Console\Commands;

use App\User;
use function foo\func;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class ImportUsersFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:import:fix {--cacheonly=false} {--clearcache=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $years = [2015, 2016, 2017, 2018];
    protected $redisPath = 'import:user:csv:';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::useDailyFiles(storage_path('logs/import_users_fix' . date('Y_m_d', time()) . '.log'));

        $cacheonly = $this->option('cacheonly') == 'true' ? true : false;
        $clearcache = $this->option('clearcache') == 'true' ? true : false;

        if($clearcache)
        {
            $this->cacheClear();
            return;
        }

        if($cacheonly)
        {
            $this->cacheCsv();
            return;
        }

        $users = User::with('studentProfile')
            ->where(function($query){
                $query->whereHas('studentProfile', function($query1){
                    $query1->whereNull('education_study_form');
                })
                    ->orWhereHas('bcApplication', function($query1){
                        $query1->whereNull('education');
                    })
                    ->orWhereHas('mgApplication', function($query1){
                        $query1->whereNull('education');
                    });
            })
            ->where('keycloak', true)
            ->get();

        $this->output->progressStart(count($users));

        foreach($users as $user)
        {
            $importUser = Redis::get($this->redisPath . $user->ex_id);
            $importUser = json_decode($importUser, true);
            $user->studentProfile->education_study_form = $this->convertEducationStudyForm($importUser[16]);
            $user->studentProfile->save();
            $application = $user->mgApplication ? $user->mgApplication : $user->bcApplication;
            $application->education = $this->convertBaseEducation($importUser[18]);
            $application->save();

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    public function cacheCsv()
    {
        $hasId = [];

        foreach ($this->years as $year)
        {
            $this->info('Make cache ' . $year);
            $this->output->progressStart(sizeof (file (storage_path('import/import_users_' . $year . '.csv'))));

            $file = fopen(storage_path('import/import_users_' . $year . '.csv'), 'r');

            while($row = fgetcsv($file, 0, ',', "'"))
            {
                if(!in_array($row[0], $hasId)) {
                    Redis::set($this->redisPath . $row[0], json_encode($row));
                    $hasId[] = $row[0];
                }
                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
        }
    }

    public function cacheClear()
    {

        $keys = $keys = Redis::keys($this->redisPath . '*');

        if ($keys) {
            return Redis::del($keys);
        }

        $this->info('Cache cleaned');

        return true;
    }

    public function convertEducationStudyForm($str)
    {
        $list = [
            'Вечерняя' => 'evening',
            'Очная' => 'fulltime',
            'Онлайн'    => 'online',
            'Дистанционная' => 'distant',
            'Заочная' => 'extramural'
        ];

        return $list[$str] ?? null;
    }

    public function convertBaseEducation($str)
    {
        $list = [
            'Среднее' => 'high_school',
            'СПО' => 'vocational_education',
            'ВО' => 'bachelor'
        ];

        return $list[$str] ?? null;
    }
}
