<?php

use Illuminate\Database\Seeder;

class PromotionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $promotionWork = \App\Promotion::where('name', 'working_student')->first();

        if(!$promotionWork)
        {
            $promotionWork = new \App\Promotion();
        }

        $promotionWork->name = 'working_student';
        $promotionWork->description = 'For working students a discount. Download the required documents.';
        $promotionWork->discount = 25;
        $promotionWork->status = 'active';

        $promotionWork->save();
    }
}
