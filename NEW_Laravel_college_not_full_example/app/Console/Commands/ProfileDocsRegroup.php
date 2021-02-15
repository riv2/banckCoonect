<?php

namespace App\Console\Commands;

use App\User;
use App\Profiles;
use App\ProfileDoc;
use File;
use Illuminate\Console\Command;

class ProfileDocsRegroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:regroupe';

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
        $type = 'front_id_photo';
        $profiles = Profiles::where($type, '!=', 1)->where('id', 192)->get();

        foreach ($profiles as $profile)
        {
            $filePath = public_path('images/uploads/frontid/' . $profile->{'$type'} . '');
            $this->info($filePath);
            if(File::exists($filePath) ) {
                $uploadedFile = new \Symfony\Component\HttpFoundation\File\File($filePath);
                //ProfileDoc::where('type', '')
                $this->info('File exist');
            } else {
                //$this->error('File not exist');
            }
            
        }
    }
}
