<?php

namespace App\Console\Commands\Import;

use App\AdminStudentComment;
use App\User;
use Illuminate\Console\Command;

class StudentCommentsInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:student:comments:info';

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
        $file = fopen(storage_path('import/mini_import_comments.csv'), 'r');
        $author = User::where('email', 'i@thergbstudio.com')->first();

        $this->output->progressStart();

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $user = User::where('ex_id', $row[0])->first();

            for($i=1; $i < count($row); $i++)
            {
                if($row[$i])
                {
                    $comment = new AdminStudentComment();
                    $comment->author_id = $author->id;
                    $comment->user_id = $user->id;
                    $comment->check_level = 'or_cabinet';
                    $comment->text = $row[$i];
                    //$comment->save();
                }
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
