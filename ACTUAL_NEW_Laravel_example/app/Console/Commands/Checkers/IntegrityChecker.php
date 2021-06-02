<?php

namespace App\Console\Commands\Checkers;

use App\Models\Pp;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class IntegrityChecker extends CheckerTemplate
{
    protected $signature = 'checker:integrity';

    public function doCheck()
    {
        $this->errors = [];
        $users_table = [
            'changes_log' => 'user_id',
            'clicks' => 'partner_id',
            'enter_log' => 'user_id',
            'links' => 'partner_id',
            'news_users' => 'user_id',
            'notify' => 'partner_id',
            'notify_params' => 'partner_id',
            'offers' => 'user_id',
            'orders' => 'partner_id',
            'orders_products' => 'partner_id',
            'partner_payments' => 'partner_id',
            'pp' => 'user_id',
            'rate_rules' => 'partner_id',
            'servicedesk_task_comments' => 'partner_id',
            'servicedesk_tasks' => 'creator_user_id',
            'servicedesk_tasks' => 'doer_user_id',
            'stat_daily' => 'partner_id',
            'uploads' => 'user_id',
            'users_pay_methods' => 'user_id',
        ];

        $pps_table = [
            'business_units' => 'pp_id',
            'clicks' => 'pp_id',
            'clients' => 'pp_id',
            'faq_categories' => 'pp_id',
            'links' => 'pp_id',
            'news' => 'pp_id',
            'offers' => 'pp_id',
            'orders' => 'pp_id',
            'orders_products' => 'pp_id',
            'partner_payments' => 'pp_id',
            'pixel_log' => 'pp_id',
            'pp_pay_methods' => 'pp_id',
            'rate_rules' => 'pp_id',
            'reestrs' => 'pp_id',
            'servicedesk_tasks' => 'pp_id',
            'users' => 'pp_id',
            'xmlfeed_categories' => 'pp_id',
            'xmlfeed_offers' => 'pp_id',
        ];

        $this->checkIntegrityFor($users_table, 'users', User::pluck('id'));
        $this->checkIntegrityFor($pps_table, 'pp', Pp::pluck('id'));
    }

    public function checkIntegrityFor(array $tables, string $source_table_name, Collection $values)
    {
        collect($tables)->each(function (string $column_name, string $table_name) use ($source_table_name, $values) {
            $this->checkIntegrity($table_name, $column_name, $source_table_name, $values);
        });
    }

    private function checkIntegrity(string $table_name, string $column_name, string $soucre_table_name, Collection $values)
    {
        $className = 'App\\Models\\' . Str::studly(Str::singular($table_name));

        if (class_exists($className)) {
            $models = $className::query()
                ->withoutGlobalScopes()
                ->whereNotIn($column_name, $values)
                ->get();

            if ($count = $models->count()) {
                if ($this->confirm(sprintf('В таблице %s есть %s, где %s не соотносится со значениями из таблицы %s', $table_name, Lang::choice(':value запись|:value записи|:value записей', $count, ['value' => $count]), $column_name, $soucre_table_name))) {
                    foreach ($models as $key) {
                        $this->line('Удаляю запись', $key);
                        $key->forceDelete();
                    }
                }
            }
        } else {
            $this->warn('model for table '. $table_name .' not found!');
            // $models = DB::table($table_name)->where($column_name, '=', $values)->get();
        }
    }
}
