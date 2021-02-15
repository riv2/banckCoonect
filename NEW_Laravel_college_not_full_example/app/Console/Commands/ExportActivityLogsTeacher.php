<?php

namespace App\Console\Commands;

use App\ActivityLog;
use Illuminate\Console\Command;

class ExportActivityLogsTeacher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:activitylogs:teacher';

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

        $fileName = storage_path('export/activity_logs_teacher'.date('Y_m_d_H:i').'.csv');
        $file = fopen($fileName, 'w');

        $oActivityLogR = ActivityLog::
        select([
            'id',
            'user_id',
            'properties',
        ])->
        with('profileTeacher')->
        whereHas('profileTeacher')->
        where('activity_logs.log_type',ActivityLog::TEACHER_ONLINE_ACTIVITY);

        $count = $oActivityLogR;
        $count = $count->count();

        fputcsv($file, [
            'ID',
            'ФИО',
            'ИИН',
            'Номер',
            'Дата',
            'Страницы'
        ]);

        $this->output->progressStart($count);

        $oActivityLogR->chunk(500, function($rows) use($file){
            foreach ($rows as $item)
            {

                $properties = json_decode($item->properties);

                $aPages = [];
                if( !empty($properties->visited_pages) )
                {
                    foreach( $properties->visited_pages as $vpItem )
                    {
                        $aPages[] = date('H:i',strtotime($vpItem->time)) . ' - ' . $vpItem->page . ' - ' . $vpItem->url;
                    }
                }

                fputcsv($file, [
                    $item->user_id,
                    $item->profileTeacher->fio ?? '',
                    $item->profileTeacher->iin ?? '',
                    $item->profileTeacher->mobile ?? '',
                    date('Y-m-d',strtotime($properties->from)),
                    implode(' | ',$aPages),
                ]);

                $this->output->progressAdvance();
            }
        });

        $this->output->progressFinish();
        fclose($file);

    }
}
