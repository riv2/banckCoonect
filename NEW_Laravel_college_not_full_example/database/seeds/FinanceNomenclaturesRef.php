<?php

use Illuminate\Database\Seeder;

class FinanceNomenclaturesRef extends Seeder
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
                'code'       => '00000003274',
                'name'       => 'Получение справки по месту требования',
                'name_kz'    => 'Оқып жатқан жері туралы анықтаманы алу ақысы',
                'name_en'    => 'Reference for submission upon request',
                'type'       => 'fee',
                'cost'       => 500,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'code'       => '00000003274',
                'name'       => 'Cправка в военкомат',
                'name_kz'    => 'Әскери комиссариатқа анықтама',
                'name_en'    => 'Reference to the military commissariat',
                'type'       => 'fee',
                'cost'       => 500,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'code'       => '00000003274',
                'name'       => 'Справка в ГЦВП4',
                'name_kz'    => 'Зейнетақы төлеуі жөніндегі мемлекеттік орталыққа анықтама 4',
                'name_en'    => 'Reference to the state pension payment center type 4',
                'type'       => 'fee',
                'cost'       => 500,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'code'       => '00000003274',
                'name'       => 'Справка в ГЦВП21',
                'name_kz'    => 'Зейнетақы төлеуі жөніндегі мемлекеттік орталыққа анықтама 21',
                'name_en'    => 'Reference to the state pension payment center type 21',
                'type'       => 'fee',
                'cost'       => 500,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'code'       => '00000003274',
                'name'       => 'Справка в ГЦВП6',
                'name_kz'    => 'Зейнетақы төлеуі жөніндегі мемлекеттік орталыққа анықтама 6',
                'name_en'    => 'Reference to the state pension payment center type 6',
                'type'       => 'fee',
                'cost'       => 500,
                'created_at' => date('Y-m-d H:i:s')
            ],
            /*
            [
                'code'       => '00000003274',
                'name'       => 'Справка транскрипт',
                'name_kz'    => 'Зейнетақы транскрипт',
                'name_en'    => 'Transcript enquire',
                'type'       => 'fee',
                'cost'       => 500,
                'created_at' => date('Y-m-d H:i:s')
            ],
            */
        ];

        foreach ($nomenclatureList as $nomenclature)
        {
            
            $nomModel = \App\FinanceNomenclature::where('name_en', $nomenclature['name_en'])->first();

            if(!$nomModel) {
                $nomModel = new \App\FinanceNomenclature();
            }

            $nomModel->fill($nomenclature);
            $nomModel->save();
        }
            
        $nomModel = \App\FinanceNomenclature::where('name_en', 'Transcript enquire')->first();
        if($nomModel) {
            $nomModel->delete();
        }

        $nomModel = \App\FinanceNomenclature::where('name_en', 'Reference to the state pension payment center')
            ->first();
        if($nomModel) {
            $nomModel->delete();
        }
        

    }
}
