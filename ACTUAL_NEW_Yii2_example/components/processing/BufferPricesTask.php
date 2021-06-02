<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\pool\ParsingBuffer;
use app\models\pool\PriceParsed;
use app\models\reference\ConsoleTask;
use app\models\register\Error;
use yii\base\BaseObject;

class BufferPricesTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $i = 0;
        foreach (ParsingBuffer::find()
                     ->andWhere([
                         'is_error' => false
                     ])
                     ->batch(5000) as $buffers) {
            foreach ($buffers as $buffer) {
                try {
                    /** @var ParsingBuffer $buffer */
                    $parsedPrice = new PriceParsed;
                    if ($i >= 10) {
                        $i = 0;
                    }
                    $data = $buffer->data;
                    $data['thread'] = $i;
                    $data['parsing_id'] = 'aaa00002-36e5-4b35-bd74-cb971a8d9335';
                    $data['parsing_project_id'] = 'aaa00001-36e5-4b35-bd74-cb971a8d9335';
                    $parsedPrice->importOneFromFile($data);
                    $i++;
                    $buffer->delete();
                } catch (\Exception $e) {
                    $buffer->is_error = true;
                    $buffer->error_message = Error::extractMessage($e->getMessage());
                    $buffer->save(false);
                }
            }
        }
    }
}