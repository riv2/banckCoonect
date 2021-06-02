<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\exchange\Exchange;
use app\models\reference\ConsoleTask;
use yii\base\BaseObject;

class PhubCategoriesImportTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        Exchange::runImport([
            'Categories'        => [
                'queue'         => true,
                'importQueue'   => 1000
            ],
        ]);
        return true;
    }
}