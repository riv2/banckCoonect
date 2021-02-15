<?php

use Illuminate\Database\Seeder;

class ScientificFieldInitList extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('scientific_field')->insert([
            0 => [
                'id'         => 1,
                'name'       => 'Без отрасли',
                'name_kz'    => 'Өнеркәсіпсіз',
                'name_en'    => 'Without industry',
                'created_at' => date('Y-m-d H:i',time())
            ],
            1 => [
                'id'         => 2,
                'name'       => 'Физико-математические науки',
                'name_kz'    => 'Физика-математика ғылымдары',
                'name_en'    => 'Physics and Mathematics',
                'created_at' => date('Y-m-d H:i',time())
            ],
            2 => [
                'id'         => 3,
                'name'       => 'Химические науки',
                'name_kz'    => 'Химия ғылымдары',
                'name_en'    => 'Chemical sciences',
                'created_at' => date('Y-m-d H:i',time())
            ],
            3 => [
                'id'         => 4,
                'name'       => 'Биологические науки',
                'name_kz'    => 'Биология ғылымдары',
                'name_en'    => 'Biological sciences',
                'created_at' => date('Y-m-d H:i',time())
            ],
            4 => [
                'id'         => 5,
                'name'       => 'Технические науки',
                'name_kz'    => 'Техника ғылымдары',
                'name_en'    => 'Technical science',
                'created_at' => date('Y-m-d H:i',time())
            ],
            5 => [
                'id'         => 6,
                'name'       => 'Сельскохозяйственные науки',
                'name_kz'    => 'Ауыл шаруашылығы ғылымдары',
                'name_en'    => 'Agricultural sciences',
                'created_at' => date('Y-m-d H:i',time())
            ],
            6 => [
                'id'         => 7,
                'name'       => 'Исторические науки',
                'name_kz'    => 'Тарих ғылымдары',
                'name_en'    => 'Historical sciences',
                'created_at' => date('Y-m-d H:i',time())
            ],
            7 => [
                'id'         => 8,
                'name'       => 'Экономические науки',
                'name_kz'    => 'Экономика ғылымдары',
                'name_en'    => 'Economics',
                'created_at' => date('Y-m-d H:i',time())
            ],
            8 => [
                'id'         => 9,
                'name'       => 'Философские науки',
                'name_kz'    => 'Философиялық ғылымдары',
                'name_en'    => 'Philosophical Sciences',
                'created_at' => date('Y-m-d H:i',time())
            ],
            9 => [
                'id'         => 10,
                'name'       => 'Филологические науки',
                'name_kz'    => 'Филология ғылымдары',
                'name_en'    => 'Philological sciences',
                'created_at' => date('Y-m-d H:i',time())
            ],
            10 => [
                'id'         => 11,
                'name'       => 'Юридические науки',
                'name_kz'    => 'Заң ғылымдары',
                'name_en'    => 'Jurisprudence',
                'created_at' => date('Y-m-d H:i',time())
            ],
            11 => [
                'id'         => 12,
                'name'       => 'Педагогические науки',
                'name_kz'    => 'Педагогика ғылымдары',
                'name_en'    => 'Pedagogical sciences',
                'created_at' => date('Y-m-d H:i',time())
            ],
            12 => [
                'id'         => 13,
                'name'       => 'Медицинские науки',
                'name_kz'    => 'Медицина ғылымдары',
                'name_en'    => 'Medical sciences',
                'created_at' => date('Y-m-d H:i',time())
            ],
            13 => [
                'id'         => 14,
                'name'       => 'Фармацевтические науки',
                'name_kz'    => 'Фармацевтика ғылымдары',
                'name_en'    => 'Pharmaceutical Sciences',
                'created_at' => date('Y-m-d H:i',time())
            ],
            14 => [
                'id'         => 15,
                'name'       => 'Ветеринарные науки',
                'name_kz'    => 'Ветеринария ғылымдары',
                'name_en'    => 'Veterinary sciences',
                'created_at' => date('Y-m-d H:i',time())
            ],
            15 => [
                'id'         => 16,
                'name'       => 'Искусствоведение',
                'name_kz'    => 'Өнер тарихы',
                'name_en'    => 'Art history',
                'created_at' => date('Y-m-d H:i',time())
            ],
            16 => [
                'id'         => 17,
                'name'       => 'Архитектура',
                'name_kz'    => 'Сәулет',
                'name_en'    => 'Architecture',
                'created_at' => date('Y-m-d H:i',time())
            ],
            17 => [
                'id'         => 18,
                'name'       => 'Психологические науки',
                'name_kz'    => 'Психология ғылымдары',
                'name_en'    => 'Psychological sciences',
                'created_at' => date('Y-m-d H:i',time())
            ],
            18 => [
                'id'         => 19,
                'name'       => 'Социологические науки',
                'name_kz'    => 'Әлеуметтану ғылымдары',
                'name_en'    => 'Sociological sciences',
                'created_at' => date('Y-m-d H:i',time())
            ],
            19 => [
                'id'         => 20,
                'name'       => 'Политические науки',
                'name_kz'    => 'Саясаттану ғылымдары',
                'name_en'    => 'Political science',
                'created_at' => date('Y-m-d H:i',time())
            ],
            20 => [
                'id'         => 21,
                'name'       => 'Культурология',
                'name_kz'    => 'Мәдениеттану',
                'name_en'    => 'Cultural studies',
                'created_at' => date('Y-m-d H:i',time())
            ],
            21 => [
                'id'         => 22,
                'name'       => 'Науки о Земле',
                'name_kz'    => 'Жер туралы ғылымдары',
                'name_en'    => 'Earth sciences',
                'created_at' => date('Y-m-d H:i',time())
            ],
        ]);
    }
}
