<?php

use Illuminate\Database\Seeder;

class FinanceNomenclatureAddAdditionServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $nomenclatureList = [
            [
                'code'       => '00000003284',
                'name'       => 'Базовый пакет студента',
                'name_kz'    => 'Негізгі студенттік пакеті',
                'type'       => 'fee',
                'cost'       => 2000,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($nomenclatureList as $nomenclature)
        {
            $nomModel = \App\FinanceNomenclature::where('code', $nomenclature['code'])->first();

            if(!$nomModel)
            {
                $nomModel = new \App\FinanceNomenclature();
            }

            $nomModel->fill($nomenclature);
            $nomModel->save();
        }

    }
}
