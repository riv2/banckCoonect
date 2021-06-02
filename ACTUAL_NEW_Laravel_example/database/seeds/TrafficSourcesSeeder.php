<?php

use App\Models\TrafficSource;
use Illuminate\Database\Seeder;

class TrafficSourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $values = [
            1 => 'Google Ads',
            2 => 'Яндекс Директ',
            3 => 'Facebook Ads',
            4 => 'YouTube',
            5 => 'MyTarget',
            6 => 'Контентные сайты',
            7 => 'Баннерная реклама',
            8 => 'Тизерная реклама',
            9 => 'Pre-roll',
            10 => 'Брокерский трафик',
            11 => 'E-mail рассылки',
            12 => 'SMS рассылки',
            13 => 'Doorway-трафик',
            14 => 'Мотивированный трафик',
            15 => 'Clickunder/Popunder',
            16 => 'Социальные сети',
            17 => 'Контекстная реклама на бренд',
            18 => 'Скидочные и купонные агрегаторы',
            19 => 'Cashback-сервисы',
            20 => 'Ретаргетинг',
            21 => 'Adult-трафик',
            22 => 'Авто-редирект',
            23 => 'Блокировка контента',
        ];
        foreach ($values as $id => $title) {
            $item = new TrafficSource;
            $item->id = $id;
            $item->title = $title;
            $item->save();
        }
    }
}
