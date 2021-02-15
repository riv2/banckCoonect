<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NobdUserCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('nobd_user')) {
            Schema::create('nobd_user', function (Blueprint $table) {

                $table->increments('id');

                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                // 1 //
                $table->unsignedInteger('study_exchange')->nullable(true)->comment('студент обучается по обмену');
                $table->foreign('study_exchange')->references('id')->on('nobd_study_exchange');

                // 1
                $table->unsignedInteger('host_country')->nullable(true)->comment('принимающая страна');
                $table->foreign('host_country')->references('id')->on('nobd_country');

                // 2
                $table->string('host_university_name')->nullable(true)->comment('Наименование принимающего зарубежного вуза-партнера');

                // 3
                $table->unsignedInteger('host_university_language')->nullable(true)->comment('Язык обучения в принимающем вузе');
                $table->foreign('host_university_language')->references('id')->on('nobd_language');

                // 4
                $table->unsignedInteger('exchange_specialty')->nullable(true)->comment('Специальность по обмену');
                $table->foreign('exchange_specialty')->references('id')->on('nobd_exchange_specialty');

                // 5
                $table->string('exchange_specialty_st')->nullable(true)->comment('Специальность по обмену');

                // 6
                $table->date('exchange_date_start')->nullable(true)->comment('Начало срока пребывания по обмену');

                // 7
                $table->date('exchange_date_end')->nullable(true)->comment('Окончание срока пребывания');

                // 8
                $table->unsignedInteger('academic_mobility')->nullable(true)->comment('Академическая мобильность');
                $table->foreign('academic_mobility')->references('id')->on('nobd_academic_mobility');


                // 2 //
                $table->unsignedInteger('academic_leave')->nullable(true)->comment('Находится в академическом отпуске');
                $table->foreign('academic_leave')->references('id')->on('nobd_academic_leave');

                // 1
                $table->string('academic_leave_order_number')->nullable(true)->comment('Номер приказа о предоставлении обучающемуся академического отпуска');

                // 2
                $table->date('academic_leave_order_date')->nullable(true)->comment('Дата приказа о предоставлении обучающемуся академического отпуска');

                // 3
                $table->string('academic_leave_out_order_number')->nullable(true)->comment('Номер приказа о выходе обучающегося из академического отпуска');

                // 4
                $table->date('academic_leave_out_order_date')->nullable(true)->comment('Дата приказа о выходе обучающегося из академического отпуска');


                // 15
                $table->tinyInteger('is_national_student_league')->default(0)->comment('Участвует в Национальной студенческой лиге');

                // 16
                $table->tinyInteger('is_world_winter_universiade')->default(0)->comment('Участвует во всемирной зимней Универсиаде');

                // 17
                $table->tinyInteger('is_world_summer_universiade')->default(0)->comment('Участвует во всемирной летней Универсиаде');

                // 18
                $table->tinyInteger('is_winter_universiade_republic_kz')->default(0)->comment('Участвует в зимней Универсиаде Республики Казахстан');

                // 19
                $table->tinyInteger('is_summer_universiade_republic_kz')->default(0)->comment('Участвует в летней Универсиаде Республики Казахстан');

                // 20
                $table->tinyInteger('is_nonresident_student')->default(0)->comment('Иногородний студент');

                // 21
                $table->tinyInteger('is_needs_hostel')->default(0)->comment('Нуждается в общежитии');

                // 22
                $table->tinyInteger('is_lives_hostel')->default(0)->comment('Проживает в общежитии');

                // 23
                $table->unsignedInteger('payment_type')->nullable(true)->comment('Обучение за счет средств');
                $table->foreign('payment_type')->references('id')->on('nobd_payment_type');

                // 24
                $table->integer('cost_education')->nullable(true)->comment('Стоимость обучения (за год), тысяч тенге');

                // 25
                $table->string('number_grant_certificate')->nullable(true)->comment('Номер свидетельства об присуждении гранта');

                // 26
                $table->unsignedInteger('trained_quota')->nullable(true)->comment('Обучается по квоте');
                $table->foreign('trained_quota')->references('id')->on('nobd_trained_quota');

                // 27
                $table->unsignedInteger('cause_stay_year')->nullable(true)->comment('Оставлен на повторный курс');
                $table->foreign('cause_stay_year')->references('id')->on('nobd_cause_stay_year');


                // 28
                $table->tinyInteger('is_participation_competitions')->default(0)->comment('Участие в соревнованиях');


                // 29
                $table->tinyInteger('is_orphan')->default(0)->comment('Сирота');

                // 30
                $table->tinyInteger('is_child_without_parents')->default(0)->comment('Ребенок оставшийся без попечения родителей');

                // 31
                $table->tinyInteger('is_invalid')->default(0)->comment('Инвалид');

                // 32
                $table->unsignedInteger('disability_group')->nullable(true)->comment('Группа инвалидности');
                $table->foreign('disability_group')->references('id')->on('nobd_disability_group');


                // 33
                $table->unsignedInteger('type_violation')->nullable(true)->comment('Виды нарушений');
                $table->foreign('type_violation')->references('id')->on('nobd_type_violation');

                // 34
                $table->string('conclusion_pmpc')->nullable(true)->comment('Заключение ПМПК');

                // 35
                $table->date('conclusion_date')->nullable(true)->comment('Дата заключения');


                // 36
                $table->tinyInteger('is_thesis_defense')->default(0)->comment('С защитой диссертации');

                // 37
                $table->unsignedInteger('form_diplom')->nullable(true)->comment('Вид диплома');
                $table->foreign('form_diplom')->references('id')->on('nobd_form_diplom');

                // 38
                $table->string('diplom_series')->nullable(true)->comment('Серия диплома');

                // 39
                $table->string('diplom_number')->nullable(true)->comment('Номер диплома');

                // 40
                $table->date('date_disposal')->nullable(true)->comment('Дата выбытия');

                // 41
                $table->string('number_disposal_order')->nullable(true)->comment('Номер приказа выбытия');

                // 42
                $table->unsignedInteger('reason_disposal')->nullable(true)->comment('Причина выбытия');
                $table->foreign('reason_disposal')->references('id')->on('nobd_reason_disposal');

                // 43
                $table->unsignedInteger('employment_opportunity')->nullable(true)->comment('Трудоустройство');
                $table->foreign('employment_opportunity')->references('id')->on('nobd_employment_opportunity');


                $table->timestamps();
                $table->softDeletes();

            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nobd_user');
    }
}
