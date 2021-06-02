<?php

/**
 * Project laravel
 * Created by danila 15.05.20 @ 7:05
 */

namespace App\Console\Commands\Checkers;

use App\Models\PixelLog;
use Carbon\Carbon;

class CheckerPixelLog extends CheckerTemplate
{
    protected $signature = 'checker:pixelLog';

    protected array $ppIds = [
        79,
    ];

    public function doCheck()
    {
        $this->errors = [];

        foreach ($this->ppIds as $pp_id) {
            $pixelLogs = PixelLog::query()
                ->where('pp_id', '=', $pp_id)
                ->where('created_at', '>=', Carbon::now()->subHour()->toDateTimeString())
                ->get();
            if (count($pixelLogs) == 0) {
                $this->errors[] = 'Отсутствуют записи за последние 60 минут в таблице pixel_log по партнёрской программе с pp_id =' . $pp_id . '!';
            }
        }
    }
}
