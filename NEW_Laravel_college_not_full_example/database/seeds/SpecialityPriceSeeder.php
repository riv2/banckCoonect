<?php

use App\BcApplications;
use App\Profiles;
use App\Speciality;
use App\SpecialityPrice;
use Illuminate\Database\Seeder;

class SpecialityPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $specialities = Speciality::get(['id'])->toArray();

        foreach ($specialities as $speciality) {
            SpecialityPrice::createNewAsset($speciality['id']);
        }
    }
}
