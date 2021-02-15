<?php

use Illuminate\Database\Seeder;

class ImportDisciplines extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fileName = storage_path('import/disciplines.csv');
        $i = 1;

        if(!file_exists($fileName))
        {
            throw new Exception('Import file not found');
        }

        $handler = fopen($fileName, "rb");
        $head = fgetcsv($handler, 0, '#');

        $tabooDelete = [];
        while (!feof($handler))
        {
            $row = fgetcsv($handler, 0, '#');

            if(!$row[0] || count($row) == 0)
            {
                continue;
            }

            $id = $row[0];
            $tabooDelete[] = $id;
            $discipline = \App\Discipline::where('id', $id)->first();

            if(!$discipline)
            {
                $discipline = new \App\Discipline();
                $discipline->id = $id;
            }

            $discipline->name                   = $row[1] ?? '';
            $discipline->credits                = $row[2] ?? 0;
            $discipline->kz                     = (isset($row[3]) && is_numeric($row[3])) ? $row[3] : 0;
            $discipline->ru                     = (isset($row[4]) && is_numeric($row[4])) ? $row[4] : 0;
            $discipline->en                     = (isset($row[5]) && is_numeric($row[5])) ? $row[5] : 0;
            $discipline->module_number          = $row[6] ?? '';
            $discipline->name_kz                = $row[10] ?? '';
            $discipline->name_en                = $row[11] ?? '';
            $discipline->num_ru                 = $row[12] ?? '';
            $discipline->num_kz                 = $row[13] ?? '';
            $discipline->num_en                 = $row[14] ?? '';
            $discipline->dependence             = $row[15] ?? '';
            $discipline->dependence2            = $row[16] ?? '';
            $discipline->dependence3            = $row[17] ?? '';
            $discipline->dependence4            = $row[18] ?? '';
            $discipline->dependence5            = $row[19] ?? '';
            $discipline->discipline_cicle       = $row[20] ?? '';
            $discipline->mt_tk                  = $row[21] ?? '';
            $discipline->ects                   = (isset($row[22]) && is_numeric($row[22])) ? $row[22] : 0;
            $discipline->description            = $row[23] ?? '';
            $discipline->description_en         = $row[24] ?? '';
            $discipline->description_kz         = $row[25] ?? '';
            $discipline->lang                   = $row[26] ?? '';
            $discipline->save();

            $i++;
        }

        \App\Discipline::whereNotIn('id', $tabooDelete)->delete();
        \App\Discipline::whereNotIn('id', $tabooDelete)->delete();
    }
}
