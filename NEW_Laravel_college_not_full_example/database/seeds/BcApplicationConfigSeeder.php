<?php

use Illuminate\Database\Seeder;

class BcApplicationConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\BcApplicationConfig::create();
    }
}
