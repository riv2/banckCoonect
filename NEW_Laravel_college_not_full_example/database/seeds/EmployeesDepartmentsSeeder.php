<?php

use App\EmployeesDepartment;
use Illuminate\Database\Seeder;

class EmployeesDepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = [
        	[
        		'id' => 1, 
        		'name' => 'Отдел по развитию', 
        		'superviser' => 34
        	],
        	[
        		'id' => 3, 
        		'name' => 'Отдел информационных технологий', 
        		'superviser' => 34
        	],
        	[
        		'id' => 4, 
        		'name' => 'Отдел административного управления и кадров', 
        		'superviser' => 1
        	],
        	[
        		'id' => 5, 
        		'name' => 'Отдел организации практик и трудоустройства', 
        		'superviser' => 8
        	],
        	[
        		'id' => 6, 
        		'name' => 'Отдел по оперативному управлению', 
        		'superviser' => 34
        	],
        	[
        		'id' => 7, 
        		'name' => 'Комитет по делам молодежи', 
        		'superviser' => 58
        	],
        	[
        		'id' => 8, 
        		'name' => 'УМУ', 
        		'superviser' => 58
        	],
        	[
        		'id' => 9, 
        		'name' => 'Офис-регистратор', 
        		'superviser' => 58
        	],
        	[
        		'id' => 11, 
        		'name' => 'Бухгалтерия', 
        		'superviser' => 34
        	],
        	[
        		'id' => 12, 
        		'name' => 'Отдел маркетинга', 
        		'superviser' => 58
        	],
        	[
        		'id' => 14, 
        		'name' => 'Отдел магистратуры', 
        		'superviser' => 58
        	],
        	[
        		'id' => 15, 
        		'name' => 'Административно-хозяйственная часть', 
        		'superviser' => 34
        	],
        	[
        		'id' => 16, 
        		'name' => 'Здрав.пункт', 
        		'superviser' => 34
        	],
        	[
        		'id' => 17, 
        		'name' => 'Студенческая канцелярия', 
        		'superviser' => 34
        	],
        	[
        		'id' => 18, 
        		'name' => 'Научно-исследовательский центр', 
        		'superviser' => 58
        	],
        	[
        		'id' => 19, 
        		'name' => 'Архив', 
        		'superviser' => 58
        	],
        	[
        		'id' => 20, 
        		'name' => 'Центр обслуживания студентов', 
        		'superviser' => 58
        	],
        	[
        		'id' => 21, 
        		'name' => 'Центр тестирования', 
        		'superviser' => 9
        	],
        	[
        		'id' => 22, 
        		'name' => 'Секретариат', 
        		'superviser' => 6
        	],
        	[
        		'id' => 34, 
        		'name' => 'Аппарат Президента', 
        		'superviser' => 58
        	],
        	[
        		'id' => 45, 
        		'name' => 'Деканат факультета педагогики, искусства и языков', 
        		'superviser' => 34
        	],
        	[
        		'id' => 46, 
        		'name' => 'Деканат факультета экономики, права и информационных технологий', 
        		'superviser' => 34
        	],
        	[
        		'id' => 47, 
        		'name' => 'IT отдел', 
        		'superviser' => 34
        	],
        	[
        		'id' => 57, 
        		'name' => 'Приемная комиссия', 
        		'superviser' => 45
        	],
        	[
        		'id' => 58, 
        		'name' => 'Ректорат', 
        		'superviser' => 34
        	],
        	[
        		'id' => 63, 
        		'name' => 'Отдел международных связей', 
        		'superviser' => 58
        	],
        	[
        		'id' => 64, 
        		'name' => 'Научно-Технический Совет', 
        		'superviser' => 58
        	],
        	[
        		'id' => 65, 
        		'name' => 'Офис коммерциализации', 
        		'superviser' => 58
        	],
        	[
        		'id' => 66, 
        		'name' => 'Штаб ГО', 
        		'superviser' => 34
        	],
        	[
        		'id' => 67, 
        		'name' => 'Образовательно-информационный центр', 
        		'superviser' => 8
        	],
        	[
        		'id' => 68, 
        		'name' => 'Служба диспетчеров', 
        		'superviser' => 8
        	],
        	[
        		'id' => 70, 
        		'name' => 'Финансовый отдел', 
        		'superviser' => 58
        	],
        	[
        		'id' => 71, 
        		'name' => 'Ученый совет', 
        		'superviser' => 34
        	],
        	[
        		'id' => 72, 
        		'name' => 'Отдел по социальной и воспитательной работе', 
        		'superviser' => 58
        	],
        	[
        		'id' => 73, 
        		'name' => 'Сектор художественного труда и дизайна', 
        		'superviser' => 45,
                'is_sector' => 1,
        	],
        	[
        		'id' => 74, 
        		'name' => 'Сектор иностранных языков', 
        		'superviser' => 45,
                'is_sector' => 1,
        	],
        	[
        		'id' => 75, 
        		'name' => 'Сектор педагогики и психологии, дефектологии, педагогики и методики начального обучения, учитель информатики', 
        		'superviser' => 45,
                'is_sector' => 1,
        	],
        	[
        		'id' => 76, 
        		'name' => 'Сектор права', 
        		'superviser' => 45,
                'is_sector' => 1,
        	],
        	[
        		'id' => 77, 
        		'name' => 'Сектор IT и телекоммуникаций', 
        		'superviser' => 46,
                'is_sector' => 1,
        	],
        	[
        		'id' => 78, 
        		'name' => 'Сектор казахского языка и литературы', 
        		'superviser' => 45,
                'is_sector' => 1,
        	],
        	[
        		'id' => 79, 
        		'name' => 'Сектор бизнеса и финансов', 
        		'superviser' => 46,
                'is_sector' => 1,
        	],
        	[
        		'id' => 80, 
        		'name' => 'Сектор химии и биологии, физической культуры и спорта', 
        		'superviser' => 45,
                'is_sector' => 1,
        	],
        	[
        		'id' => 81, 
        		'name' => 'Редакционно-издательский отдел', 
        		'superviser' => 58
        	],
        	[
        		'id' => 82, 
        		'name' => 'Сектор учета и аудита', 
        		'superviser' => 46
        	],
        	[
        		'id' => 83, 
        		'name' => 'Сектор экономики и управления', 
        		'superviser' => 46,
                'is_sector' => 1,
        	],
        	[
        		'id' => 84, 
        		'name' => 'Сектор туризма, управления гостеприимством', 
        		'superviser' => 46,
                'is_sector' => 1,
        	],
        	[
        		'id' => 85, 
        		'name' => 'Бизнес-инкубатор', 
        		'superviser' => 58
        	],
        	[
        		'id' => 86, 
        		'name' => 'Отдел дистанционно-образовательных технологий', 
        		'superviser' => 8
        	],
        	[
        		'id' => 87, 
        		'name' => 'Центр неформального и дополнительного образования', 
        		'superviser' => 6
        	]
        ];

        foreach ($departments as $value) {
        	EmployeesDepartment::updateOrCreate($value);
        }
    }
}
