<?php

namespace App\Console\Commands;

use App\Profiles;
use App\User;
use Illuminate\Console\Command;

class CrutchRenameImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crutch:images:rename';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'rename all images without -b.jpg';

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
        $it = new \RecursiveDirectoryIterator(public_path("images"));
        foreach(new \RecursiveIteratorIterator($it) as $file) {
            $ext = explode('.', $file);
            $ext = strtolower(array_pop($ext));
            if ($ext == 'jfif') {
                if(rename($file, $file . '-b.jpg')){
                    $this->info('File has been renamed: ' . $file);  
                }
                
            }
        }

        
    }
}
