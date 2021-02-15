<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDiscountTypeList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_type_list', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('category_id')->nullable();
            $table->string('name_ru')->nullable();
            $table->string('name_kz')->nullable();
            $table->string('name_en')->nullable();
            $table->string('description_ru')->nullable();
            $table->string('description_kz')->nullable();
            $table->string('description_en')->nullable();
            $table->boolean('citizen')->default(1);
            $table->boolean('hidden')->default(0);
            $table->integer('discount')->nullable()->comment('discount size in percents');
            
            $table->timestamps();
        });

        DB::table('discount_type_list')->insert([
            'category_id' => 1,
            'name_ru' => 'Свободное владение английским языком',
            'name_kz' => 'Ағылшын тілін еркін меңгеру',
            'name_en' => 'Fluency in English',
            'discount' => 100
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 1,
            'name_ru' => 'Наивысший балл ЕНТ',
            'name_kz' => 'ҰБТ-ның ең жоғарғы ұпайы',
            'name_en' => 'Highest score UNT',
            'discount' => 100
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 1,
            'name_ru' => 'Балл GPA',
            'name_kz' => 'GPA көрсеткіші',
            'name_en' => 'GPA score',
            'discount' => 50
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 2,
            'name_ru' => 'Инвалиды 1 и 2 групп',
            'name_kz' => '1 және 2 топтағы мүгедектер',
            'name_en' => 'Handicapped 1 and 2 groups',
            'discount' => 10
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 2,
            'name_ru' => 'Семьи, имеющие или воспитывающие инвалидов',
            'name_kz' => 'Мүгедектігі бар немесе мүгедектігі бар отбасылар',
            'name_en' => 'Families with or raising disabilities',
            'discount' => 10
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 2,
            'name_ru' => 'Дети-сироты',
            'name_kz' => 'Orphans',
            'name_en' => 'Жетім балалар',
            'discount' => 10
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 2,
            'name_ru' => 'Многодетные семьи',
            'name_kz' => 'Үлкен отбасылар',
            'name_en' => 'Large families',
            'discount' => 10
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 3,
            'name_ru' => 'За трудоустройство',
            'name_kz' => 'Жұмысқа орналасу үшін',
            'name_en' => 'For employment',
            'discount' => 50
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 3,
            'name_ru' => 'Для выпускников колледжа/университета Мирас',
            'name_kz' => 'Мирас колледжінің / университетінің түлектері үшін',
            'name_en' => 'For graduates of Miras College / University',
            'discount' => 10
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 3,
            'name_ru' => 'Для сотрудников и их родственников',
            'name_kz' => 'Қызметкерлер мен олардың туыстарына арналған',
            'name_en' => 'For employees and their relatives',
            'discount' => 10
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 3,
            'name_ru' => 'Для родственников студентов',
            'name_kz' => 'Студенттердің туыстарына арналған',
            'name_en' => 'For relatives of students',
            'discount' => 10
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 3,
            'name_ru' => 'Объемная скидка',
            'name_kz' => 'Үлкен жеңілдік',
            'name_en' => 'Bulk discount',
            'discount' => 10
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 4,
            'name_ru' => 'Заслуженный мастер спорта / Мастер спорта международного класса',
            'name_kz' => 'Еңбек сіңірген спорт шебері / Халықаралық дәрежедегі спорт шебері',
            'name_en' => 'Honored Master of Sports / Master of Sports of International Class',
            'discount' => 100
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 4,
            'name_ru' => 'Мастер спорта/кандидат в мастера спорта',
            'name_kz' => 'Спорт шебері / спорт шеберіне үміткер',
            'name_en' => 'Master of Sports / Candidate Master of Sports',
            'discount' => 10
        ]);


        DB::table('discount_type_list')->insert([
            'category_id' => 3,
            'name_ru' => 'Для граждан Узбекистана',
            'name_kz' => 'Өзбекстан азаматтары үшін',
            'name_en' => 'For citizens of Uzbekistan',
            'discount' => 50,
            'citizen' => 0
        ]);
        DB::table('discount_type_list')->insert([
            'category_id' => 3,
            'name_ru' => 'Для граждан Киргизии',
            'name_kz' => 'Қырғызстан азаматтары үшін',
            'name_en' => 'For citizens of Kyrgyzstan',
            'discount' => 50,
            'citizen' => 0
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_type_list');
    }
}
