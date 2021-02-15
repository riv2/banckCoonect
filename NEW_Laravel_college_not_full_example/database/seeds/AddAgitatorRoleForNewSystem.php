<?php

use App\Role;
use Illuminate\Database\Seeder;

class AddAgitatorRoleForNewSystem extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $oRole = new Role();
        $oRole->name = 'agitator';
        $oRole->title_ru = 'Агитатор';
        $oRole->can_set_pay_in_orcabinet = 0;
        $oRole->created_at = date('Y-m-d H:i:s');
        $oRole->save();
    }
}
