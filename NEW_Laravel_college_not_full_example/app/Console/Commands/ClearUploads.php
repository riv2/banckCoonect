<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ClearUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:uploads';

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        File::cleanDirectory(public_path('avatars'));
        File::cleanDirectory(public_path('images/uploads/articles'));
        File::cleanDirectory(public_path('images/uploads/backid'));
        File::cleanDirectory(public_path('images/uploads/certificates'));
        File::cleanDirectory(public_path('images/uploads/courses'));
        File::cleanDirectory(public_path('images/uploads/diploma'));
        File::cleanDirectory(public_path('images/uploads/frontid'));
        File::cleanDirectory(public_path('images/uploads/atteducation'));
        File::cleanDirectory(public_path('images/uploads/residence'));
        File::cleanDirectory(public_path('images/uploads/r086'));
        File::cleanDirectory(public_path('images/uploads/r063'));
        File::cleanDirectory(public_path('images/uploads/military'));
        File::cleanDirectory(public_path('images/uploads/nostrificationattach'));
        File::cleanDirectory(public_path('images/uploads/nostrifications'));
    }
}
