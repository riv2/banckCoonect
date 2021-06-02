<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\reference\ConsoleTask;
use yii\base\BaseObject;

class RefinePricesTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $threads = intval(getenv('PARSING_CONSUMER_THREADS'),10);

        for ($t = 0; $t < $threads; $t++) {
            $thread = str_pad($t, 2, '0',STR_PAD_LEFT);
            if (self::processIsRun('price-refined/refine '. $thread) < 2) {
                shell_exec('php ' . \Yii::getAlias('@app') . '/yii price-refined/refine ' . $thread . ' > /dev/null 2>/dev/null &');
            }
        }
        return true;
    }

    public static function processIsRun($processName)
    {
        $result = [];
        exec("ps aux | grep -v grep | grep \"$processName\"", $result);
        $count = count($result);
        return ceil($count);
    }
}