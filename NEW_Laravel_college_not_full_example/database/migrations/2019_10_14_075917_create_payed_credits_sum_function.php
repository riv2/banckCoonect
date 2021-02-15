<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayedCreditsSumFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('CREATE FUNCTION payed_credit_sum (user_id int) RETURNS int
BEGIN
    DECLARE sum INT;
    SELECT sum(payed_credits) as `sum` from students_disciplines where student_id = user_id INTO sum;
    RETURN sum;
END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP FUNCTION payed_credit_sum');
    }
}
