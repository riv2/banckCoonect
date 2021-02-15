<?php

use Illuminate\Database\Seeder;

class WifiTariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataList = [
            [
                'name' => 'One day',
                'cost' => 100,
                'mins' => 1440,
                'value' => 500
            ]
        ];

        foreach ($dataList as $row)
        {
            $model = new \App\WifiTariff();
            $model->fill($row);
            $model->save();
        }
    }
}
