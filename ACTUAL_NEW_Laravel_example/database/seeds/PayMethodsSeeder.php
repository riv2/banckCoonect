<?php

use App\Models\PayMethod;
use Illuminate\Database\Seeder;

class PayMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $values = [
            1 => 'Р/с юридического лица',
            2 => 'Банковская карта',
            3 => 'WebMoney',
        ];
        foreach ($values as $id => $caption) {
            $item = new PayMethod();
            $item->id = $id;
            $item->caption = $caption;
            $item->save();
        }
    }
}
