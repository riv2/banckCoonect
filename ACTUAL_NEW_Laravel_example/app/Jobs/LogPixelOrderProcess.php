<?php

namespace App\Jobs;

use App\Models\Link;
use App\Models\LogPixelOrder;
use App\Models\Order;
use App\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LogPixelOrderProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // public $tries = 1;
    // public $timeout = 10;

    protected $pixel_log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LogPixelOrder $pixel_log)
    {
        $this->pixel_log = $pixel_log->withoutRelations();

        $this->users_names = Cache::remember('LogPixelOrderProcess->users_names', now()->addMinutes(1), function (): \Illuminate\Database\Eloquent\Collection {
            return User::query()
                ->get(['id', 'name'])
                ->keyBy('id');
        });

        $this->links = Cache::remember('LogPixelOrderProcess->links', now()->addMinutes(1), function (): \Illuminate\Database\Eloquent\Collection {
            return Link::query()
                ->get(['id', 'pp_id', 'partner_id', 'offer_id'])
                ->keyBy('id');
        });
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if (empty($this->pixel_log->data)) {
                throw new Exception('Пустой массив data', 1);
            }
            if (empty($this->pixel_log->data['utm_medium'])) {
                throw new Exception('Отсутствует utm_medium', 1);
            }
            if ($this->pixel_log->data['utm_medium'] !== 'cpa') {
                throw new Exception('utm_medium != cpa', 1);
            }

            $validator = $this->orderValidator($this->pixel_log->data);
            if ($validator->fails()) {
                dump($this->pixel_log->id, $validator->errors()->first());
                throw new Exception($validator->errors()->toJson());
            }

            $parsed_campaign = explode('@', $this->pixel_log->data['utm_campaign'], 8);
            $validator = $this->campaignValidator($parsed_campaign);
            if ($validator->fails()) {
                dump($this->pixel_log->id, $validator->errors()->first());
                throw new Exception($validator->errors()->toJson());
            }

            $link = $this->links[$parsed_campaign[5]] ?? null;
            if (!$link) {
                throw new Exception('Отсутсвует link');
            }

            $order = Order::firstOrNew([
                'pixel_id' => $this->pixel_log->id,
            ], [
                'offer_id' => $link->offer_id,
            ]);

            $order->order_id = $this->pixel_log->data['cpa_order_id'];
            $order->offer_id = $link->offer_id;
            $order->datetime = $this->pixel_log->created_at;
            $order->partner_id = $link->partner_id;
            $order->pp_id = $link->pp_id;
            $order->category_id = null;
            $order->landing_id = null;
            $order->link_id = $link->id;
            $order->click_id = $this->pixel_log->data['cpa_click_id'] ?? null;
            $order->web_id = $this->pixel_log->data['cpa_partner_id'] ?? null;
            $order->client_id = $this->pixel_log->data['client_id'] ?? null;
            $order->pixel_id = $this->pixel_log->id;
            $order->save();

            $this->pixel_log->is_valid = true;
            $this->pixel_log->status = 'Заказ ' . $order->id . ' создан!';
            $this->pixel_log->save();

            // dump($order);
        } catch (\Throwable $th) {
            $this->pixel_log->is_valid = false;
            $this->pixel_log->status = $th->getMessage();
            $this->pixel_log->save();
            // throw $th;
        }

        // dump($this->pixel_log->data);
    }

    private function orderValidator(array $data)
    {
        return Validator::make($data, [
            'client_id' => 'string|nullable',
            'cpa_order_id' => 'required',
            'cpa_click_id' => 'string|nullable',
            'cpa_partner_id' => 'string|nullable',
            'utm_medium' => 'required|in:cpa',
            'utm_source' => ['required', Rule::in($this->users_names->pluck('name')->toArray())],
        ]);
    }

    private function campaignValidator(array $data)
    {
        return Validator::make($data, [
            '0' => 'required|in:Pochta',
            '1' => 'required|in:Cash',
            '2' => 'required|in:lpCash',
            '3' => ['required', Rule::in($this->users_names->pluck('name')->toArray())],
            '4' => 'required|in:Platform',
            '5' => ['required', Rule::in($this->links->keys())],
            '6' => 'string|nullable',
            '7' => 'string|nullable',
        ]);
    }
}
