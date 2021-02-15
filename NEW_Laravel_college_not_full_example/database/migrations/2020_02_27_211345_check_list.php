<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CheckList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('check_list')) {
            Schema::create('check_list', function (Blueprint $table) {

                $table->increments('id');

                $table->integer('speciality_id')->nullable(false);

                $table->enum('basic_education', ['high_school', 'vocational_education', 'higher'])->default('high_school');
                $table->enum('citizenship', ['citizenship_kz', 'all_citizenship', 'citizenship_without_kz'])->default('citizenship_kz');
                $table->enum('education_level', ['bachelor', 'magistracy'])->default('bachelor');

                $table->tinyInteger('documents_checked')->default(0);
                $table->tinyInteger('documents_is_sum')->default(0);
                $table->tinyInteger('prerequisites_checked')->default(0);
                $table->tinyInteger('prerequisites_is_sum')->default(0);
                $table->tinyInteger('Interview_checked')->default(0);
                $table->tinyInteger('Interview_is_sum')->default(0);
                $table->tinyInteger('total_point_checked')->default(0);

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
        Schema::dropIfExists('check_list');
    }
}
