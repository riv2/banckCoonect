<?php

use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('_billing_products')->where('code', 'ADD_GATEWAY_BALANCE')->where('advanced_value', 'ROBOKASSA')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '1',
                    'name'           => 'Пополнение через робокассу',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '',
                    'table'          => '',
                    'status'         => 'hidden',
                    'code'           => 'ADD_GATEWAY_BALANCE',
                    'advanced_value' => 'ROBOKASSA'
                ]
            ]);
        }

        if (!DB::table('_billing_products')->where('code', 'ADD_MANUAL_BALANCE')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '2',
                    'name'           => 'Ручное пополнение баланса',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '',
                    'table'          => '',
                    'status'         => 'hidden',
                    'code'           => 'ADD_MANUAL_BALANCE',
                    'advanced_value' => '-- uniq пополнителя --'
                ]
            ]);
        }

        if (!DB::table('_billing_products')->where('code', 'ADD_REMAIN_BALANCE')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '3',
                    'name'           => 'Возврат средств по подписке',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '',
                    'table'          => '',
                    'status'         => 'hidden',
                    'code'           => 'ADD_REMAIN_BALANCE',
                    'advanced_value' => ''
                ]
            ]);
        }

        if (!DB::table('_billing_products')->where('code', 'COINS_COST')->where('uniq_table', '1')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '4',
                    'name'           => 'Стоимость метра квадрат - Development',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '1',
                    'table'          => '_subscribes',
                    'status'         => 'hidden',
                    'code'           => 'COINS_COST',
                    'advanced_value' => ''
                ]
            ]);
        }

        if (!DB::table('_billing_products')->where('code', 'COINS_COST')->where('uniq_table', '2')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '5',
                    'name'           => 'Стоимость метра квадрат - Free',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '2',
                    'table'          => '_subscribes',
                    'status'         => 'hidden',
                    'code'           => 'COINS_COST',
                    'advanced_value' => ''
                ]
            ]);
        }

        if (!DB::table('_billing_products')->where('code', 'COINS_COST')->where('uniq_table', '3')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '6',
                    'name'           => 'Стоимость метра квадрат - Enthusiast',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '3',
                    'table'          => '_subscribes',
                    'status'         => 'hidden',
                    'code'           => 'COINS_COST',
                    'advanced_value' => ''
                ]
            ]);
        }

        if (!DB::table('_billing_products')->where('code', 'COINS_COST')->where('uniq_table', '4')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '7',
                    'name'           => 'Стоимость метра квадрат - Professional',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '4',
                    'table'          => '_subscribes',
                    'status'         => 'hidden',
                    'code'           => 'COINS_COST',
                    'advanced_value' => ''
                ]
            ]);
        }


        if (!DB::table('_billing_products')->where('code', 'COINS_COST')->where('uniq_table', '5')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '8',
                    'name'           => 'Стоимость метра квадрат - Limitless',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '5',
                    'table'          => '_subscribes',
                    'status'         => 'hidden',
                    'code'           => 'COINS_COST',
                    'advanced_value' => ''
                ]
            ]);
        }

        if (!DB::table('_billing_products')->where('code', 'COINS_COST')->where('uniq_table', '5')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '8',
                    'name'           => 'Стоимость метра квадрат - Limitless',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '5',
                    'table'          => '_subscribes',
                    'status'         => 'hidden',
                    'code'           => 'COINS_COST',
                    'advanced_value' => ''
                ]
            ]);
        }


        if (!DB::table('_billing_products')->where('code', 'COINS_COST')->where('uniq_table', '5')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '9',
                    'name'           => 'Стоимость метра квадрат - Limitless',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '5',
                    'table'          => '_subscribes',
                    'status'         => 'hidden',
                    'code'           => 'COINS_COST',
                    'advanced_value' => ''
                ]
            ]);
        }

        if (!DB::table('_billing_products')->where('code', 'SUBSCRIBE_COST')->where('uniq_table', '1')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '10',
                    'name'           => 'Стоимость подписки - Development',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '1',
                    'table'          => '_subscribes',
                    'status'         => 'hidden',
                    'code'           => 'SUBSCRIBE_COST',
                    'advanced_value' => ''
                ]
            ]);
        }

        if (!DB::table('_billing_products')->where('code', 'SUBSCRIBE_COST')->where('uniq_table', '2')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '11',
                    'name'           => 'Стоимость подписки - Free',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '2',
                    'table'          => '_subscribes',
                    'status'         => 'hidden',
                    'code'           => 'SUBSCRIBE_COST',
                    'advanced_value' => ''
                ]
            ]);
        }

        if (!DB::table('_billing_products')->where('code', 'SUBSCRIBE_COST')->where('uniq_table', '3')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '12',
                    'name'           => 'Стоимость подписки - Enthusiast',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '3',
                    'table'          => '_subscribes',
                    'status'         => 'hidden',
                    'code'           => 'SUBSCRIBE_COST',
                    'advanced_value' => ''
                ]
            ]);
        }


        if (!DB::table('_billing_products')->where('code', 'SUBSCRIBE_COST')->where('uniq_table', '4')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '13',
                    'name'           => 'Стоимость подписки - Professional',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '4',
                    'table'          => '_subscribes',
                    'status'         => 'hidden',
                    'code'           => 'SUBSCRIBE_COST',
                    'advanced_value' => ''
                ]
            ]);
        }

        if (!DB::table('_billing_products')->where('code', 'SUBSCRIBE_COST')->where('uniq_table', '5')->first()) {
            DB::table('_billing_products')->insert([
                [
                    'uid'            => '14',
                    'name'           => 'Стоимость подписки - Limitless',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'uniq_table'     => '5',
                    'table'          => '_subscribes',
                    'status'         => 'hidden',
                    'code'           => 'SUBSCRIBE_COST',
                    'advanced_value' => ''
                ]
            ]);
        }


        if (!DB::table('_billing_products_cost')->where('uid_product', '4')->first()) {
            DB::table('_billing_products_cost')->insert([
                [
                    'uid_product'    => '4',
                    'uniq_currency'  => 'RUB',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'cost'           => '1',
                    'count'          => '1'
                ]
            ]);
        }


        if (!DB::table('_billing_products_cost')->where('uid_product', '5')->first()) {
            DB::table('_billing_products_cost')->insert([
                [
                    'uid_product'    => '5',
                    'uniq_currency'  => 'RUB',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'cost'           => '1',
                    'count'          => '1'
                ]
            ]);
        }

        if (!DB::table('_billing_products_cost')->where('uid_product', '6')->first()) {
            DB::table('_billing_products_cost')->insert([
                [
                    'uid_product'    => '6',
                    'uniq_currency'  => 'RUB',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'cost'           => '1',
                    'count'          => '1'
                ]
            ]);
        }

        if (!DB::table('_billing_products_cost')->where('uid_product', '7')->first()) {
            DB::table('_billing_products_cost')->insert([
                [
                    'uid_product'    => '7',
                    'uniq_currency'  => 'RUB',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'cost'           => '1',
                    'count'          => '1'
                ]
            ]);
        }

        if (!DB::table('_billing_products_cost')->where('uid_product', '8')->first()) {
            DB::table('_billing_products_cost')->insert([
                [
                    'uid_product'    => '8',
                    'uniq_currency'  => 'RUB',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'cost'           => '1',
                    'count'          => '1'
                ]
            ]);
        }

        if (!DB::table('_billing_products_cost')->where('uid_product', '9')->first()) {
            DB::table('_billing_products_cost')->insert([
                [
                    'uid_product'    => '9',
                    'uniq_currency'  => 'RUB',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'cost'           => '1',
                    'count'          => '1'
                ]
            ]);
        }

        if (!DB::table('_billing_products_cost')->where('uid_product', '10')->first()) {
            DB::table('_billing_products_cost')->insert([
                [
                    'uid_product'    => '10',
                    'uniq_currency'  => 'RUB',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'cost'           => '1',
                    'count'          => '1'
                ]
            ]);
        }

        if (!DB::table('_billing_products_cost')->where('uid_product', '11')->first()) {
            DB::table('_billing_products_cost')->insert([
                [
                    'uid_product'    => '11',
                    'uniq_currency'  => 'RUB',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'cost'           => '1',
                    'count'          => '1'
                ]
            ]);
        }

        if (!DB::table('_billing_products_cost')->where('uid_product', '12')->first()) {
            DB::table('_billing_products_cost')->insert([
                [
                    'uid_product'    => '12',
                    'uniq_currency'  => 'RUB',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'cost'           => '1',
                    'count'          => '1'
                ]
            ]);
        }

        if (!DB::table('_billing_products_cost')->where('uid_product', '13')->first()) {
            DB::table('_billing_products_cost')->insert([
                [
                    'uid_product'    => '13',
                    'uniq_currency'  => 'RUB',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'cost'           => '1',
                    'count'          => '1'
                ]
            ]);
        }

        if (!DB::table('_billing_products_cost')->where('uid_product', '14')->first()) {
            DB::table('_billing_products_cost')->insert([
                [
                    'uid_product'    => '14',
                    'uniq_currency'  => 'RUB',
                    'date_created'   => '2020-08-12 18:24:57',
                    'date_updated'   => '2020-08-12 18:24:57',
                    'cost'           => '1',
                    'count'          => '1'
                ]
            ]);
        }

        if (!DB::table('_subscribes_options')->where('code', 'COINS_AMOUNT')->where('subscribe_uid', '1')->first()) {
            DB::table('_subscribes_options')->insert([
                [
                    'subscribe_uid'  => '1',
                    'code'           => 'COINS_AMOUNT',
                    'name'           => 'Количество квадратных метров',
                    'limitation'     => '1000',
                    'refresh_period' => '30'
                ]
            ]);
        }

        if (!DB::table('_subscribes_options')->where('code', 'COINS_AMOUNT')->where('subscribe_uid', '2')->first()) {
            DB::table('_subscribes_options')->insert([
                [
                    'subscribe_uid'  => '2',
                    'code'           => 'COINS_AMOUNT',
                    'name'           => 'Количество квадратных метров',
                    'limitation'     => '1000',
                    'refresh_period' => '30'
                ]
            ]);
        }

        if (!DB::table('_subscribes_options')->where('code', 'COINS_AMOUNT')->where('subscribe_uid', '3')->first()) {
            DB::table('_subscribes_options')->insert([
                [
                    'subscribe_uid'  => '3',
                    'code'           => 'COINS_AMOUNT',
                    'name'           => 'Количество квадратных метров',
                    'limitation'     => '1000',
                    'refresh_period' => '30'
                ]
            ]);
        }

        if (!DB::table('_subscribes_options')->where('code', 'COINS_AMOUNT')->where('subscribe_uid', '4')->first()) {
            DB::table('_subscribes_options')->insert([
                [
                    'subscribe_uid'  => '4',
                    'code'           => 'COINS_AMOUNT',
                    'name'           => 'Количество квадратных метров',
                    'limitation'     => '1000',
                    'refresh_period' => '30'
                ]
            ]);
        }

        if (!DB::table('_subscribes_options')->where('code', 'COINS_AMOUNT')->where('subscribe_uid', '5')->first()) {
            DB::table('_subscribes_options')->insert([
                [
                    'subscribe_uid'  => '5',
                    'code'           => 'COINS_AMOUNT',
                    'name'           => 'Количество квадратных метров',
                    'limitation'     => '1000',
                    'refresh_period' => '30'
                ]
            ]);
        }



    }
}



