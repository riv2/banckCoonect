<?php
/**
 * Project qpartners
 * Created by danila 22.06.2020 @ 18:09
 */

namespace App\Helpers;

use App\Models\Click;
use App\Models\Order;
use App\Models\OrdersGraph;
use DateInterval;
use DatePeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

trait ReportTrait
{
    public function index(Request $request)
    {
        $contents = view(auth()->user()->role . '.report', [
            'graph' => $this->getGraph(),
            'stat' => $this->getStat(),
            'orders' => $this->getOrders(),
            'partners' => $this->getBestPartners(),
            'pp_target' => $this->getTarget(),
        ]);
        return response($contents)->withHeaders([
            'Link' => '</vendors~js/chunks/report.js>; rel=preload; as=script',
            'Link' => '</js/chunks/report.js>; rel=preload; as=script',
        ]);
    }

    protected function getGraph()
    {
        /** @var array Массив с датами для формирования графика */
        $dates_values = [];
        $period = new DatePeriod(now()->subDays(7), new DateInterval('P1D'), now()->endOfDay());
        foreach ($period as $date) {
            $dates_values[] = $date->format('Y-m-d');
        }

        /** @var array Массив со нулевыми значениями по умолчанию - ключ это дата */
        $dates_keys = array_fill_keys($dates_values, 0);

        /** @var array Шаблон с данными по умолчанию */
        $pp_target = auth()->user()->pp->pp_target ?? null;
        if ($pp_target == 'products') {
            $return = [
                'categories' => $dates_values,
                'series' => [
                    [
                        'name' => __('lists.all-orders.I.plural'),
                        'data' => $dates_keys,
                    ],
                    [
                        'name' => __('lists.orderStateList.products.I.plural.new'),
                        'data' => $dates_keys,
                    ],
                    [
                        'name' => __('lists.orderStateList.products.I.plural.sale'),
                        'data' => $dates_keys,
                    ],
                    [
                        'name' => __('lists.orderStateList.products.I.plural.reject'),
                        'data' => $dates_keys,
                    ],
                ],
            ];
        } else {
            $return = [
                'categories' => $dates_values,
                'series' => [
                    [
                        'name' => __('lists.all-leads.I.plural'),
                        'data' => $dates_keys,
                    ],
                    [
                        'name' => __('lists.orderStateList.lead.I.plural.new'),
                        'data' => $dates_keys,
                    ],
                    [
                        'name' => __('lists.orderStateList.lead.I.plural.sale'),
                        'data' => $dates_keys,
                    ],
                    [
                        'name' => __('lists.orderStateList.lead.I.plural.reject'),
                        'data' => $dates_keys,
                    ],
                ],
            ];
        }

        $date_start = now()->startOfDay()->subDays(7);
        $date_end = now()->endOfDay();
        foreach (['orders', 'new', 'sale', 'reject'] as $ki => $kname) {
            $count = OrdersGraph::{$kname}($date_start, $date_end)
                ->get()
                ->pluck('aggregate', 'date_datetime')
                ->union($dates_keys)
                ->toArray();
            ksort($count);
            $return['series'][$ki]['data'] = array_values($count);
        }

        $return['series'] = array_values($return['series']);

        return $return;
    }

    protected function getOrders()
    {
        return Order::query()
            ->when(auth()->user()->role == 'partner', function (Builder $query) {
                $query->where('partner_id', '=', auth()->user()->id);
            })
            ->where('type', 'order')
            ->with('offer')
            ->with('link')
            ->with('partner')
            ->orderBy('datetime', 'DESC')
            ->paginate(20);
    }

    /**
     * Получает LTV
     * Сумма выручки поделенная на количество уникальных клиентов
     *
     * @param bool $first_order_month показать данные за месяц первой заявки
     * @return float
     */
    protected function getLtv(bool $first_order_month = false): float
    {
        $orders = Order::query()
            ->selectRaw('COALESCE((SUM(gross_amount) / COUNT(DISTINCT client_id)), 0) as ltv')
            ->when($first_order_month, function (Builder $query) {
                $min_sale_datetime = Order::query()
                    ->selectRaw(DB::raw('MIN(`datetime`) as min_sale_datetime'))
                    ->from('orders')
                    ->where('status', '=', 'sale')
                    ->first()
                    ->min_sale_datetime;

                $min_sale_datetime = Date::parse($min_sale_datetime);
                $between = [
                    $min_sale_datetime->startOfMonth()->toDateTimeString(),
                    $min_sale_datetime->endOfMonth()->toDateTimeString(),
                ];
                $query->whereBetween('datetime', $between);
            })
            ->where('status', '=', 'sale')
            ->withCasts(['ltv' => 'decimal:2'])
            ->first();

        return (float) $orders->ltv;
    }

    protected function getStat()
    {
        $dates_between = [now()->startOfDay()->subDays(30)->toDateString(), now()->endOfDay()->addDay()->toDateString()];

        $orders = Order::query()
            ->selectRaw('COUNT(*) orders_sum')
            ->selectRaw('COUNT(DISTINCT client_id) orders_unique')
            ->selectRaw('SUM(CASE WHEN `status` = "sale" THEN 1 END) orders_sale')
            ->selectRaw('SUM(CASE WHEN `status` = "sale" THEN `gross_amount` END) gross_amount_sum')
            ->selectRaw('SUM(amount) amount_sum')
            ->selectRaw('SUM(amount_advert) amount_advert_sum')
            ->whereBetween('datetime', $dates_between)
            ->first();

        $clicks = Click::query()
            ->selectRaw('COUNT(*) clicks')
            ->selectRaw('COUNT(DISTINCT client_id) clicks_unique')
            ->whereBetween('created_at', $dates_between)
            ->first();

        $ltv_first = $this->getLtv(true);
        $ltv_total = $this->getLtv();

        $report = [
            'orders' => $orders,
            'clicks' => $clicks,
            'ltv_first' => $ltv_first,
            'ltv_total' => $ltv_total,
        ];

        return $report;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function getBestPartners()
    {
        $dates_between = [now()->startOfDay()->subDays(30)->toDateString(), now()->endOfDay()->addDay()->toDateString()];

        return DB::table('orders')
            ->select('orders.partner_id', 'users.name')
            ->selectRaw('COUNT(*) cnt')
            ->selectRaw('COALESCE(SUM(CASE WHEN orders.status = "sale" THEN 1 END), 0) AS orders_sale')
            ->selectRaw('SUM(orders.gross_amount) amount_sum')
            ->leftJoin('users', 'users.id', '=', 'orders.partner_id')
            ->whereBetween('datetime', $dates_between)
            ->where('orders.pp_id', '=', PartnerProgramStorage::getPP()->id)
            ->groupBy('partner_id')
            ->orderBy('cnt', 'desc')
            ->limit(10)
            ->get();
    }

    protected function getTarget()
    {
        return auth()->user()->pp->pp_target ?? null;
    }
}
