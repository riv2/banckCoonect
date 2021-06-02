<?php

use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('_rights_permissions')->where('code', 'MASTER_CURRENCY')->first()) {
            DB::table('_rights_permissions')->insert([
                [
                    'name'         => 'Настройка валют',
                    'code'         => 'MASTER_CURRENCY',
                    'date_deleted' => 0
                ]
            ]);
        }

        if (!DB::table('_rights_permissions')->where('code', 'MASTER_GATEWAYS')->first()) {
            DB::table('_rights_permissions')->insert([
                [
                    'name'         => 'Настройка платежных шлюзов',
                    'code'         => 'MASTER_GATEWAYS',
                    'date_deleted' => 0
                ]
            ]);
        }

        if (!DB::table('_rights_permissions')->where('code', 'ADD_BALANCE_GATEWAYS')->first()) {
            DB::table('_rights_permissions')->insert([
                [
                    'name'         => 'Добавление баланса через шлюзы',
                    'code'         => 'ADD_BALANCE_GATEWAYS',
                    'date_deleted' => 0
                ]
            ]);
        }

        if (!DB::table('_rights_permissions')->where('code', 'MASTER_PRODUCT')->first()) {
            DB::table('_rights_permissions')->insert([
                [
                    'name'         => 'Настройка продуктов',
                    'code'         => 'MASTER_PRODUCT',
                    'date_deleted' => 0
                ]
            ]);
        }

        if (!DB::table('_rights_permissions')->where('code', 'BUY_COINS')->first()) {
            DB::table('_rights_permissions')->insert([
                [
                    'name'         => 'Покупка коинов',
                    'code'         => 'BUY_COINS',
                    'date_deleted' => 0
                ]
            ]);
        }

        if (!DB::table('_rights_permissions')->where('code', 'MY_COMPANY_CREATE')->first()) {
            DB::table('_rights_permissions')->insert([
                [
                    'name'         => 'Сознание моих компаний',
                    'code'         => 'MY_COMPANY_CREATE',
                    'date_deleted' => 0
                ]
            ]);
        }





    }
}