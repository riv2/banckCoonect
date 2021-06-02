<?php

use App\Models\Offer;
use App\Models\Pp;
use Illuminate\Database\Seeder;

class OffersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Offer::class, 30)->create();
    }
}
