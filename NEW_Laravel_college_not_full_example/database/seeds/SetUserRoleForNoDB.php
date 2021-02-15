<?php

use App\ProjectSection;
use Illuminate\Database\Seeder;

class SetUserRoleForNoDB extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $oProjectSection = new ProjectSection();
        $oProjectSection->url     = 'nobd_data';
        $oProjectSection->name_ru = 'Данные НОБД';
        $oProjectSection->project = ProjectSection::PROJECT_ADMIN;
        $oProjectSection->save();

    }
}
