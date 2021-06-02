<?php

namespace App\Console\Commands;

use App\Helpers\ApiCheckerHelper;
use App\Models\Pp;
use Illuminate\Console\Command;

class CheckOrderState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gocpa:checkapi {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка заказов по API';

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
     * @return int
     */
    public function handle()
    {

        $className = "App\\Console\\Commands\\Apis\\Api" . ucfirst($this->argument("name"));
        /**
         * @var ApiCheckerHelper $apiObject
         */
        $apiObject = new $className();
        if (Pp::query()->where('id','=',$apiObject->ppID)->first()->stopupdate) {
            return false;
        }
        $apiObject->run();
        return 0;
    }
}
