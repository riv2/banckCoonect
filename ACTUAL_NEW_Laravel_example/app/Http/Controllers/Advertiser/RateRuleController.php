<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOfferRateRule;
use App\Http\Requests\UpdateOfferRateRule;
use App\Models\Link;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\Pp;
use App\Models\RateRule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Response;

class RateRuleController extends Controller
{
    /** @return Response */
    public function create(): Response
    {
        $links = Link::query()
            ->where('pp_id', '=', auth()->user()->pp->id)
            ->get()
            ->pluck('link_name', 'id')
            ->toArray();
        $users = User::query()
            ->where('pp_id', '=', auth()->user()->pp->id)
            ->where('role', '=', 'partner')
            ->get()
            ->pluck('email', 'id')
            ->toArray();
        $rate_rule = new RateRule([
            'pp_id' => auth()->user()->pp_id,
        ]);
        $availableProgressiveParams = $rate_rule->getAvailableProgressiveParam();


        return response()->view('advertiser.rateRule.create', [
            'links' => $links,
            'users' => $users,
            'availableProgressiveParams' => $availableProgressiveParams,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreOfferRateRule $request
     * @param RateRule $rateRule
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(StoreOfferRateRule $request, RateRule $rateRule)
    {
        $offer = Offer::query()
            ->where('id', '=', $request->get('offer_id'))
            ->where('user_id', '=', Auth::id())
            ->firstOrFail();

        $request->validated();

        $answer = $this->validateDatesStore($request, $rateRule);

        if ($answer['result']) {
            $rateRule->offer_id = $request->get('offer_id');
            $rateRule->fee = $request->get('fee');
            $rateRule->partner_id = $request->get('partner_id');
            $rateRule->link_id = $request->get('link_id');
            $rateRule->date_start = $request->get('date_start');
            $rateRule->date_end = $request->get('date_end');
            $rateRule->progressive_param = $request->get('progressive_param');
            $rateRule->progressive_value = $request->get('progressive_value');
            $rateRule->save();
            return redirect(route('advertiser.offers.show', ['offer' => $offer]))->with('success', 'Ставка успешно добавлена');
        }
        return redirect(route('advertiser.rateRule.create', ['offer_id' => $request->get('offer_id')]))->withErrors($answer['error']);
    }

    /**
     * @param Request $request
     * @param RateRule $rate_rule
     * @return RateRule|\Illuminate\Database\Eloquent\Builder
     */
    protected function checkProgressiveParam(Request $request, RateRule $rate_rule = null)
    {
        $query = RateRule::query();
        $progressive_param = $request->get('progressive_param');
        $partner_id = $request->get('partner_id');
        $link_id = $request->get('link_id');
        if (!empty($rate_rule)) {
            $query->where('id', '<>', $rate_rule->id);
        }
        if (empty($partner_id)) {
            $query->whereNull('partner_id');
        } else {
            $query->where('partner_id', '=', $partner_id);
        }
        if (empty($link_id)) {
            $query->whereNull('link_id');
        } else {
            $query->where('link_id', '=', $link_id);
        }
        if (empty($progressive_param)) {
            $query->whereNull('progressive_param');
        } else {
            $query
                ->where('progressive_param', '=', $request->get('progressive_param'))
                ->where('progressive_value', '=', $request->get('progressive_value'));
        }
        return $query;
    }

    public function validateDatesStore(StoreOfferRateRule $request, RateRule $rate_rule = null): array
    {
        $oldEndNull = $this->checkProgressiveParam($request, $rate_rule)
            ->where('offer_id', '=', $request->get('offer_id'))
            ->where('date_end', '=', null)
            ->first();

        $newStart = Carbon::parse($request->get('date_start'));

        // Первый блок проверок: если есть ставка date_end = null.
        if ($oldEndNull) {
            // Дата начала новой ставки меньше текущей даты.
            if ($newStart->lessThan(now())) {
                /** @todo Перевод */
                return [
                    'result' => false,
                    'error' => 'Дату начала можно установить только на дату из будущего. Существует активная ставка.'
                ];
            }

            if (is_null($request->get('date_end'))) {
                $oldRateRules = $this->checkProgressiveParam($request, $rate_rule)
                    ->where('offer_id', '=', $request->get('offer_id'))
                    ->where('business_unit_id', '=', $request->get('business_unit_id'))
                    ->where('date_end', '!=', null)->get();
                foreach ($oldRateRules as $oldRateRule) {
                    $oldEnd = Carbon::parse($oldRateRule->date_end);
                    // Дата старта новой ставки должна быть позже даты окончания старой.
                    if ($newStart->lessThan($oldEnd)) {
                        /** @todo Перевод */
                        return [
                            'result' => false,
                            'error' => 'На выбранный диапазон дат уже назначена ставка. Выберите другие даты.'
                        ];
                    }
                }
                // Делаем окончание действия старой ставки = дата старта новой минус один день.
                $oldEndNull->date_end = $newStart->subDay();
                $oldEndNull->save();

                return ['result' => true];
            }

            $newEnd = Carbon::parse($request->get('date_end'));
            $oldRateRules = $this->checkProgressiveParam($request, $rate_rule)
                ->where('offer_id', '=', $request->get('offer_id'))
                ->where('business_unit_id', '=', $request->get('business_unit_id'))
                ->where('date_end', '!=', null)->get();

            foreach ($oldRateRules as $oldRateRule) {
                $oldStart = Carbon::parse($oldRateRule->date_start);
                $oldEnd = Carbon::parse($oldRateRule->date_end);
                // Первое условие до || проверяет лежит ли дата начала действия новой ставки в диапазоне действия старой
                // Второе условие проверяет лежит ли дата окончания действия новой ставки в диапазоне действия старой
                if ($newStart->between($oldStart, $oldEnd, false) ||
                    $newEnd->between($oldStart, $oldEnd, false) ||
                    $newStart->lessThan($oldStart) && $newEnd->greaterThan($oldEnd)
                ) {
                    /** @todo Перевод */
                    return [
                        'result' => false,
                        'error' => 'На выбранный диапазон дат уже назначена ставка. Выберите другие даты.'
                    ];
                }
            }
            // Делаем окончание действия старой ставки = дата старта новой - 1
            $oldEndNull->date_end = $newStart->subDay();
            $oldEndNull->save();

            return ['result' => true];
        }


        // Второй блок проверок: исключаем пересечение дат в случае, когда нет ставки с датой окончания = null.

        // Если дата окончания новой ставки null, то проверяем:
        if (is_null($request->get('date_end'))) {
            $oldRateRules = $this->checkProgressiveParam($request, $rate_rule)
                ->where('offer_id', '=', $request->get('offer_id'))
                ->where('business_unit_id', '=', $request->get('business_unit_id'))->get();
            foreach ($oldRateRules as $oldRateRule) {
                $oldEnd = Carbon::parse($oldRateRule->date_end);
                // Дата старта новой ставки должна быть позже даты окончания старой.
                if ($newStart->lessThan($oldEnd)) {
                    /** @todo Перевод */
                    return [
                        'result' => false,
                        'error' => 'На выбранный диапазон дат уже назначена ставка. Выберите другие даты.'
                    ];
                }
            }

            return ['result' => true];
        }

        $newEnd = Carbon::parse($request->get('date_end'));
        // Проверяем дата окончания новой ставки позже даты начала новой ставки.
        if ($newEnd->lessThan($newStart)) {
            /** @todo Перевод */
            return [
                'result' => false,
                'error' => 'Дата окончания должна быть после даты начала!'
            ];
        }

        $oldRateRules = $this->checkProgressiveParam($request, $rate_rule)
            ->where('offer_id', '=', $request->get('offer_id'))
            ->where('business_unit_id', '=', $request->get('business_unit_id'))->get();

        foreach ($oldRateRules as $oldRateRule) {
            $oldStart = Carbon::parse($oldRateRule->date_start);
            $oldEnd = Carbon::parse($oldRateRule->date_end);
            // Первое условие до || проверяет лежит ли дата начала действия новой ставки в диапазоне действия старой
            // Второе условие проверяет лежит ли дата окончания действия новой ставки в диапазоне действия старой
            if ($newStart->between($oldStart, $oldEnd) || $newEnd->between($oldStart, $oldEnd) || $newStart->lessThan($oldStart) && $newEnd->greaterThan($oldEnd)) {
                /** @todo Перевод */
                return [
                    'result' => false,
                    'error' => 'На выбранный диапазон дат уже назначена ставка. Выберите другие даты.'
                ];
            }
        }
        return ['result' => 'true'];
    }

    /**
     * @param RateRule $rateRule
     * @return Response
     */
    public function edit(RateRule $rateRule): Response
    {
        $links = Link::query()
            ->where('pp_id', '=', auth()->user()->pp->id)
            ->get()
            ->pluck('link_name', 'id')
            ->toArray();
        $users = User::query()
            ->where('pp_id', '=', auth()->user()->pp->id)
            ->where('role', '=', 'partner')
            ->get()
            ->pluck('email', 'id')
            ->toArray();

        return response()->view('advertiser.rateRule.edit', [
            'rateRule' => $rateRule,
            'links' => $links,
            'users' => $users,
        ]);
    }

    /**
     * @param UpdateOfferRateRule $request
     * @param RateRule $rateRule
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(UpdateOfferRateRule $request, RateRule $rateRule)
    {
        $offer = RateRule::query()
            ->where('id', '=', $rateRule->id)
            ->where('pp_id', '=', auth()->user()->pp->id)
            ->firstOrFail();

        $request->validated();

        // Функция проверок:
        $answer = $this->validateDatesUpdate($request, $rateRule);

        $route = route('advertiser.offers.show', ['offer' => $rateRule->offer_id]);
        if ($answer['result']) {
            $rateRule->fee = $request->get('fee');
            $rateRule->date_start = $request->get('date_start');
            $rateRule->date_end = $request->get('date_end');
            $rateRule->save();
            return redirect($route)->with('success', 'Ставка успешно изменена!');
        } else {
            return redirect($route)->withErrors($answer['error']);
        }
    }


    public function validateDatesUpdate(UpdateOfferRateRule $request, RateRule $rateRule): array
    {
        // Первый блок проверок: ищем заказы с этой ставкой.

        // Сначала проверим на наличие изменений:
        // Если поля fee и date_start не изменились, то можем поменять дату окончания ставки на любую из будущего.
        $newStart = Carbon::parse($request->get('date_start'));
        if ($rateRule->fee == $request->get('fee') && $rateRule->date_start == $request->get('date_start')) {
            // Если дата окончания новой ставки null,
            // то ничего не изменилось или ставка сделалась бессрочной
            // - выходим из функции(проверка пройдена).
            if (is_null($request->get('date_end'))) {
                return ['result' => true];
            }

            // Дальше проверяются ставки только с датами окончания
            $newEnd = Carbon::parse($request->get('date_end'));
            // Проверяем дата окончания новой ставки позже даты начала новой ставки.
            if ($newEnd->lessThan($newStart)) {
                /** @todo Перевод */
                return [
                    'result' => false,
                    'error' => 'Дата окончания должна быть после даты начала!'
                ];
            }
            // Проверяем, позже ли настоящего дата окончания
            if ($newEnd->greaterThan(Carbon::now())) {
                return ['result' => true];
            } else {
                /** @todo Перевод */
                return [
                    'result' => false,
                    'error' => 'Дату окончания можно поменять только на дату из будущего!'
                ];
            }
        }

        $pp = Pp::query()->where('id', '=', $rateRule->pp_id)->first();

        switch ($pp->pp_target) {
            case 'products':
                $hasOrderProduct = OrdersProduct::query()
                    ->where('fee_id', '=', $rateRule->id)
                    ->first();
                    $hasOrder = false;
                break;
            case 'lead':
                $hasOrder = Order::query()->where('fee_id', '=', $rateRule->id)->first();
                $hasOrderProduct = false;
                break;
            default:
                $hasOrderProduct = false;
                $hasOrder = false;
                break;
        }

        if ($hasOrderProduct || $hasOrder) {
            /** @todo Перевод */
            return [
                'result' => false,
                'error' => 'Найдены заказы с этой ставкой. Вы не можете редактировать ставку, если заказы уже созданы. Если вам необходимо изменить ставку, задайте вопрос через <a href="/advertiser/servicedeskadv/create?type=technical&subject=%D0%9F%D0%BE%D0%BC%D0%BE%D1%89%D1%8C%20%D1%81%20%D1%80%D0%B5%D0%B4%D0%B0%D0%BA%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5%D0%BC%20%D1%81%D1%82%D0%B0%D0%B2%D0%BA%D0%B8"> тикет</a>.'
            ];
        }

        // Если только поменялась ставка, и нет заказов с этой ставкой
        $oldStart = Carbon::parse($rateRule->date_start)->toDate()->format('Y-m-d');
        $oldEnd = ($rateRule->date_end) ? Carbon::parse($rateRule->date_end)->toDate()->format('Y-m-d') : null;

        if (
            // даты те же
            ($oldStart == $request->get('date_start'))
            && ((is_null($request->get('date_end')) && empty($oldEnd)) || ($oldEnd == $request->get('date_end')))
            // И прогресс. параметр тот же
            && ((is_null($request->get('progressive_param')) && $rateRule->progressive_param) || $request->get('progressive_param') == $rateRule->progressive_param)
            // И значение для сравнения то же
            && ((is_null($request->get('progressive_value')) && $rateRule->progressive_value) || $request->get('progressive_value') == $rateRule->progressive_value)
        ) {
            return [
                'result' => true
            ];
        }

        // Второй блок проверок: исключаем пересечение дат.

        // Если дата окончания новой ставки null, то проверяем:
        if (is_null($request->get('date_end'))) {
            $oldRateRules = RateRule::query()->where('offer_id', '=', $rateRule->offer_id)
                ->where('business_unit_id', '=', $rateRule->business_unit_id)->get();
            foreach ($oldRateRules as $oldRateRule) {
                $oldEnd = Carbon::parse($oldRateRule->date_end);
                // Дата старта новой ставки должна быть позже даты окончания старой.
                if ($newStart->lessThan($oldEnd)) {
                    /** @todo Перевод */
                    return [
                        'result' => false,
                        'error' => 'На выбранный диапазон дат уже назначена ставка. Выберите другие даты.'
                    ];
                }
            }

            return ['result' => true];
        }
        $newEnd = Carbon::parse($request->get('date_end'));

        if ($newEnd->lessThan($newStart)) {
            /** @todo Перевод */
            return [
                'result' => false,
                'error' => 'Дата окончания должна быть после даты начала!'
            ];
        }

        $oldRateRules = RateRule::query()->where('offer_id', '=', $rateRule->offer_id)
            ->where('business_unit_id', '=', $rateRule->business_unit_id)
            ->where('id', '!=', $rateRule->id)
            ->get();

        foreach ($oldRateRules as $oldRateRule) {
            $oldStart = Carbon::parse($oldRateRule->date_start);
            $oldEnd = Carbon::parse($oldRateRule->date_end);
            // Первое условие до || проверяет лежит ли дата начала действия новой ставки в диапазоне действия старой
            // Второе условие проверяет лежит ли дата окончания действия новой ставки в диапазоне действия старой
            if ($newStart->between($oldStart, $oldEnd) || $newEnd->between($oldStart, $oldEnd) || $newStart->lessThan($oldStart) && $newEnd->greaterThan($oldEnd)) {
                /** @todo Перевод */
                return [
                    'result' => false,
                    'error' => 'На выбранный диапазон дат уже назначена ставка. Выберите другие даты.'
                ];
            }
        }

        return ['result' => true];
    }
}
