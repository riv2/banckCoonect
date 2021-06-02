<?php

use App\Models\Offer;
use App\Models\Pp;
use App\User;
use Illuminate\Database\Seeder;

class PpTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Pp::class, 3)
            ->create()
            ->each(function (Pp $pp) {
                // мульти чет не пашет
                for ($i=0; $i < 2; $i++) {
                    $pp->users()->save(factory(User::class)->make());
                }
                $pp->offers()->createMany(factory(Offer::class, 3)->make()->toArray());
            });

        // DB::insert("INSERT INTO `rate_rules` (`partner_id`, `user_cat`, `business_unit_id`, `fee`, `fee_advert`, `pp_id`, `offer_id`, `date_start`, `date_end`, `created_at`, `updated_at`, `deleted_at`)
        // VALUES
        //     (NULL, NULL, NULL, 3.00, NULL, 1, 11, '2020-11-01 00:00:00', NULL, '2020-11-26 16:44:49', '2020-11-26 16:44:49', NULL),
        //     (NULL, NULL, NULL, 10.00, NULL, 1, 10, '2020-10-01 00:00:00', NULL, '2020-11-27 17:51:44', '2020-11-27 17:51:44', NULL),
        //     (NULL, NULL, NULL, 20.00, NULL, 1, 11, '2020-10-01 00:00:00', NULL, '2020-11-27 17:52:30', '2020-11-27 17:52:30', NULL);
        // ");

        // DB::insert("INSERT INTO `links` (`pp_id`, `partner_id`, `link_name`, `link`, `link_source`, `offer_id`, `status`, `created_at`, `updated_at`)
        // VALUES
        //     (1, 4, 'Партнерские программы', 'https://gocpa.ru/?utm_source=partners&utm_medium=cpa&utm_campaign=1&utm_content=4', NULL, 10, 'ACTIVE', '2020-10-27 18:52:53', NULL),
        //     (1, 4, 'Партнерские программы', 'https://gocpa.ru/?utm_source=partners&utm_medium=cpa&utm_campaign=2&utm_content=4', NULL, 10, 'ACTIVE', '2020-12-01 23:45:46', NULL);
        // ");

        // DB::insert("INSERT INTO `news` (`id`, `pp_id`, `news_title`, `news_text`, `send_to`, `send_to_value`, `deleted_at`, `created_at`, `updated_at`)
        // VALUES
        //     (1, 1, 'Тестовая новость ПП 1', '<p>Реферат по гироскопии</p>\r\n<p>Тема: «Твердый гирогоризонт: предпосылки и развитие»</p>\r\n<p>Управление полётом самолёта безусловно переворачивает нестационарный волчок. Расчеты </p>\r\n<p>предсказывают, что регулярная прецессия влияет на составляющие гироскопического </p>\r\n<p>момента больше, чем астатический центр подвеса. Угол курса стабилизирует момент. В силу принципа виртуальных скоростей,  ошибка проецирует газообразный период. Исходя из астатической системы координат Булгакова, дифференциальное уравнение астатично.</p>\r\n<p>Точность тангажа опасна. Отсутствие трения определяет центр подвеса. Волчок, в соответствии с модифицированным уравнением Эйлера, очевиден.</p>\r\n<p>Погрешность изготовления трудна в описании. Непосредственно из законов сохранения следует, что астатическая система координат Булгакова горизонтальна. Угловая скорость искажает дифференциальный кожух, что обусловлено малыми углами карданового подвеса. Согласно теории устойчивости движения траектория неподвижно преобразует механический тангаж, исходя из суммы моментов. Тангаж представляет собой экваториальный момент.</p>', 'all', NULL, NULL, '2020-10-05 16:15:47', '2020-10-05 16:15:47'),
        //     (2, 1, 'Новость от Егора', '<p>Тест Тест Тест<!----><!----><!--StartFragment-->Тест Тест Тест<!--StartFragment-->Тест Тест Тест<!--EndFragment--><!--EndFragment--><!----><!----><!----></p>', 'all', NULL, NULL, '2020-10-05 16:54:12', '2020-10-05 16:54:12');
        // ");

        // DB::insert("INSERT INTO `clicks` (`pp_id`, `partner_id`, `link_id`, `click_id`, `web_id`, `pixel_log_id`) VALUES (1, 4, 1, NULL, NULL, 44927);");
        // DB::insert("INSERT INTO `clicks` (`pp_id`, `partner_id`, `link_id`, `click_id`, `web_id`, `pixel_log_id`) VALUES (1, 4, 1, NULL, NULL, 44928);");
        // DB::insert("INSERT INTO `clicks` (`pp_id`, `partner_id`, `link_id`, `click_id`, `web_id`, `pixel_log_id`) VALUES (1, 4, 1, NULL, NULL, 44929);");
    }
}
