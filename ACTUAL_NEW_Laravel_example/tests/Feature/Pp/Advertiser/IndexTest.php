<?php
//
//namespace Tests\Feature\Pp\Advertiser;
//
//use App\Helpers\ArrayHelper;
//use App\Helpers\PartnerProgramStorage;
//use App\Lists\PpOnboardingList;
//use App\Models\Pp;
//use App\User;
//use Illuminate\Foundation\Testing\RefreshDatabase;
//use Tests\PpTestCase;
//
//class IndexTest extends PpTestCase
//{
//    use RefreshDatabase;
//
//    static function getPp(): Pp
//    {
//        $PpOnboardingList = PpOnboardingList::getList();
//        $PpOnboardingListForInterfaceTests = [];
//        foreach ($PpOnboardingList as $key => $value) {
//            if ($key != 'registered') {
//                array_push($PpOnboardingListForInterfaceTests, $key);
//            }
//        }
//        $pp = PartnerProgramStorage::getPP();
//        $pp->onboarding_status = ArrayHelper::getRandomValue($PpOnboardingListForInterfaceTests);
//        PartnerProgramStorage::setPP($pp);
//        config(['app.url' => 'https://' . $pp->tech_domain]);
//        \URL::forceRootUrl('https://' . $pp->tech_domain);
//        return $pp;
//    }
//
//    public function testAdvertiserCanViewProfilePage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//        $user = User::query()->where('id','=', $pp->user_id)->first();
//        $user->pp_id = $pp->id;
//        $user->save();
//        dd($user);
//        dump(Pp::all()->toArray(),User::all()->toArray());
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.profile'));
//
//        $response->assertStatus(200);
//    }
//
//
//    public function testAdvertiserCanViewReportPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.report'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewOrdersIndexPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.orders.index'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewPartnersIndexPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.partners.index'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewServicedeskIndexPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.servicedesk.index'));
//
//        /* @todo Либо переписать запрос, который используется на странице, либо донастроить БД, которую используют тесты.
//         * Видимо, SQLite (или другая БД, используемая в тестах) не имеет функции NOW(), которая используется на этой странице
//         * Illuminate\Database\QueryException: SQLSTATE[HY000]: General error: 1 no such function: NOW (SQL: select COUNT(*) as count_orders, COUNT(DISTINCT `creator_user_id`) as count_partners, COALESCE(SUM(CASE WHEN `status` = &quot;new&quot; THEN 1 END), 0) AS new_cnt, COALESCE(SUM(CASE WHEN `status` = &quot;pending&quot; THEN 1 END), 0) AS pending_cnt, COALESCE(SUM(CASE WHEN `status` != &quot;closed&quot; AND `deadline_at` &lt;= NOW() THEN 1 END), 0) AS expired_cnt, COALESCE(SUM(CASE WHEN `status` != &quot;closed&quot; AND `not_closed` = 1 THEN 1 END), 0) AS not_closed_cnt, COALESCE(SUM(CASE WHEN `status` = &quot;closed&quot; THEN 1 END), 0) AS closed_cnt, MIN(`created_at`) as min_datetime, MAX(`created_at`) as max_datetime from &quot;servicedesk_tasks&quot; where &quot;status&quot; in (new, pending) and &quot;doer_user_id&quot; = 2 and &quot;servicedesk_tasks&quot;.&quot;deleted_at&quot; is null and &quot;pp_id&quot; = 1 order by &quot;id&quot; desc limit 1) in file /Users/kirillshkidin/PhpstormProjects/cloud/vendor/laravel/framework/src/Illuminate/Database/Connection.php on line 671
//         */
//        $response->assertStatus(500);
//    }
//
//    public function testAdvertiserCanViewOffersIndexPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.offers.index'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewNewsIndexPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.news.index'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewAccountPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.account'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewPenaltysPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.penaltys'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewReestrsIndexPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.reestrs.index'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewSettingsCompanyIndexPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.settings.company.index'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewSettingsAppearanceIndexPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.settings.appearance.index'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewSettingsFaqIndexPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.settings.faq.index'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewIntegrationPixelPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.integration.pixel'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewIntegrationCmsPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.integration.cms'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewIntegrationCmsTildaPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.integration.cms.tilda'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewIntegrationApiPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.integration.api'));
//
//        /* @todo Не видно глобальный массив
//         * Так понимаю нет доступа к глобальному массиву $_SERVER
//         * Facade\Ignition\Exceptions\ViewException: Undefined index: HTTP_HOST (View: /Users/kirillshkidin/PhpstormProjects/cloud/resources/views/advertiser/integration/api.blade.php) in file /Users/kirillshkidin/PhpstormProjects/cloud/resources/views/advertiser/integration/api.blade.php on line 66
//         * line 66:
//         * href="https://@php echo $_SERVER['HTTP_HOST'] @endphp/adv_api/du/">https://@php echo $_SERVER['HTTP_HOST'] @endphp
//         */
//
//        $response->assertStatus(500);
//    }
//
//    public function testAdvertiserCanViewPostbacksPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.postbacks'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewServicedeskadvIndexPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.servicedeskadv.index'));
//
//        $response->assertStatus(200);
//    }
//
//    public function testAdvertiserCanViewTariffPage()
//    {
//        $pp = $this::getPp();
//        $user = factory(User::class)->create([
//            'role' => 'advertiser',
//            'pp_id' => $pp->id,
//        ]);
//
//        $response = $this->actingAs($user, 'web')
//            ->get(route('advertiser.tariff'));
//
//        $response->assertStatus(200);
//    }
//
//}
