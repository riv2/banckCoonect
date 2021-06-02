<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Справочники
        $this->call(PayMethodsSeeder::class);
        $this->call(TrafficSourcesSeeder::class);

        $this->call(TestDataSeeder::class);

        //Пользователи
        $this->call(PpTableSeeder::class);
        // $this->call(UsersPayMethodsSeeder::class);
        //Офферы
        $this->call(OffersTableSeeder::class);
        $this->call(OfferMaterialsSeeder::class);
        $this->call(RateRulesSeeder::class);
        //Линки
        $this->call(LinksTableSeeder::class);
        //Заявки (заказы)
        $this->call(OrdersTableSeeder::class);
        $this->call(OrdersProductsSeeder::class);
    }
}
