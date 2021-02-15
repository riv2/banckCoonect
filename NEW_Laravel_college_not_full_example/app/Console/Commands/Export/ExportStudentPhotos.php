<?php

namespace App\Console\Commands\Export;

use App\ProfileDoc;
use App\Profiles;
use App\Services\Avatar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExportStudentPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:studetns:photo';

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
        /*$files = ProfileDoc
            ::leftJoin('profiles', 'profiles.user_id', '=', 'profile_docs.user_id')
            ->leftJoin('users', 'users', '=', 'profile_docs.user_id')
            ->where('profile_docs.doc_type', 'student_photo')
            ->whereNull('users.deleted_at')
            ->get();*/

        $count = Profiles
            ::where('education_status', Profiles::EDUCATION_STATUS_STUDENT)->count();

        $this->output->progressStart($count);

        Profiles
            ::with(['profileDocs' => function($q){
                $q->where('doc_type', 'student_photo');
            }])
            ->where('education_status', Profiles::EDUCATION_STATUS_STUDENT)
            ->chunk(1000, function($profiles){

                foreach ($profiles as $profile)
                {
                    $downloadLink = '';

                    if($profile->profileDocs && isset($profile->profileDocs[0]))
                    {
                        $doc = new ProfileDoc;
                        $item = $profile->profileDocs[0];
                        $downloadLink = '/' . $doc->getPathForDoc($item->doc_type, $item->filename) . $item->filename . ProfileDoc::EXT;

                        Log::info('docs ' . $downloadLink);
                    }
                    elseif($profile->faceimg)
                    {
                        $downloadLink = Avatar::getStudentFacePublicPath($profile->faceimg);
                        Log::info('avatar ' . $downloadLink);
                    }

                    if($downloadLink)
                    {
                        $downloadLink = 'https://miras.app' . $downloadLink;
                        $file = file_get_contents($downloadLink);

                        if($file)
                        {
                            $fname = storage_path('photos/' . $profile->iin . '.jpg');

                            file_put_contents($fname, $file);
                        }
                    }

                    $this->output->progressAdvance();
                }


            });

        $this->output->progressFinish();
    }
}
