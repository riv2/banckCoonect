<?php

use Illuminate\Database\Seeder;

class BanksListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataList = [
            ['bic' => 'ABNAKZKX', 'name' => 'АО "First Heartland Bank"'],
            ['bic' => 'ALFAKZKA', 'name' => 'АО "Дочерний Банк "АЛЬФА-БАНК"'],
            ['bic' => 'ALMNKZKA', 'name' => 'АО "АТФБанк"'],
            ['bic' => 'ATYNKZKA', 'name' => 'АО "Altyn Bank" (ДБ China Citic Bank Corporation Limited)"'],
            ['bic' => 'BKCHKZKA', 'name' => 'АО ДБ "БАНК КИТАЯ В КАЗАХСТАНЕ"'],
            ['bic' => 'CASPKZKA', 'name' => 'АО "KASPI BANK"'],
            ['bic' => 'CEDUKZKA', 'name' => 'АО "Центральный Депозитарий Ценных Бумаг"'],
            ['bic' => 'CITIKZKA', 'name' => 'АО "Ситибанк Казахстан"'],
            ['bic' => 'DVKAKZKA', 'name' => 'АО "Банк Развития Казахстана"'],
            ['bic' => 'EABRKZKA', 'name' => 'ЕВРАЗИЙСКИЙ БАНК РАЗВИТИЯ'],
            ['bic' => 'EURIKZKA', 'name' => 'АО "Евразийский Банк"'],
            ['bic' => 'GCVPKZ2A', 'name' => 'НАО Государственная корпорация "Правительство для граждан"'],
            ['bic' => 'HCSKKZKA', 'name' => 'АО "Жилстройсбербанк Казахстана"'],
            ['bic' => 'HLALKZKZ', 'name' => 'АО "Исламский Банк "Al Hilal"'],
            ['bic' => 'HSBKKZKX', 'name' => 'АО "Народный Банк Казахстана"'],
            ['bic' => 'ICBKKZKX', 'name' => 'АО "Торгово-промышленный Банк Китая в г. Алматы"'],
            ['bic' => 'INEARUMM', 'name' => 'г.Москва Межгосударственный Банк'],
            ['bic' => 'INLMKZKA', 'name' => 'ДБ АО "Хоум Кредит энд Финанс Банк"'],
            ['bic' => 'IRTYKZKA', 'name' => 'АО "ForteBank"'],
            ['bic' => 'KCJBKZKX', 'name' => 'АО "Банк ЦентрКредит"'],
            ['bic' => 'KICEKZKX', 'name' => 'АО "Казахстанская фондовая биржа"'],
            ['bic' => 'KINCKZKA', 'name' => 'АО "Банк "Bank RBK"'],
            ['bic' => 'KISCKZKX', 'name' => 'РГП "Казахстанский центр межбанковских расчетов НБРК"'],
            ['bic' => 'KKMFKZ2A', 'name' => 'РГУ "Комитет казначейства Министерства финансов РК"'],
            ['bic' => 'KPSTKZKA', 'name' => 'АО "КАЗПОЧТА"'],
            ['bic' => 'KSNVKZKA', 'name' => 'АО "Банк Kassa Nova" (Дочерний банк АО "ForteBank")"'],
            ['bic' => 'KZIBKZKA', 'name' => 'АО "ДБ "КАЗАХСТАН-ЗИРААТ ИНТЕРНЕШНЛ БАНК"'],
            ['bic' => 'LARIKZKA', 'name' => 'АО "AsiaCredit Bank (АзияКредит Банк)"'],
            ['bic' => 'NBPAKZKA', 'name' => 'АО ДБ "Национальный Банк Пакистана" в Казахстане"'],
            ['bic' => 'NBPFKZKX', 'name' => 'Банк-кастодиан АО  "ЕНПФ"'],
            ['bic' => 'NBRKKZKX', 'name' => 'РГУ Национальный Банк Республики Казахстан"'],
            ['bic' => 'NURSKZKX', 'name' => 'АО "Нурбанк"'],
            ['bic' => 'SABRKZKA', 'name' => 'ДБ АО "Сбербанк"'],
            ['bic' => 'SHBKKZKA', 'name' => 'АО "Шинхан Банк Казахстан"'],
            ['bic' => 'TBKBKZKA', 'name' => 'АО "Capital Bank Kazakhstan"'],
            ['bic' => 'TNGRKZKX', 'name' => 'АО "Tengri Bank"'],
            ['bic' => 'TSESKZKA', 'name' => 'АО "First Heartland Jysan Bank"'],
            ['bic' => 'VTBAKZKZ', 'name' => 'ДО АО Банк ВТБ (Казахстан)"'],
            ['bic' => 'ZAJSKZ22', 'name' => 'АО "Исламский банк "Заман-Банк"'],
        ];

        foreach ($dataList as $row)
        {
            $model = new \App\Bank();
            $model->fill($row);
            $model->save();
        }
    }
}
