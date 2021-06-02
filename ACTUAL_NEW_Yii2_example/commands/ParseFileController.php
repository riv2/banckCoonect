<?php
namespace app\commands;

use app\components\DateTime;
use app\models\enum\PriceRefinedType;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\pool\PriceRefined;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

use Box\Spout\Reader\XLSX\RowIterator;
use Box\Spout\Reader\XLSX\Sheet;
use yii;
use yii\console\Controller;

class ParseFileController extends Controller
{
    private $mapping = [];
    private $competitorId = null;
    private $filePath = null;

    const TYPE_OPT_1 = 1;
    const TYPE_OPT_2 = 1;
    const TYPE_OPT_3 = 1;

    const COL_OPT_1 = 1;
    const COL_OPT_2 = 1;
    const COL_OPT_3 = 1;


    public function actionT()
    {

        $this->mapping = [
            self::COL_OPT_1 => self::TYPE_OPT_1,
            self::COL_OPT_2 => self::TYPE_OPT_2,
            self::COL_OPT_3 => self::TYPE_OPT_3,
        ];

        $this->competitorId = Competitor::find()
            ->andWhere([
                'name' => '220 Вольт'
            ])
            ->select('id')
            ->limit(1)
            ->scalar();

        $this->filePath = __DIR__.'/'.'discount_price_7_other.xlsx';

        try {
            $reader = ReaderFactory::create(Type::XLSX);
        } catch (UnsupportedTypeException $e) {
            print_r($e->getMessage());
        }

        try {
            $reader->open($this->filePath);
        } catch (IOException $e) {
            print_r($e->getMessage());
        }

        $c = 0;

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                /** @var Sheet $sheet */

                $pricesChunks = [];

                foreach ($sheet->getRowIterator() as $row) {

                    $filledCols = 0;

                    foreach ($row as $i => $col) {
                        if ($col) {
                            $filledCols++;
                        }
                    }

                    if ($filledCols > 6 && is_numeric($row[2])) {
                        foreach ($this->mapping as $col => $priceRefinedType) {
                            $itemId = CompetitorItem::find()
                                ->andWhere([
                                    'competitor_id' => $this->competitorId,
                                    'sku' => $row[2],
                                    'status_id' => Status::STATUS_ACTIVE,
                                ])
                                ->select('item_id')
                                ->limit(1)
                                ->scalar();

                            if ($itemId) {
                                $priceRefinedVi = new PriceRefined();
                                $priceRefinedVi->loadDefaultValues();
                                $priceRefinedVi->extracted_at           = new DateTime();
                                $priceRefinedVi->price_refined_type_id  = $priceRefinedType;
                                $priceRefinedVi->price                  = $row[$col];
                                $priceRefinedVi->region                 = [1];
                                $priceRefinedVi->item_id                = $itemId;
                                $priceRefinedVi->competitor_id          = $this->competitorId;
                                $priceRefinedVi->source_id              = Source::SOURCE_WEBSITE;
                                $priceRefinedVi->out_of_stock           = false;
                                $priceRefinedVi->save();
                            }

                        }
                        $c++;
                    }
                }
            }
        } catch (ReaderNotOpenedException $e) {
            print_r($e->getMessage());
        }

        echo " === $c ===";


    }

    /**
     * Проверяем, что процесс запущен
     * @param string $processName Имя процесса
     * @return bool
     * @return boolean
     */
    public function processIsRun($processName)
    {
        $result = [];
        exec("ps aux | grep -v grep | grep $processName", $result);
        $count = count($result);
        return ceil($count);
    }

}