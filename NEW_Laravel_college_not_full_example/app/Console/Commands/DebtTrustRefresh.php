<?php

namespace App\Console\Commands;

use App\DebtTrust;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebtTrustRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debt_trust:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $models = DebtTrust
            ::select([
                '*',
                DB::raw('TIMESTAMPDIFF(MONTH , updated_at, NOW()) as months')
            ])
            ->where(DB::raw('TIMESTAMPDIFF(MONTH , updated_at, NOW())'), '>=', 1)
            ->get();

        foreach ($models as $model)
        {
            $limit = $model->contract_current_debt - ($model->contract_month_cost * $model->months);
            $limit = $limit < 0 ? $limit = 0 : $limit;

            $model->contract_current_debt = $limit;
            $model->save();
        }
    }
}
