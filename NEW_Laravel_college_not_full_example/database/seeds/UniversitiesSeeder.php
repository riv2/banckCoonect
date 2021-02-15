<?php

use Illuminate\Database\Seeder;

class UniversitiesSeeder extends Seeder
{
    const FIELD_ID = 0;
    const FIELD_NAME = 1;
    const FIELD_RECTOR_FIO = 2;
    const FIELD_SCIENCE_RECTOR_FIO = 3;
    const FIELD_CONTACT = 4;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = fopen(storage_path('import/univer_list.csv'), 'r');

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $univer = \App\University::where('id', $row[self::FIELD_ID])->first();

            if(!$univer)
            {
                $univer = new \App\University();
            }

            $univer->name = $row[self::FIELD_NAME];
            $univer->rector_fio = $row[self::FIELD_RECTOR_FIO];
            $univer->science_rector_fio = $row[self::FIELD_SCIENCE_RECTOR_FIO];
            $univer->contact = $row[self::FIELD_CONTACT];
            $univer->save();
        }
    }
}
