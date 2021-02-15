<?php

namespace App\Console\Commands\Fix;

use App\StudentFinanceNomenclature;
use Illuminate\Console\Command;

class DoubleRemoteAccessBuyExport extends Command
{
    protected $signature = 'export:double_remote_access_buy';

    protected $description = 'Command description';

    public function handle()
    {
        $seconds = 10;

        StudentFinanceNomenclature::where('finance_nomenclature_id', 6)
        ->chunk(100, function ($items) use ($seconds) {
            foreach ($items as $item) {
                /** @var StudentFinanceNomenclature $item */

                $double = StudentFinanceNomenclature::where('id', '!=', $item->id)
                    ->where('user_id', $item->user_id)
                    ->where('finance_nomenclature_id', $item->finance_nomenclature_id)
                    ->where('cost', $item->cost)
                    ->where('semester', $item->semester)
                    ->whereBetween('created_at', [$item->created_at->format('Y-m-d H:i:s'), $item->created_at->subSeconds($seconds)->format('Y-m-d H:i:s')])
                    ->first();

                if (!empty($double)) {
                    $this->info('id=' . $double->id . "\n");
                }
            }
        });
    }
}
