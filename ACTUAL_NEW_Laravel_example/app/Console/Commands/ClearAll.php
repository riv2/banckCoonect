<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAll extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'clearall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очищает все кеши';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $commands = [
            'clear-compiled',
            'auth:clear-resets',
            'cache:clear',
            'config:clear',
            'debugbar:clear',
            'event:clear',
            'optimize:clear',
            'route:clear',
            'view:clear',
            'package:discover',
        ];
        foreach ($commands as $command) {
            $this->call($command);
        }
        $this->info('Files cached successfully!');
    }
}
