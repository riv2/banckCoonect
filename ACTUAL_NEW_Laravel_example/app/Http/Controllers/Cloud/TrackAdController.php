<?php

namespace App\Http\Controllers\Cloud;

use App\Http\Controllers\Controller;
use App\Http\Middleware\TrackAdMiddleware;
use App\Models\Order;
use App\Models\Pp;
use DOMDocument;
use DOMElement;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Отдача XML для TrackAd
 */
class TrackAdController extends Controller
{
    /** @param Pp $pp - партнерская программа, для которой выгружаем заказы */
    public Pp $pp;

    /** @param int $orders_period - количество дней, за которые выгружаем заказы */
    public int $orders_period = 30;

    /** @param array Справочник по замене наших статусов на статусы TrackAd */
    public array $statuses_list = [
        'new'    => 'new',            // (только что созданный заказ)
        'new'    => 'wait',           // (заказ проверен, доставляется)
        'reject' => 'cancel',         // ( заказ отменен покупателем до получения)
        'reject' => 'refuse',         // (покупатель отказался от заказа, без оплаты)
        'reject' => 'return',         // (заказ был оплачен, а затем возвращен)
        'reject' => 'partial_return', // (заказ оплачен, но часть заказа возвращена)
        'sale'   => 'done',           // (заказ получен и оплачен)
    ];

    public function __construct()
    {
        $this->middleware(TrackAdMiddleware::class);
    }

    /**
     * Обработка запроса
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Pp $pp): Response
    {
        $this->pp = $pp;
        return response()->make($this->generateXml())->header('Content-Type', 'text/xml');
    }

    /**
     * Получает список заказов для выгрузки
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getOrders(): Collection
    {
        return Order::query()
            ->where('pp_id', '=', $this->pp->id)
            ->where('datetime', '>=', today()->subDays($this->orders_period))
            ->orWhere('status_datetime', '>=', today()->subDays($this->orders_period))
            ->get();
    }

    /**
     * Преобразует заказ в массив для TrackAd
     *
     * @param Order $order
     * @return array
     */
    public function convertOrderToArray(Order $order): array
    {
        if (!in_array($order->status, array_keys($this->statuses_list))) {
            throw new Exception('Неожиданный статус заказа "' . $order->status . '" при генерации TrackAd XML');
        }
        $state = $this->statuses_list[$order->status];

        $result = [
            'id' => $order->order_id,
            'state' => $state,
            'stateupdatedate' => $order->status_datetime,
            'date' => $order->datetime->toDateTimeString(),
            'price' => (int) $order->gross_amount,
            'commission' => (float) $order->amount,
            'utm_source' => 'partners',
            'utm_medium' => 'cpa',
            'utm_campaign' => (int) $order->link_id,
        ];
        $result = array_filter($result, function ($var) {
            return ($var !== null && $var !== false && $var !== '');
        });
        return $result;
    }

    /**
     * Генерация XML
     *
     * @return string
     */
    public function generateXml(): string
    {
        $orders = $this->getOrders();

        $dom = new DOMDocument();
        $dom->formatOutput = true;
        $dom->xmlVersion = '1.0';
        $dom->encoding = 'utf-8';

        $orders_element = $dom->createElement('orders');
        foreach ($orders as $order) {
            $order_array = $this->convertOrderToArray($order);
            $this->createElementFromArray($dom, $orders_element, 'order', $order_array);
        }
        $dom->appendChild($orders_element);

        return $dom->saveXML();
    }

    /**
     * Создает новый XML элемент из массива
     *
     * @param DOMDocument $dom
     * @param string $name
     * @param array $elems
     * @return void
     */
    public function createElementFromArray(DOMDocument &$dom, DOMElement &$orders_element, string $name, array $elems): void
    {
        $order_node = $dom->createElement($name);
        foreach ($elems as $key => $value) {
            $order_node->appendChild($dom->createElement($key, $value));
        }
        $orders_element->appendChild($order_node);
        return;
    }
}
