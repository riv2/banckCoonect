<?php

use App\{ProjectSection};
use Illuminate\Database\Seeder;

class AddAgitatorTransactionToProjectSection extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $oProjectSection = new ProjectSection();
        $oProjectSection->url     = 'agitator_transactions';
        $oProjectSection->name_ru = 'Агитаторы: Транзакции';
        $oProjectSection->project = ProjectSection::PROJECT_ADMIN;
        $oProjectSection->save();

    }
}
