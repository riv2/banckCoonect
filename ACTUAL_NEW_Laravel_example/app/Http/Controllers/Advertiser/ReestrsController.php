<?php

namespace App\Http\Controllers\Advertiser;

use App\Exports\ReestrExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\PartnerPayment;
use App\Models\Reestr;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ReestrsController extends Controller
{

    /**
     * остановить обновление статусов
     */
    public function stopUpdate()
    {
        $updates_stopped = true;
        return redirect(route('advertiser.reestrs.create', compact($updates_stopped)));
    }

    /**
     * Включить обновление статусов
     */
    public function startUpdate()
    {
        $updates_stopped = false;
        return redirect(route('advertiser.reestrs.create', compact($updates_stopped)));
    }

    /**
     * Список элементов
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $collection = Reestr::query()
            ->with(['payments', 'orders'])
            ->paginate();
        return view('advertiser.reestrs.list', [
            'collection' => $collection,
        ]);
    }

    /**
     * @param $reestr_id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse | RedirectResponse
     */
    public function export($reestr_id)
    {
        return (new ReestrExport($reestr_id))->download('reestr-' . $reestr_id . '.xlsx');
    }

    /**
     * Форма добавления новой записи
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('advertiser.reestrs.create', [
            'updates_stopped' => false
        ]);
    }

    /**
     * Создание нового элемента
     * Метод отправки: POST
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        //Создаем реестр и сохраняем
        $reestr = new Reestr();
        $reestr->total = 0;
        $reestr->payed = 0;
        $reestr->pp_id = Auth()->user()->pp->id;
        $reestr->datetime = Carbon::parse($request->get('date_end'))->endOfDay()->toDateTimeString();
        $reestr->save();

        $month = Carbon::now()->month - 1;

        //Заказы и продукты(если есть) - в реестр
        $orders = Order::query()
            ->where('amount', '!=', 0)
            ->where('pp_id', '=', $reestr->pp_id)
            ->where('datetime', '<=', $request->get('date_end'))
            ->whereNull('reestr_id')
            ->withCount('partner');

        if ($reestr->pp_id == 16) {
            $orders->whereMonth("datetime_sale", $month);
        }
        $orders = $orders->get();
        foreach ($orders as $order) {
            if (!is_null(User::query()
                ->find($order->partner_id)
                ->pay_methods
                ->first())) {
                $order->reestr_id = $reestr->reestr_id;
                $order->save();
            }
        }

        if (Auth()->user()->pp->pp_target == 'products') {
            foreach (Order::query()->where('reestr_id', '=', $reestr->reestr_id)->get() as $order) {
                $products = $order->products->where('fee', '>', 0);
                foreach ($products as $product) {
                    $product->reestr_id = $reestr->reestr_id;
                    $product->save();
                }
            }
        }

        foreach (Order::query()->where('reestr_id', '=', $reestr->reestr_id)
                     ->selectRaw('SUM(amount) as amount, partner_id')
                     ->groupBy('partner_id')->get() as $payment) {
            $reestr->total += $payment->amount;
            $reestr->save();
            $partnerPayment = new PartnerPayment();
            $partnerPayment->partner_id = $payment->partner_id;
            $partnerPayment->reestr_id = $reestr->reestr_id;
            $partnerPayment->pay_method = User::query()
                ->find($payment->partner_id)
                ->pay_methods
                ->first()
                ->pivot
                ->pay_method_id;
            $partnerPayment->pay_account = User::query()->find($payment->partner_id)
                ->pay_methods
                ->first()
                ->pivot
                ->id;
            $partnerPayment->pp_id = Auth()->user()->pp->id;
            $partnerPayment->revenue = $payment->amount;
            $partnerPayment->save();
        }

        DB::commit();

        return redirect()
            ->route('advertiser.reestrs.show', $reestr)
            ->withSuccess(__('Запись успешно создана!'));
    }

    /**
     * Страница просмотра элемента
     *
     * @param \App\Models\Reestr $reestr
     * @return \Illuminate\Http\Response
     */
    public function show(Reestr $reestr)
    {
        return view('advertiser.reestrs.show', [
            'reestr' => $reestr,
        ]);
    }

    /**
     * Страница редактирования элемента
     *
     * @param \App\Models\Reestr $reestr
     * @return \Illuminate\Http\Response
     */
    public function edit(Reestr $reestr)
    {
        return view('advertiser.reestrs.edit', [
            'reestr' => $reestr,
        ]);
    }

    /**
     * Сохранение элемента
     * Метод отправки: PUT
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Reestr $reestr
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reestr $reestr)
    {
        $request->validate([
            'element' => 'required',
        ]);

        $reestr->element = $request->get('element');
        $reestr->save();

        return redirect()
            ->route('advertiser.reestrs.show', $reestr)
            ->withSuccess(__('Запись успешно сохранена!'));
    }

    /**
     * Удаление элемента
     * Метод отправки: DELETE
     *
     * @param \App\Models\Reestr $reestr
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reestr $reestr)
    {
        // $reestr->delete();

        // return redirect()
        //    ->route('advertiser.reestrs.show', $reestr)
        //    ->withSuccess(__('Запись успешно удалена!'));
    }
}
