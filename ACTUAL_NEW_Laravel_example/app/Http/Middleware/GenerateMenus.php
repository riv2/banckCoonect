<?php

namespace App\Http\Middleware;

use App\Models\Pp;
use Closure;
use Spatie\Menu\Laravel\Menu;
use Spatie\Menu\Link;

class GenerateMenus
{
    public static function getOrderOrLead()
    {
        if (auth()->user()->role != 'manager') {
            if (auth()->user()->pp->pp_target == 'products') {
                return __('menu.partner.orders.index.orders');
            } else {
                return __('menu.partner.orders.index.leads');
            }
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Menu::macro('main', function () {
            $rolemenus = [
                'advertiser' => [
                    'allowed' => (auth()->user()->role === 'advertiser'),
                    'items' => [
                        [
                            'icon' => '<i class="far fa-fw fa-chart-bar"></i>',
                            'caption' => __('menu.advertiser.statistics'),
                            'items' => [
                                'advertiser.report' => __('menu.advertiser.report'),
                                'advertiser.orders.index' => GenerateMenus::getOrderOrLead(),

                            ],
                        ],
                        [
                            'icon' => '<i class="fas fa-fw fa-users"></i>',
                            'caption' => __('menu.advertiser.partners'),
                            'items' => [
                                'advertiser.partners.index' => __('menu.advertiser.partners.index'),
                                'advertiser.servicedesk.index' => __('menu.advertiser.servicedesk.index'),
                            ],
                        ],
                        [
                            'icon' => '<i class="far fa-fw fa-gem"></i>',
                            'caption' => __('menu.advertiser.promo'),
                            'items' => [
                                'advertiser.offers.index' => __('menu.advertiser.offers.index'),
                                'advertiser.news.index' => __('menu.advertiser.news.index'),
                            ],
                        ],
                        [
                            'icon' => '<i class="fas fa-fw fa-ban"></i>',
                            'caption' => __('menu.advertiser.ban'),
                            'items' => [
                                'advertiser.banned-links.index' => __('menu.advertiser.banned_links.index'),
                                'advertiser.banned-frauds.index' => __('menu.advertiser.banned_frauds.index'),
                            ],
                        ],
                        [
                            'icon' => '<i class="fas fa-fw fa-coins"></i>',
                            'caption' => __('menu.advertiser.finance'),
                            'items' => [
                                'advertiser.account' => __('menu.advertiser.account'),
                                'advertiser.penaltys.index' => __('menu.advertiser.penaltys.index'),
                                'advertiser.reestrs.index' => __('menu.advertiser.reestrs.index'),
                            ],
                        ],
                        [
                            'icon' => '<i class="fas fa-fw fa-tools"></i>',
                            'caption' => __('menu.advertiser.settings'),
                            'items' => [
                                'advertiser.settings.company.index' => __('menu.advertiser.settings.company.index'),
                                'advertiser.settings.appearance.index' => __('menu.advertiser.settings.appearance.index'),
                                'advertiser.settings.faq.index' => __('menu.advertiser.settings.faq.index'),
                            ],
                        ],
                        [
                            'icon' => '<i class="fas fa-fw fa-cogs"></i>',
                            'caption' => __('menu.advertiser.integrations'),
                            'items' => [
                                'advertiser.integration.pixel' => __('menu.advertiser.integration.pixel'),
                                'advertiser.integration.cms' => __('menu.advertiser.integrations.cms'),
                                'advertiser.integration.api' => __('menu.advertiser.integration.api'),
                                'advertiser.postbacks' => __('menu.advertiser.postbacks'),
                            ],
                        ],
                        [
                            'icon' => '<i class="far fa-fw fa-question-circle"></i>',
                            'caption' => __('menu.advertiser.support'),
                            'items' => [
                                'advertiser.servicedeskadv.index' => __('menu.advertiser.servicedeskadv.index'),
                                'advertiser.tariff' => __('menu.advertiser.tariff'),
                            ],
                        ],
                    ],
                ],
                'manager' => [
                    'allowed' => (auth()->user()->role === 'manager'),
                    'items' => [
                        [
                            'icon' => '<i class="far fa-fw fa-chart-bar"></i>',
                            'caption' => __('menu.manager.statistics'),
                            'items' => [
                                'manager.report' => __('menu.manager.report'),
                                'manager.pixel.index' => __('menu.manager.pixel.index'),
                                //'manager.payments' => __('Финансы'),
                                //'manager.blocks' => __('Блокировки'),
                                //'manager.postbacks' => __('Постбеки'),
                            ],
                        ],
                        [
                            'icon' => '<i class="fas fa-fw fa-users"></i>',
                            'caption' => __('menu.manager.users'),
                            'items' => [
                                'manager.advertisers.index' => __('menu.manager.advertisers'),
                                'manager.partners.index' => __('menu.manager.partners'),
                                'manager.analysts.index' => __('menu.manager.analysts'),
                            ],
                        ],
                        [
                            'icon' => '<i class="far fa-fw fa-gem"></i>',
                            'caption' => __('menu.manager.promo'),
                            'items' => [
                                'manager.offers.index' => __('menu.manager.offers.index'),
                                'manager.traffic.sources' => __('menu.manager.traffic.sources'),
                                //'manager.fees' => __('Ставки'),
                            ],
                        ],
                        [
                            'icon' => '<i class="fas fa-fw fa-info"></i>',
                            'caption' => __('menu.manager.info'),
                            'items' => [
                                'manager.news.index' => __('menu.manager.news.index'),
                                'manager.servicedesk.index' => __('menu.manager.servicedesk.index'),
                                'manager.translations' => __('menu.manager.translations'),
                            ],
                        ],
                        [
                            'icon' => '<i class="fas fa-fw fa-coins"></i>',
                            'caption' => __('menu.manager.finance'),
                            'items' => [
                                'manager.reestrs' => __('menu.manager.reestrs'),
                            ],
                        ],
                        [
                            'icon' => '<i class="fas fa-fw fa-upload"></i>',
                            'caption' => __('menu.manager.import.data'),
                            'items' => [
                                'manager.import' => __('menu.manager.import'),
                            ],
                        ],
                    ],
                ],
                'partner' => [
                    'allowed' => (auth()->user()->role === 'partner'),
                    'items' => [
                        [
                            'icon' => '<i class="far fa-fw fa-chart-bar"></i>',
                            'caption' => __('menu.partner.statistics'),
                            'items' => [
                                'partner.report' => __('menu.partner.report'),
                                'partner.orders.index' => GenerateMenus::getOrderOrLead(),
                            ],
                        ],
                        [
                            'icon' => '<i class="far fa-fw fa-gem"></i>',
                            'caption' => __('menu.partner.promo'),
                            'items' => [
                                'partner.offers.index' => __('menu.partner.offers.index'),
                                //'partner.offers.index' => __('Акции'),
                            ],
                        ],
                        [
                            'icon' => '<i class="fas fa-fw fa-tools"></i>',
                            'caption' => __('menu.partner.tools'),
                            'items' => [
                                'partner.links.index' => __('menu.partner.links'),
                                // 'partner.banners' => __('Баннеры'),
                                // 'partner.pwa' => __('PWA'),
                                //'partner.feeds' => __('XML фиды'),
                                'partner.postbacks' => __('menu.partner.postbacks'),

                            ],
                        ],
                        [
                            'icon' => '<i class="fas fa-fw fa-funnel-dollar"></i>',
                            'caption' => __('menu.partner.finance'),
                            'items' => [
                                'partner.payments' => __('menu.partner.payments'),
                            ],
                        ],
                        [
                            'icon' => '<i class="fas fa-fw fa-ban"></i>',
                            'caption' => __('menu.partner.ban'),
                            'items' => [
                                'partner.banned-links.index' => __('menu.partner.banned_links.index'),
                            ],
                        ],
                        [
                            'icon' => '<i class="fas fa-fw fa-info"></i>',
                            'caption' => __('menu.partner.info'),
                            'items' => [
                                'partner.news.index' => __('menu.partner.news.index'),
                                'partner.servicedesk.index' => __('menu.partner.servicedesk.index'),
                                'partner.faq.index' => __('menu.partner.faq.index'),
                            ],
                        ],
                    ],
                ],
                'analyst' => [
                    'allowed' => (auth()->user()->role === 'analyst'),
                    'items' => [
                        [
                            'icon' => '<i class="far fa-fw fa-chart-bar"></i>',
                            'caption' => __('menu.advertiser.statistics'),
                            'items' => [
                                'analyst.report' => __('menu.advertiser.report'),
                                'analyst.orders.index' => __('menu.advertiser.orders.index'),
                            ],
                        ],
                        [
                            'icon' => '<i class="far fa-fw fa-gem"></i>',
                            'caption' => __('menu.advertiser.promo'),
                            'items' => [
                                'analyst.offers.index' => __('menu.advertiser.offers.index'),
                            ],
                        ],
                    ],
                ],
            ];

            $menu = Menu::new()
                ->addClass('list-unstyled mb-0')
                ->setActiveFromRequest();

            foreach ($rolemenus as $rolemenu) {
                if (!$rolemenu['allowed']) {
                    continue;
                }

                foreach ($rolemenu['items'] as $rm) {
                    $caption = '<span class="nav-caption">' . join(' ', [($rm['icon'] ?? ''), $rm['caption']]) . '</span>';
                    $menu->submenu($caption, function (Menu $menu) use ($rm) {
                        $menu->addClass('list-unstyled mb-0')->addParentClass('nav-item-root');

                        $menu->fill($rm['items'], function (Menu $menu, $caption, $route) {
                            $link = Link::to(route($route), $caption)->addClass('nav-link');
                            $menu->add($link);
                        });
                    });
                }
            }
            return $menu;
        });
        return $next($request);
    }
}
