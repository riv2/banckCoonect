<?php

namespace App\Jobs;

use App\Exceptions\PixelLogException;
use App\Models\Click;
use App\Models\Client;
use App\Models\Link;
use App\Models\Notify;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\PixelLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class PixelLogProcessJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected PixelLog $pixel_log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PixelLog $pixel_log)
    {
        $this->pixel_log = $pixel_log;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->log('Начинаю обработку пикселя');
        try {
            // Эти значения будут перезаписываться в ходе работы скрипта
            $this->pixel_log->is_valid = null;
            $this->pixel_log->is_click = null;
            $this->pixel_log->is_order = null;
            $this->pixel_log->status = null;

            $this->pixel_log->parsed_client_id = $this->pixel_log->parseClientIdFromOpenPixel();
            $this->pixel_log->parsed_partner_id = $this->pixel_log->parseAndValidatePartnerId();
            $this->pixel_log->parsed_link_id = $this->pixel_log->parseAndValidateLinkId();
            $this->pixel_log->parsed_click_id = $this->pixel_log->parseClickIdFromOpenPixel();
            $this->pixel_log->parsed_web_id = $this->pixel_log->parseWebIdFromOpenPixel();
            $this->pixel_log->parsed_order_id = $this->pixel_log->parseOrderIdFromOpenPixel();
            $this->pixel_log->saved_offer_id = $this->pixel_log->parseAndValidateOfferId();
            $this->pixel_log->is_click = $this->pixel_log->isClickedLink();
            $this->pixel_log->is_order = (bool) $this->pixel_log->parsed_order_id;
            $this->pixel_log->is_valid = true;
            $this->pixel_log->save();

            $this->createClient();
            $this->createClick();
            $this->createOrder();
        } catch (PixelLogException $th) {
            $error = sprintf(
                'pixel_id #%d: %s in file %s:%d',
                $this->pixel_log->id,
                $th->getMessage(),
                $th->getFile(),
                $th->getLine(),
            );
            $this->log($error, 'error');
            $this->status = $error;
        } finally {
            $this->pixel_log->save();
        }
        $this->log('Пиксель обработан');
        $this->log('');
    }

    /**
     * Создает клиента, если еще не создан
     */
    public function createClient(): ?Client
    {
        // Проверяем, заполнено ли поле client_id
        if (is_null($this->pixel_log->parsed_client_id)) {
            $this->log('Поле parsed_client_id пустое, создавать клиента не буду!');
            return null;
        }

        $client_exists = Client::query()
            ->where('pp_id', '=', $this->pixel_log->pp_id)
            ->where('id', '=', $this->pixel_log->parsed_client_id)
            ->exists();
        if ($client_exists) {
            $this->log('Клиент ' . $this->pixel_log->parsed_client_id . ' уже существует');
            return null;
        }

        $client = new Client();
        $client->pp_id = $this->pixel_log->pp_id;
        $client->id = $this->pixel_log->parsed_client_id;
        $client->save();

        $this->log('Клиент ' . $this->pixel_log->parsed_client_id . ' успешно создан');

        return $client;
    }

    /**
     * Создает запись в таблице clicks
     */
    public function createClick(): ?Click
    {
        if (is_null($this->pixel_log->is_click)) {
            $this->log('Это не переход по партнерской ссылке');
            return null;
        }

        $click_exists = Click::where('pixel_log_id', '=', $this->pixel_log->id)->exists();
        if ($click_exists) {
            $this->log('Этот переход по партнерской ссылке уже учтен');
            return null;
        }

        $click = new Click();
        $click->pp_id = $this->pixel_log->pp_id;
        $click->partner_id = $this->pixel_log->parsed_partner_id;
        $click->link_id = $this->pixel_log->parsed_link_id;
        $click->client_id = $this->pixel_log->parsed_client_id;
        $click->click_id = $this->pixel_log->parsed_click_id;
        $click->web_id = $this->pixel_log->parsed_web_id;
        $click->pixel_log_id = $this->pixel_log->id;
        $click->save();

        $this->log('Переход#' . $click->id . ' по партнерской ссылке успешно создан');

        return $click;
    }

    public function createOrder(): ?Order
    {
        if (is_null($this->pixel_log->is_order) || is_null($this->pixel_log->parsed_order_id)) {
            $this->log('Так как отсутсвует parsed_order_id - этот пиксель не является заказом');
            return null;
        }

        // Проверяем, может этот заказ уже обрабатывался пикселем
        /** @todo индекс в базу */
        $order_exists = Order::where('pixel_id', '=', $this->pixel_log->id)->exists();
        if ($order_exists) {
            $this->log('Этот заказ уже учтен, пропускаем обновление');
            return null;
        }

        // Прогреваем кэши
        $link_id_to_offer_id = Cache::remember('link_id_to_offer_id', now()->addMinutes(1), fn () => Link::pluck('offer_id', 'id'));

        $offer_id = $link_id_to_offer_id[$this->pixel_log->parsed_link_id];

        // Проверяем, может быть заказ был создан вручную (или получен по API)
        $order_nopixel_exists = Order::query()
            ->where('offer_id', '=', $offer_id)
            ->where('order_id', '=', $this->pixel_log->parsed_order_id)
            // У нас индекс по offer_id и order_id, поэтому по pp_id не проверяем
            // ->where('pp_id', '=', $this->pixel_log->pp_id)
            ->exists();

        if ($order_nopixel_exists) {
            // Заказ уже есть в базе, ничего не обновляем!
            // Можно обновить ему pixel_id например, но это нужно дополнительно обсудить
            $this->log('Заказ с данным order_id уже создан кем-то еще, выходим');
            return null;
        }

        // Пока не создаем постбеки и ничего не считаем
        $order = Order::withoutEvents(
            function () use ($offer_id): ?Order {
                $order = new Order();
                $order->order_id = $this->pixel_log->parsed_order_id;
                $order->offer_id = $offer_id;
                $order->datetime = $this->pixel_log->created_at;
                $order->partner_id = $this->pixel_log->parsed_partner_id;
                $order->pp_id = $this->pixel_log->pp_id;
                $order->link_id = $this->pixel_log->parsed_link_id;
                $order->click_id = $this->pixel_log->parsed_click_id;
                $order->web_id = $this->pixel_log->parsed_web_id;
                $order->client_id = $this->pixel_log->parsed_client_id;
                $order->pixel_id = $this->pixel_log->id;
                $order->type = 'order';
                $order->status = 'new';
                $order->gross_amount = 0;
                $order->save();

                $this->log('Заказ создан');
                $pixel_products = $this->pixel_log->parseProductsFromOpenPixel();
                if (is_array($pixel_products)) {
                    $this->log('У данного заказа есть товары, создаем их');
                    foreach ($pixel_products as $pixel_product) {
                        $product = $this->createOrdersProduct($pixel_product, $order);
                        $order->gross_amount += $product->total;
                    }
                }
                $order->save();

                return $order;
            }
        );

        $this->createOrderNotify($order);

        return $order;
    }

    public function createOrdersProduct($pixel_product, &$order): ?OrdersProduct
    {
        $product = OrdersProduct::query()
            ->where('order_id', '=', $order->order_id)
            ->where('offer_id', '=', $order->offer_id)
            ->where('product_id', '=', $pixel_product['id'])
            ->where('price', '=', $pixel_product['price'])
            // У нас индекс по order_id, offer_id, product_id и price, поэтому по pp_id не проверяем
            // ->where('pp_id', '=', $order->pp_id)
            ->exists();

        if ($product) {
            // Товар уже есть в базе, ничего не обновляем!
            $this->log('Товар уже есть в базе, ничего не обновляем');
            return null;
        }

        // Пока не создаем постбеки и ничего не считаем
        /** @todo после обновления лары переделать на метод saveQuietly без замыкания */
        $product = OrdersProduct::withoutEvents(function () use ($order, $pixel_product) {
            $product = new OrdersProduct();
            $product->order_id = $order->order_id;
            $product->parent_id = $order->id;
            $product->datetime = $order->datetime;
            $product->pp_id = $order->pp_id;
            $product->partner_id = $order->partner_id;
            $product->offer_id = $order->offer_id;
            $product->link_id = $order->link_id;
            $product->product_id = $pixel_product['id'];
            $product->product_name = $pixel_product['name'];
            $product->category = $pixel_product['category'];
            $product->price = $pixel_product['price'];
            $product->quantity = $pixel_product['quantity'] ?? 1;
            $product->total = $pixel_product['price'] * ($pixel_product['quantity'] ?? 1);
            $product->status = 'new';
            $product->web_id = $order->web_id;
            $product->click_id = $order->click_id;
            $product->pixel_id = $order->pixel_id;
            $product->save();

            return $product;
        });

        $this->log('Товар создан');
        return $product;
    }

    public function createOrderNotify(Order &$order): ?Notify
    {
        $np = $order->notifyParams;
        if (!$np) {
            $this->log('Постбеки данному партнеру не требуются');
            return null;
        }

        $status = 'status_' . $order->status . '_value';
        if (empty($np->$status)) {
            $this->log('Постбеки данному партнеру на данный статус не требуются');
            return null;
        }

        $notify_exists = Notify::query()
            ->where('order_id', '=', $order->order_id)
            ->where('status', '=', $order->status)
            ->exists();
        if ($notify_exists) {
            $this->log('Постбек на данный заказ уже существует');
            return null;
        }

        $notify = new Notify();
        $notify->order_id = $order->order_id;
        $notify->partner_id = $order->partner_id;
        $notify->status = $order->status;
        $notify->fee_id = $order->fee_id;
        $notify->sent_cnt = 0;
        $notify->datetime = now();
        $notify->click_id = $order->click_id;
        $notify->web_id = $order->web_id;
        $notify->link_id = $order->link_id;
        $notify->gross_amount = $order->gross_amount ?? 0.00;
        $notify->model = $order->model;
        $notify->amount = $order->amount ?? 0;
        $notify->save();
        $this->log('Постбек создан');

        return $notify;
    }

    private function log(string $message, $level = 'debug')
    {
        $message = 'pixel_id#' . $this->pixel_log->id . ': ' . $message;
        logger()->channel('pochtabank_pixel')->{$level}($message);
    }
}
