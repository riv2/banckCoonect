<?php

use App\Models\OfferMaterial;
use Illuminate\Database\Seeder;

class OfferMaterialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(OfferMaterial::class, 30)->create();
    }
}
