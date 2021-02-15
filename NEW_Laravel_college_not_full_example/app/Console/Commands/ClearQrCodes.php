<?php

namespace App\Console\Commands;

use App\QrCode;
use Illuminate\Console\Command;

class ClearQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:qr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete qr codes by expire date';

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
        QrCode::whereRaw('TIMESTAMPDIFF(SECOND,created_at,"' . date('Y-m-d H:i:s', time()) . '") > expire_sec')->delete();
    }
}
