<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EntranceExamFilesChangeTypeEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("alter table `entrance_exam_files` modify column `type` enum ('manual','statement','commission_structure','composition_appeal_commission','schedule','protocols_creative_exams','protocols_appeal_commission','report_exams')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("alter table `entrance_exam_files` modify column `type` enum ('manual','commission_structure','composition_appeal_commission','schedule','protocols_creative_exams','protocols_appeal_commission','report_exams')");
    }
}
