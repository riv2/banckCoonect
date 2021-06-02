<?php

namespace app\components\base;

use app\models\reference\ConsoleTask;

interface ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask);
}