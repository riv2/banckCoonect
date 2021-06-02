<?php

namespace App\Console\Commands\Checkers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

abstract class CheckerTemplate extends Command
{
    protected array $errors = [];

    abstract public function doCheck();

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('Старт проверки ' . $this->signature);
        $this->doCheck();
        if (!empty($this->errors)) {
            foreach ($this->errors as $error) {
                $this->doAlarm($error);
            }
        }
        Log::info('Конец проверки ' . $this->signature);
    }

    protected function doAlarm($message)
    {
        Log::stack(['stack', 'stderr', 'telegram'])->error($this->signature . ' :: ' . $message);
    }
}
