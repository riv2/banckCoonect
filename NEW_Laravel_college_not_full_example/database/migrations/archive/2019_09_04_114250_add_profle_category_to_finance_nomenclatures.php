<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProfleCategoryToFinanceNomenclatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_nomenclatures', function (Blueprint $table) {
            $table->string('profile_category', 20)->nullable();
        });

        $service = \App\FinanceNomenclature::where('id', 22)->first();
        $service->name = 'Доступ к посещению занятий, для студентов иностранных ВУЗов-партнеров';
        $service->name_kz = 'Шетелдік серіктес жоғары оқу орындарының студенттері үшін сабаққа қатысуға қол жеткізу';
        $service->cost = 10000;
        $service->hidden = 0;
        $service->profile_category = \App\Profiles::CATEGORY_TRANSIT;
        $service->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_nomenclatures', function (Blueprint $table) {
            $table->dropColumn('profile_category');
        });

        $service = \App\FinanceNomenclature::where('id', 22)->first();
        $service->name = 'Доступ к посещению занятий, для студентов иностранных ВУЗов-партнеров (за 1 дисциплину)';
        $service->name_kz = '';
        $service->cost = 5000;
        $service->hidden = 1;
        $service->save();
    }
}
