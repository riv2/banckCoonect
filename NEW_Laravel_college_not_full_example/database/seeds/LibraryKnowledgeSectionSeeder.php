<?php

use App\LibraryKnowledgeSection;
use Illuminate\Database\Seeder;

class LibraryKnowledgeSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
        	'Экономика',
        	'Биология',
        	'Менеджмент',
        	'Математика',
        	'Информатика',
        	'Химия',
        	'Психология',
            'Педагогика',
            'Дефектология',
            'Образование',
            'Иностранные языки',
            'История',
            'Программирование',
            'Электроника',
            'Радиотехника'
        ];

        foreach ($records as $value) {
        	LibraryKnowledgeSection::updateOrCreate([
        		'name' => $value
        	]);
        }
    }
}
