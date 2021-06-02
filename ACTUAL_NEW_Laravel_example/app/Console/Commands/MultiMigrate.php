<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MultiMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'multimigrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Миграции для всех доменов';

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
        foreach(config("domain.domains") as $dom=>$item) {
            $this->info("Migrate for: " . $dom);
            system("php artisan migrate --domain=" . $dom);
            #Artisan::call("migrate", ["domain"=> $dom, "--verbose"=>true]);
            #$this->info(Artisan::output());
        }

    }
}
