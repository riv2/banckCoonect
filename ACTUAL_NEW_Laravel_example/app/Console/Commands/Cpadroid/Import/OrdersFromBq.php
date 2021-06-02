<?php

namespace App\Console\Commands\Cpadroid\Import;

use App\Models\Link;
use App\Models\Order;
use App\User;
use Exception;
use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\Core\ExponentialBackoff;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\NotIn;
use Illuminate\Validation\Rules\In;

class OrdersFromBq extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cpadroid:import:orders_from_bq
    {--o|order=* : Обновить данные только по выбранным ID заявок}
    {--f|force : Очистить кеш данных, полученных из BQ}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импорт заказов из BQ в пп';

    /**
     * ID проекта, откуда забираем данные
     *
     * @var string
     */
    public $projectId = 'pochta-bank-279214';

    /**
     * Клиент BQ
     *
     * @var \Google\Cloud\BigQuery\BigQueryClient
     */
    public BigQueryClient $bigQuery;

    /**
     * ID партнерки
     *
     * @var integer
     */
    public $pp_id = 16;

    /**
     * Список разрешенных статусов для валидации
     *
     * @var array
     */
    public $allowed_statuses = [
        'Cancelled',
        'Done',
        'Preapproved',
        'Filling in CC',
        'Approved',
        'On Completion',
        'New',
        'Expired',
    ];

    /**
     * Cписок статусов, при которых заявка является отклоненной
     *
     * @var array
     */
    public $reject_statuses = [
        'Cancelled',
        'Expired',
    ];

    /**
     * Коллекция для определения юзера по hash_name
     *
     * @var \Illuminate\Support\Collection
     */
    public $user_name_to_user_id;

    /**
     * Список ссылок у каждого юзера
     *
     * @var \Illuminate\Database\Eloquent\Collection<mixed, (\Illuminate\Database\Eloquent\Builder|\App\Models\Link)>
     */
    public $link_id_to_user_id;

    /**
     * Список заявок для исключения из импорта
     */
    public array $ignored_orders = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->log('Запущена синхронизация с BQ', 'debug');

        $this->bigQuery = new BigQueryClient([
            'projectId' => $this->projectId,
            'keyFilePath' => storage_path('bigquery-key.json'),
        ]);

        $this->date_after = now()->subDays(30)->toDateString();

        $this->user_name_to_user_id = User::query()
            ->where('pp_id', '=', $this->pp_id)
            ->where('role', '=', 'partner')
            ->pluck('id', 'name');

        $this->link_id_to_user_id = Link::query()
            ->where('pp_id', '=', $this->pp_id)
            ->whereHas('partner')
            ->get()
            ->keyBy('id');

        $rows = $this->getItemsFromBQ();

        if ($orders = $this->option('order')) {
            $this->log('Обновляю данные только по заявкам ' . join(', ', $orders));
            $rows = $rows->filter(function ($value) use ($orders) {
                return (in_array($value['id_online_app'], $orders));
            });
        }

        $this->result_counts = [
            'all' => 0,
            'validate_success' => 0,
            'validate_fails' => 0,
            'created' => 0,
            'updated_status' => 0,
            'skipped_no_updated' => 0,
            'skipped_by_reestr_id' => 0,
            'skipped_finnaly_status' => 0,
        ];

        foreach ($rows as $row) {
            $this->log('Обработка заказа ' . ($row['id_online_app'] ?? '[NULL]'), 'info');
            $this->result_counts['all']++;

            $validator = $this->validateBQRow($row);

            if ($validator->fails()) {
                $this->warn('Пропуск: ' . $validator->errors()->first());
                $this->result_counts['validate_fails']++;
                continue;
            }

            $this->result_counts['validate_success']++;
            $row = $validator->validated();

            $this->saveOrder($row);
        }

        $this->log('', 'line');
        $this->log('Всего заявок: ' . $this->result_counts['all'], 'info');
        $this->log('Не прошло валидацию: ' . $this->result_counts['validate_fails'], 'info');
        $this->log('Валидных заявок: ' . $this->result_counts['validate_success'], 'info');
        $this->log('', 'line');

        $sumarr = [
            'created' => 'Создано',
            'updated_status' => 'Обновлен статус',
            'skipped_no_updated' => 'Уже обновлено',
            'skipped_by_reestr_id' => 'В реестре',
            'skipped_finnaly_status' => 'Финальный статус',
        ];

        $sum = 0;
        $msg = [];
        foreach ($sumarr as $sumkey => $summsg) {
            $sum += $this->result_counts[$sumkey];
            $msg[] = $this->result_counts[$sumkey];
            $this->log($summsg . ': ' . $this->result_counts[$sumkey], 'info');
        }
        $msg = implode(' + ', $msg);

        $result = -1;
        if ($sum == $this->result_counts['validate_success']) {
            $this->log($msg . ' = ' . $this->result_counts['validate_success'], 'info');
            $this->log('Синхронизация с BQ завершена успешно!', 'info');
            $this->log('', 'info');
            $result = 0;
        } else {
            $this->warn($msg . ' != ' . $this->result_counts['validate_success']);
            $this->log('Синхронизация с BQ завершена с ошибкой!', 'info');
            $this->log('', 'info');
            $result = 1;
        }

        return $result;
    }

    /**
     *
     */
    public function getItemsFromBQ(): Collection
    {
        $key = $this->projectId . '.CPA_Report.CPA_Report_table::' . $this->date_after;
        $ttl = now()->addHours(1);

        $query = 'SELECT * FROM `' . $this->projectId . '.CPA_Report.CPA_Report_table` WHERE `medium` = "cpa" AND `campaign` LIKE "%@Platform@%" AND `context_date` IS NOT NULL AND `context_date` >= "' . $this->date_after . '";';

        if ($this->option('force')) {
            $this->log('Данные с ключом ' . $key . ' успешно очищены!');
            Cache::forget($key);
        }

        $result = Cache::remember($key, $ttl, function () use ($query): Collection {
            $jobConfig = $this->bigQuery->query($query);
            $job = $this->bigQuery->startQuery($jobConfig);

            $backoff = new ExponentialBackoff(10);
            $backoff->execute(function () use ($job) {
                $this->log('Waiting for job to complete');
                $job->reload();
                if (!$job->isComplete()) {
                    throw new Exception('Job has not yet completed', 500);
                }
            });
            $queryResults = $job->queryResults();

            $result = collect();
            foreach ($queryResults as $row) {
                $result->push((array) $row);
            }

            $this->log('Получено заявок из BQ: ' . $result->count(), 'info');
            return $result;
        });


        return $result;
    }

    /**
     * Undocumented function
     *
     * @param array $order
     * @return ValidatorContract $validator
     */
    public function validateBQRow(array $row): ValidatorContract
    {
        // Парсим utm_campaign
        $row['campaign_parsed'] = explode('@', ($row['campaign'] ?? null), 8);

        $validation_rules = [
            'id_online_app' => [
                'required',
                new NotIn($this->ignored_orders),
            ],
            // 'product' => 'nullable|string',
            'source' => [
                'required',
                new In($this->user_name_to_user_id->keys()->toArray()),
            ],
            'medium' => 'required|string|in:cpa',
            'campaign' => 'required',
            'click_id' => 'nullable|string',
            'webmaster_id' => 'nullable|string',
            // 'web_date' => 'required|date',
            // 'context_id' => 'nullable|string',
            'context_date' => 'required|date',
            'context_issue_date' => 'nullable',
            // 'status' => 'nullable|integer',
            // 'flag_approval' => 'nullable|boolean',
            'flag_issue' => 'required|boolean',
            // 'flag_CRM' => 'nullable|boolean',
            'status_exp' => [
                'required',
                new In($this->allowed_statuses),
            ],
            // 'flag_ref_bank' => 'required|boolean',
            'campaign_parsed' => 'required|array|size:8',
            'campaign_parsed.0' => 'required|string|in:Pochta',
            'campaign_parsed.1' => 'required|string|same:product',
            'campaign_parsed.2' => 'required|string', // что тут хранится?
            'campaign_parsed.3' => 'required|string|same:source',
            'campaign_parsed.4' => 'required|string|in:Platform',
            'campaign_parsed.5' => [
                'required',
                'numeric',
                new In($this->link_id_to_user_id->keys()->toArray()),
            ],
            // пока у нас есть заказы, у которых не совпадает - валидация на совпадение полей убрана
            'campaign_parsed.6' => 'required|string', // |same:webmaster_id
            'campaign_parsed.7' => 'required|string', // |same:click_id
        ];

        $messages = [
            'same' => 'Значения полей :attribute и :other должны совпадать.',
            'id_online_app.not_in' => 'Заявка "'  . ($row['id_online_app'] ?? '[null]') . '" в списке игнорируемых',
            'source.in' => 'Партнер с source "' . ($row['source'] ?? '[null]') . '" отсутствует в данной ПП',
            'campaign_parsed.size' => 'Количество частей в :attribute должно не равно :size',
        ];

        $validator = Validator::make($row, $validation_rules, $messages);

        return $validator;
    }

    public function saveOrder(array $row): Order
    {
        $order = Order::firstOrNew([
            'pp_id' => $this->pp_id,
            'order_id' => $row['id_online_app'],
        ]);

        /** @var bool является ли заказ только что созданным или нет */
        $recently_created = ($order->exists === false);

        /** @var bool находится ли заказ в реестре */
        $in_reestr = !is_null($order->reestr_id);

        if ($in_reestr) {
            $this->result_counts['skipped_by_reestr_id']++;
            $this->log('Заказ уже в реестре, пропуск');
            return $order;
        }

        if ($recently_created) {
            $this->log('Заказ отсутствует, создаю со статусом new');

            $order->offer_id = $this->getOfferIdByLinkId($row['campaign_parsed'][5]);
            $order->datetime = $row['context_date'];
            $order->partner_id = $this->getPartnerIdByUserHash($row['source']);
            $order->link_id = $row['campaign_parsed'][5];
            $order->click_id = $row['campaign_parsed'][7]; // $row['click_id']
            $order->web_id = $row['campaign_parsed'][6]; // $row['webmaster_id']
            // Тут создается постбек, поэтому создаем со статусом new и обязательно сохраняем
            $order->status = 'new';
            $order->save();
        } else {
            $this->log('Заказ уже существует, обновляю', 'debug');
        }

        // Тут можно заполнить поля, которые можно безболезненно обновить
        $order->datetime_sale = $row['context_issue_date'] ?? null;

        $status = 'new';
        if ($this->isReject($row)) {
            $status = 'reject';
        } elseif ($this->isSale($row)) {
            $status = 'sale';
        }
        $this->log('Получен статус: ' . $status, 'debug');

        if (in_array($order->status, ['sale', 'reject']) && $order->status !== $status) {
            $this->log('Не могу изменить статус на ' . $status . ', так как уже установлен финальный статус ' . $order->status . ',  пропуск');
            $this->result_counts['skipped_finnaly_status']++;
        } else {
            if ($order->status !== $status) {
                if ($recently_created === false) {
                    $this->log(sprintf('Заменяю статус: %s->%s', $order->status, $status));
                    $this->result_counts['updated_status']++;
                } else {
                    $this->result_counts['created']++;
                }
                $order->status = $status;
            } else {
                $this->result_counts['skipped_no_updated']++;
                $this->log('Изменения в orders отсутствуют', 'debug');
            }
        }

        $order->save();
        return $order;
    }

    /**
     * Является ли текущая строка отказом
     *
     * @param array $row
     * @return boolean bool
     */
    public function isReject($row): bool
    {
        if (!array_key_exists('status_exp', $row)) {
            throw new Exception('Отсутствует status_exp');
        }

        return in_array($row['status_exp'], $this->reject_statuses);
    }

    /**
     * Является ли текущая строка покупкой
     *
     * @param array $row
     * @return boolean bool
     */
    public function isSale($row): bool
    {
        if (!array_key_exists('flag_issue', $row)) {
            throw new Exception('Отсутствует flag_issue');
        }

        return $row['flag_issue'] === '1';
    }

    public function getOfferIdByLinkId(int $link_id): int
    {
        return $this->link_id_to_user_id[$link_id]['offer_id'];
    }

    public function getPartnerIdByUserHash(string $user_hash): int
    {
        return $this->user_name_to_user_id->get($user_hash);
    }

    /**
     * Undocumented function
     *
     * @param  string  $string
     * @param  string|null  $style
     * @param  int|string|null  $verbosity
     * @return void
     */
    public function log(string $string, $style = null, $verbosity = null)
    {
        if (in_array($style, [null, 'line', 'info', 'comment', 'question', 'error'])) {
            if ($style === 'line') {
                $style = null;
            }
            $this->line($string, $style, $verbosity);
        }
        $logger = Log::channel('ordersfrombq');
        if ($style === 'debug') {
            $logger->debug($string);
        } elseif ($style === 'info') {
            $logger->info($string);
        }
    }
}
