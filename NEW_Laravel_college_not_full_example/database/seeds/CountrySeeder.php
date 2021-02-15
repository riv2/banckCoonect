<?php

use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fileName = storage_path('import/nationality.csv');
        /*$fileRu = storage_path('import/cityRu.json');
        $fileKz = storage_path('import/cityKz.json');*/

        if(!file_exists($fileName))
        {
            throw new Exception('Import file not found');
        }

        $handler = fopen($fileName, "rb");
        $tabooList = [];

        while (!feof($handler)) {
            $row = fgetcsv($handler, 0, '#');

            if (!$row[0] || count($row) == 0) {
                continue;
            }

            $model = \App\Country::where('name', $row[1])->first();

            if(!$model)
            {
                $model = new \App\Country();
            }

            $model->name = $row[1];
            $model->save();

            $tabooList[] = $model->id;

            /*file_put_contents($fileRu, '"' . $row[1] . '": "' . $row[0] . '",' . "\r\n", FILE_APPEND);
            file_put_contents($fileKz, '"' . $row[1] . '": "' . $row[2] . '",' . "\r\n", FILE_APPEND);*/
        }

        \App\Country::whereNotIn('id', $tabooList)->delete();
    }
}
