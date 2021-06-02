<?php
namespace app\commands;

use app\models\enum\PriceParsedStatus;
use app\models\pool\PriceParsed;
use app\models\register\Error;
use Yii;
use yii\console\Controller;

class PriceRefinedController extends Controller
{
    /**
     * Проверяем, что процесс запущен
     * @param string $processName Имя процесса
     * @return bool
     * @return boolean
     */
    public function processIsRun($processName)
    {
        $result = [];
        exec("ps aux | grep -v grep | grep \"$processName\"", $result);
        $count = count($result);
        return ceil($count);
    }

    public function actionRefineManager()
    {
        $threads = intval(getenv('PARSING_CONSUMER_THREADS'),10);

        for ($t = 0; $t < $threads; $t++) {
            $thread = str_pad($t, 2, '0',STR_PAD_LEFT);
            if ($this->processIsRun('price-refined/refine '. $thread) < 2) {
                shell_exec('php ' . Yii::getAlias('@app') . '/yii price-refined/refine ' . $thread . ' > /dev/null 2>/dev/null &');
            }
        }
    }

    /**
     * Перевод спарсенных цен в обработанные
     * @param int $thread
     */
    public function actionRefine($thread = '00') {

        if ($this->processIsRun('price-refined/refine '.$thread) >= 2) {
            return;
        }

        $thread = intval($thread,10);

        $parsedPrices = true;

        while ($parsedPrices) {
            /** @var PriceParsed $parsedPrice */
            $parsedPrices = PriceParsed::find()
                ->andWhere([
                    'thread' => $thread,
                    'price_parsed_status_id' => PriceParsedStatus::STATUS_NEW
                ])
                ->orderBy([
                    'extracted_at' => SORT_DESC
                ])
                ->limit(500)
                ->all();


            if ($parsedPrices) {
                foreach ($parsedPrices as $parsedPrice) {
                    try {
                        $parsedPrice->createRefinedPrice();
                        $parsedPrice->save(false);
                    } catch (\Exception $e) {
                        $parsedPrice->price_parsed_status_id = PriceParsedStatus::STATUS_ERROR;
                        $parsedPrice->error_message = Error::extractMessage($e->getMessage());
                        $parsedPrice->save(false);
                    }
                }

                if (count($parsedPrices) < 100) {
                    sleep(5);
                }
            }

        }
    }
}