<?php

use App\OrderAction;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreGraduateStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `profiles` MODIFY COLUMN `education_status` ENUM(\'matriculant\', \'student\', \'send_down\', \'academic_leave\', \'pregraduate\', \'graduate\') NULL ');

        $action = new OrderAction;
        //$action->id = 9;
        $action->name = 'Выдать диплом государственного образца и отчислить из университета';
        $action->save();

        $action = new OrderAction;
        //$action->id = 10;
        $action->name = 'Выдать диплом с отличием  государственного образца и отчислить из университета';
        $action->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `profiles` MODIFY COLUMN `education_status` ENUM(\'matriculant\', \'student\', \'send_down\', \'academic_leave\') NULL ');
    }
}
