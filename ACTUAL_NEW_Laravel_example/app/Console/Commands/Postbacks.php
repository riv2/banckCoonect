<?php

namespace App\Console\Commands;

use App\Models\Notify;
use App\Postbacks\Postback;
use Illuminate\Console\Command;

class Postbacks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gocpa:postbacks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send postbacks';

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
    public function handle(): int
    {
        $f = storage_path('logs/postbacks.disabled');
        if (is_file($f)) {
            $this->warn('Постбеки выключены!');
            return 1;
        }

        $postbacks = Notify::query()
            ->where('responce_httpcode', '=', null)
            ->where('datetime', '<=', date('Y-m-d H:i:s'))
            ->limit(10)
            ->get();
        foreach ($postbacks as $postback) {
            (new Postback($postback))->handle();
        }
        return 0;
    }
}
