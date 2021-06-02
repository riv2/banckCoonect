<?php
namespace app\components\processing;

use app\components\DateTime;
use app\components\exchange\Exchange;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\pool\PriceRefined;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\reference\Item;
use app\models\reference\PriceFormerType;
use app\models\register\FileProcessing;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Common\Type;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Reader\XLSX\Sheet;
use yii\base\BaseObject;

/**
 * Class ExcelOpt220
 * @package app\components\processing
 *
 *
 */
class ExcelOpt220 extends BaseObject
{

    /** @var FileProcessing */
    public $fileProcessing;

    /** @var string  */
    public $competitorId;

    /** @var float  "Мин цена" - по 1-ой колонке оптового прайса */
    public $minPrice;

    /** @var int  */
    public $opt1col;
    /** @var int  */
    public $opt2col;
    /** @var int  */
    public $opt3col;
    /** @var string  */
    public $opt1guid;
    /** @var string  */
    public $opt2guid;
    /** @var string  */
    public $opt3guid;

    /** @var int  */
    public $dataLifespan;

    public static function settingsBuilder() {
        return [

            'competitorId' => [
                'label'     => 'Конкурент',
                'value'     => self::defaultCompetitor(),
                'input'     => 'select2',
                'class'     => Competitor::className(),
            ],

            'minPrice' => [
                'label'     => 'Минимальная цена',
                'value'     => 1000,
                'input'     => 'number',
            ],
            'dataLifespan' => [
                'label'     => 'Актуальность цен',
                'value'     => 24 * 3600,
                'input'     => 'seconds',
            ],

            'opt1col' => [
                'label'     => 'Колонка цены опт1',
                'value'     => 6,
                'input'     => 'number',
            ],
            'opt2col' => [
                'label'     => 'Колонка цены опт2',
                'value'     => 7,
                'input'     => 'number',
            ],
            'opt3col' => [
                'label'     => 'Колонка цены опт3',
                'value'     => 8,
                'input'     => 'number',
            ],

            'opt1guid' => [
                'label'     => 'PriceFormer Опт1',
                'value'     => null,
                'input'     => 'select2',
                'class'     => PriceFormerType::className(),
            ],
            'opt2guid' => [
                'label'     => 'PriceFormer Опт2',
                'value'     => null,
                'input'     => 'select2',
                'class'     => PriceFormerType::className(),
            ],
            'opt3guid' => [
                'label'     => 'PriceFormer Опт3',
                'value'     => null,
                'input'     => 'select2',
                'class'     => PriceFormerType::className(),
            ],

        ];
    }

    public static function defaultCompetitor() {
        return  Competitor::find()
            ->andWhere([
                'name' => '220 Вольт'
            ])
            ->select('id')
            ->limit(1)
            ->scalar();
    }


    /**
     * @throws IOException
     * @throws ReaderNotOpenedException
     * @throws UnsupportedTypeException
     */
    public function process() {

        $mapping = [];
        $this->opt1col--;
        $this->opt2col--;
        $this->opt3col--;
        $this->minPrice = floatval($this->minPrice);
        if ($this->opt1guid) {
            $mapping[(int)$this->opt1col] = $this->opt1guid;
        }
        if ($this->opt2guid) {
            $mapping[(int)$this->opt2col] = $this->opt2guid;
        }
        if ($this->opt3guid) {
            $mapping[(int)$this->opt3col] = $this->opt3guid;
        }

        if (empty($mapping)) {
            throw new \Exception('Нет ниодного типа цены');
        }

        try {
            $reader = ReaderFactory::create(Type::XLSX);
        } catch (UnsupportedTypeException $e) {
            print_r($e->getMessage());
            throw $e;
        }

        try {
            $reader->open($this->fileProcessing->file_path);
        } catch (IOException $e) {
            print_r($e->getMessage());
            throw $e;
        }

        $pricesChunks = [];

        foreach ($mapping as $col => $priceFormerType) {
            $pricesChunks[$priceFormerType] = [];
        }

        $liveDate = new DateTime('+3 days');

        $total      = 0;
        $progress   = 0;
        $c          = 0;
        $products   = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            /** @var Sheet $sheet */
            foreach ($sheet->getRowIterator() as $row) {
                $any = false;
                foreach ($row as $col => $val) {
                    if ($val) {
                        $any = true;
                    }
                }
                if (!$any) {
                    break;
                }

                $total++;

                if ($total % 500 == 0) {
                    $this->fileProcessing->task->total = $total;
                    $this->fileProcessing->task->save();
                }
            }
            $sheet->getRowIterator()->rewind();
        }
        $reader->getSheetIterator()->rewind();

        $this->fileProcessing->task->total = $total;
        $this->fileProcessing->task->save();

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                /** @var Sheet $sheet */

                foreach ($sheet->getRowIterator() as $row) {

                    $any = false;
                    foreach ($row as $col => $val) {
                        if ($val) {
                            $any = true;
                        }
                    }
                    if (!$any) {
                        break;
                    }

                    $progress++;


                    if ($progress % 500 == 0) {
                        $this->fileProcessing->task->progress = $progress;
                        $this->fileProcessing->task->save();
                    }


                    if (!is_numeric($row[2])) {
                        continue;
                    }

                    foreach ($mapping as $col => $priceFormerType) {
                        if (!$row[$col]) {
                            continue;
                        }
                    }

                    //фильтрация по "Мин цена" - по 1-ой колонке оптового прайса
                    if (floatval($row[(int)$this->opt1col]) < $this->minPrice) {
                        continue;
                    }

                    PriceRefined::find()
                        ->alias('pr')
                        ->andWhere([
                            'pr.out_of_stock'  => false,
                            'pr.item_id' => CompetitorItem::find()
                                ->alias('ci')
                                ->andWhere([
                                    'ci.competitor_id' => $this->competitorId,
                                    'ci.sku'           => $row[2],
                                    'ci.status_id'     => Status::STATUS_ACTIVE,
                                ])
                                ->select('ci.item_id')
                        ])
                        ->select('pr.item_id')
                        ->limit(1)
                        ->scalar();
                    $itemId = false;
//                        ->innerJoin(['i' => Item::tableName()],
//                            'ci.item_id = i.id'
//                        )
//                        ->andWhere([
//                            'i.status_id'     => Status::STATUS_ACTIVE,
//                        ])

//                        ->innerJoin(['pr' => PriceRefined::tableName()],
//                            'ci.item_id = pr.item_id AND ci.competitor_id = pr.competitor_id'
//                        )
//                        ->andWhere([
//                            'pr.out_of_stock'  => false,
//                        ])
//                        ->orderBy([
//                            'ci.created_at' => SORT_DESC
//                        ])


                    if (!$itemId) {
                        continue;
                    }

                    $products++;
                    $c++;

                    foreach ($mapping as $col => $priceFormerType) {
                        $pricesChunks[$priceFormerType][$itemId] = floatval($row[$col]);
                        if ($c > 10000) {
                            foreach ($pricesChunks as  $priceFormerTypeId => $chunk) {
                                Exchange::runExport([
                                    'Prices' => [
                                        'prices'                => $chunk,
                                        'live_date'             => $liveDate,
                                        'price_former_type_id'  => $priceFormerTypeId,
                                    ],
                                ]);
                                $pricesChunks[$priceFormerTypeId] = [];
                            }
                            $c = 0;
                        }
                    }


                    if ($products % 500 == 0) {
                        $this->fileProcessing->progress = $products;
                        $this->fileProcessing->save();
                    }


                }
            }

            foreach ($pricesChunks as  $priceFormerTypeId => $chunk) {
                if (count($chunk) > 0) {
                    Exchange::runExport([
                        'Prices' => [
                            'prices' => $chunk,
                            'live_date' => $liveDate,
                            'price_former_type_id' => $priceFormerTypeId,
                        ],
                    ]);
                    $pricesChunks[$priceFormerTypeId] = [];
                }
            }

            $this->fileProcessing->progress = $products;
            $this->fileProcessing->save();
            $this->fileProcessing->task->progress = $progress;
            $this->fileProcessing->task->save();
        } catch (ReaderNotOpenedException $e) {
            $this->fileProcessing->progress = $products;
            $this->fileProcessing->save();
            $this->fileProcessing->task->progress = $progress;
            $this->fileProcessing->task->save();
            print_r($e->getMessage());
            throw $e;
        }

        return [
            'total' => $c,
            'progress' => $c,
            'errors' => 0
        ];
    }

    public function detect() {

    }

}