<?php

use App\Helpers\ArrayHelper;
use App\Models\Offer;
use App\Models\RateRule;
use Illuminate\Database\Seeder;

class RateRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $offers = Offer::query()->where('id', '=', 10)->get()->toArray();

        foreach ($offers as $item) {
            for ($i = 0; $i < 10; $i++) {
                RateRule::withoutEvents(function () use ($item, $i) {
                    $RateRule = new RateRule();
                    $RateRule->fee = rand(9,76);
                    $RateRule->pp_id = $item['pp_id'];
                    $RateRule->offer_id = $item['id'];
                    $RateRule->date_start = now()->firstOfQuarter();
                    $RateRule->date_end = ArrayHelper::getRandomValue([null, now()->lastOfQuarter()]);
                    $RateRule->created_at = now();
                    $RateRule->save();
                });
            }
        }
    }
}
