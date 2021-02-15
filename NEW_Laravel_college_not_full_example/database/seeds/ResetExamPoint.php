<?php

use App\{StudentDiscipline};
use Illuminate\Support\Facades\{DB};
use Illuminate\Database\Seeder;

class ResetExamPoint extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // находим 0 оценки по экзаменам
        /*
        $oStudentDiscipline = StudentDiscipline::
        where('test_result',0)->
        update([
            'test_result' => null,
            'test_result_points' => null,
            'test_result_letter' => null,
            'test_date' => null,
            'test_result_trial' => null,
            'test_blur' => null,
            'test_qr_checked' => null,
            'final_result' => null,
            'final_result_points' => null,
            'final_result_gpa' => null,
            'final_result_letter' => null,
        ]);
        */

        $result = DB::update('UPDATE students_disciplines SET 
            test_result=NULL,
            test_result_points=NULL,
            test_result_letter=NULL,
            test_date=NULL,    
            final_result=NULL,  
            final_result_points=NULL,  
            final_result_gpa=NULL,  
            final_result_letter=NULL 
        WHERE test_result = 0');


    }
}
