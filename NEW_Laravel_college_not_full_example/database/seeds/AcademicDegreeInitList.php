<?php

use Illuminate\Database\Seeder;

class AcademicDegreeInitList extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('academic_degree')->insert([
            0 => [
                'id'            => 1,
                'name'          => 'Доктор философии (PhD)',
                'short_name'    => 'Доктор PhD',
                'name_kz'       => 'Философия докторы (PhD)',
                'short_namekz'  => 'PhD',
                'name_en'       => 'Doctor of Philosophy (PhD)',
                'type'          => 'DOCTOR_PHD',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            1 => [
                'id'            => 2,
                'name'          => 'Доктор экономических наук ',
                'short_name'    => 'д.э.н.',
                'name_kz'       => 'Экономика ғылымдарының докторы',
                'short_namekz'  => 'э.ғ.д.',
                'name_en'       => 'Higher doctorate economic sciences',
                'type'          => 'DOCTOR_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            2 => [
                'id'            => 3,
                'name'          => 'Кандидат экономических наук',
                'short_name'    => 'к.э.н.',
                'name_kz'       => 'Экономика ғылымдарының кандидаты',
                'short_namekz'  => 'э.ғ.к.',
                'name_en'       => 'Candidates degree in economic sciences',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            3 => [
                'id'            => 4,
                'name'          => 'Кандидат юридических наук ',
                'short_name'    => 'к.ю.н.',
                'name_kz'       => 'Заң ғылымдарының кандидаты',
                'short_namekz'  => 'з.ғ.к.',
                'name_en'       => 'Candidates degree in jurisprudence sciences',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            4 => [
                'id'            => 5,
                'name'          => 'Доктор исторических наук',
                'short_name'    => 'д.и.н.',
                'name_kz'       => 'Тарих ғылымдарының докторы',
                'short_namekz'  => 'т.ғ.д.',
                'name_en'       => 'Higher doctorate  historical sciences',
                'type'          => 'DOCTOR_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            5 => [
                'id'            => 6,
                'name'          => 'Кандидат технических наук',
                'short_name'    => 'к.т.н.',
                'name_kz'       => 'Техника ғылымдарының кандидаты',
                'short_namekz'  => 'т.ғ.к.',
                'name_en'       => 'Candidates degree in tehnical sciences',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            6 => [
                'id'            => 7,
                'name'          => 'Доктор технических наук',
                'short_name'    => 'д.т.н.',
                'name_kz'       => 'Техника ғылымдарының докторы',
                'short_namekz'  => 'т.ғ.д.',
                'name_en'       => 'Higher doctorate tehnical sciences',
                'type'          => 'DOCTOR_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            7 => [
                'id'            => 8,
                'name'          => 'Кандидат физико-математических  наук',
                'short_name'    => 'к.ф-м.н.',
                'name_kz'       => 'Физика-математика ғылымдарының кандидаты',
                'short_namekz'  => 'ф-м.ғ.к.',
                'name_en'       => 'Candidates degree in physical and mathematical sciences',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            8 => [
                'id'            => 9,
                'name'          => 'Кандидат биологических наук ',
                'short_name'    => 'к.б.н.',
                'name_kz'       => 'Биология ғылымдарының кандидаты',
                'short_namekz'  => 'б.ғ.к.',
                'name_en'       => 'Candidates degree in biological sciences',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            9 => [
                'id'            => 10,
                'name'          => 'Кандидат химических наук ',
                'short_name'    => 'к.х.н.',
                'name_kz'       => 'Химия ғылымдарының кандидаты',
                'short_namekz'  => 'х.ғ.к.',
                'name_en'       => 'Candidates degree in chemical sciences',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            10 => [
                'id'            => 11,
                'name'          => 'Доктор биологических наук',
                'short_name'    => 'д.б.н.',
                'name_kz'       => 'Биология ғылымдарының докторы',
                'short_namekz'  => 'б.ғ.д.',
                'name_en'       => 'Higher doctorate biological sciences',
                'type'          => 'DOCTOR_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            11 => [
                'id'            => 12,
                'name'          => 'Кандидат сельскохозяйственных наук',
                'short_name'    => 'к.с.-х.н.',
                'name_kz'       => 'Ауыл шаруашылығы ғылымдарының кандидаты',
                'short_namekz'  => 'а.-ш.ғ.к.',
                'name_en'       => 'Candidates degree in agricultural sciences',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            12 => [
                'id'            => 13,
                'name'          => 'Доктор сельскохозяйственных наук',
                'short_name'    => 'д.с.-х.н.',
                'name_kz'       => 'Ауыл шаруашылығы ғылымдарының докторы',
                'short_namekz'  => 'а.-ш.ғ.д.',
                'name_en'       => 'Higher doctorate agricultural sciences',
                'type'          => 'DOCTOR_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            13 => [
                'id'            => 14,
                'name'          => 'Кандидат педагогических наук',
                'short_name'    => 'к.пед.н.',
                'name_kz'       => 'Педагогика ғылымдарының кандидаты',
                'short_namekz'  => 'пед.ғ.к.',
                'name_en'       => 'Candidates degree in pedagogical sciences',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            14 => [
                'id'            => 15,
                'name'          => 'Кандидат психологических наук',
                'short_name'    => 'к.п.н.',
                'name_kz'       => 'Психология ғылымдарының кандидаты',
                'short_namekz'  => 'п.ғ.к.',
                'name_en'       => 'Candidates degree in psychological sciences',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            15 => [
                'id'            => 16,
                'name'          => 'Кандидат филологических наук',
                'short_name'    => 'к.ф.н.',
                'name_kz'       => 'Филология ғылымдарының докторы',
                'short_namekz'  => 'ф.ғ.д.',
                'name_en'       => 'Candidates degree in philological sciences',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            16 => [
                'id'            => 17,
                'name'          => 'Кандидат исторических наук',
                'short_name'    => 'к.и.н.',
                'name_kz'       => 'Тарих ғылымдарының кандидаты',
                'short_namekz'  => 'т.ғ.к.',
                'name_en'       => 'Candidates degree in historical sciences',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            17 => [
                'id'            => 18,
                'name'          => 'Доктор педагогических наук',
                'short_name'    => 'д.пед.н.',
                'name_kz'       => 'Педагогика ғылымдарының докторы',
                'short_namekz'  => 'пед.ғ.д.',
                'name_en'       => 'Higher doctorate pedagogical sciences',
                'type'          => 'DOCTOR_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            18 => [
                'id'            => 19,
                'name'          => 'Кандидат медицинских наук',
                'short_name'    => 'к.м.н',
                'name_kz'       => 'Медицина ғылымдарының кандидаты',
                'short_namekz'  => 'м.ғ.к.',
                'name_en'       => 'Candidates degree in Medical Sciences',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            19 => [
                'id'            => 20,
                'name'          => 'Член союза художников РК',
                'short_name'    => 'ч.с.х. РК',
                'name_kz'       => 'Қазақстан Суретшілер одағының мүшесі',
                'short_namekz'  => 'ҚР с.о.м',
                'name_en'       => 'Member of the Union of Artists  of the Republic of Kazakhstan',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            20 => [
                'id'            => 21,
                'name'          => 'Почетное звание',
                'short_name'    => 'Почетное звание',
                'name_kz'       => 'Құрметті атағы',
                'short_namekz'  => 'Құрметті атағы',
                'name_en'       => 'Honorary title',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            21 => [
                'id'            => 22,
                'name'          => 'Доктор юридических наук',
                'short_name'    => 'д.ю.н.',
                'name_kz'       => 'Заң ғылымдарының докторы',
                'short_namekz'  => 'з.ғ.д',
                'name_en'       => 'Higher doctorate jurisprudence sciences',
                'type'          => 'DOCTOR_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            22 => [
                'id'            => 23,
                'name'          => 'Кандидат философских наук',
                'short_name'    => 'к.филос.н.',
                'name_kz'       => 'Философия ғылымдарының кандидаты',
                'short_namekz'  => 'филос.ғ.к.',
                'name_en'       => 'Сandidate of Philosophical sciences',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            23 => [
                'id'            => 24,
                'name'          => 'Доктор психологических наук',
                'short_name'    => 'д.п.н.',
                'name_kz'       => 'Психология ғылымдарынын докторы',
                'short_namekz'  => 'п.ғ.д',
                'name_en'       => 'Higher doctorate psychological sciences',
                'type'          => 'DOCTOR_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            24 => [
                'id'            => 25,
                'name'          => 'Заслуженный тренер Республики Казахстан',
                'short_name'    => 'з.т. РК',
                'name_kz'       => 'Қазақстан Республикасының еңбек сіңірген жаттықтырушы',
                'short_namekz'  => 'ҚР е.с.ж.',
                'name_en'       => 'Honored Trainer of the Republic of Kazakhstan',
                'type'          => 'CANDIDATE_OF_SCIENCES',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            25 => [
                'id'            => 26,
                'name'          => 'Почетный знак',
                'short_name'    => 'Почетный знак',
                'name_kz'       => 'Мемлекеттік қоры',
                'short_namekz'  => 'Мемлекеттік қоры',
                'name_en'       => 'Mark of honor',
                'type'          => '',
                'created_at'    => date('Y-m-d H:i',time())
            ],
            26 => [
                'id'            => 27,
                'name'          => 'Государственная награда',
                'short_name'    => 'Гос. награда',
                'name_kz'       => 'Мемлекеттік наградасы',
                'short_namekz'  => 'Мем. наградасы',
                'name_en'       => 'State award',
                'type'          => '',
                'created_at'    => date('Y-m-d H:i',time())
            ],

        ]);
    }
}
