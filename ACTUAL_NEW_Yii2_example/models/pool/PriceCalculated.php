<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\DateTime;
use app\components\ValidationRules;
use app\models\enum\CompetitionMode;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\reference\Brand;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\reference\Item;
use app\models\reference\Project;
use app\models\document\ProjectExecution;
use app\models\reference\ProjectCompetitor;
use app\models\reference\ProjectCompetitorItem;
use app\models\reference\ProjectItem;
use app\models\register\Error;
use yii\base\InvalidParamException;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Расчетная цена
 *
 * Class PriceCalculated
 *
 * @package app\models\pool
 *
 * @property string name
 * @property float  price                   Расчетная цена
 * @property string project_id              ID Проекта
 * @property string item_id                 ID Товара
 * @property string project_execution_id    ID Документа исполнения проекта
 *
 * @property string itemPriceSupply
 * @property string itemPriceRecommendedRetail
 * @property string itemPriceDefault
 * @property string projectItemRRPR
 * @property string projectItemMinMargin
 *
 * @property Item               item                    Товар
 * @property Project            project                 Проект
 * @property ProjectExecution   projectExecution        Документ исполнения проекта
 * @property ProjectItem        projectItem
 */

class PriceCalculated extends Pool
{

    /** @var  Project кешированный проект */
    private $_projectExecution;
    /** @var  NomenclatureDocumentItem кешированный товар */
    private $_projectItem;

    public static function isBigData() {
        return false;
    }
    public static function noCount() {
        return false;
    }


    /**
     * Кешированный проект
     * @param $projectExecution
     * @return ProjectExecution
     */
    public function projectExecution($projectExecution = null) {
        if (!$this->_projectExecution) {
            if ($projectExecution && ($projectExecution instanceof ProjectExecution)) {
                $this->_projectExecution = $projectExecution;
            } else {
                $this->_projectExecution = $this->projectExecution;
            }
        }
        return $this->_projectExecution;
    }

    /**
     * Кешированный проект
     * @param $projectItem
     * @return NomenclatureDocumentItem
     */
    public function projectItem($projectItem = null) {
        if (!$this->_projectItem) {
            if ($projectItem && ($projectItem instanceof ProjectItem)) {
                $this->_projectItem = $projectItem;
            } else {
                $this->_projectItem = $this->projectItem;
            }
        }
        return $this->_projectItem;
    }

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Расчётная цена';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Расчётные цены';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired('item_id', 'project_id', 'project_execution_id'),
            [
                [['name'], 'string'],
                [['price'], 'number'],
            ],
            ValidationRules::ruleUuid('project_id'),
            ValidationRules::ruleUuid('item_id'),
            ValidationRules::ruleUuid('project_execution_id'),
            [],
            [],
            []
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'id'                    => 'Цена',
                'price'                 => 'Цена',
                'project_id'            => 'Проект',
                'item_id'               => 'Товар',
                'project_execution_id'  => 'Документ исполнения проекта',
                'item'                  => 'Товар',
                'project'               => 'Проект',
                'projectExecution'      => 'Документа исполнения проекта',
                'logPriceCalculation'   => 'История расчета',

                'itemBrand'                 => 'Бренд',
                'itemPriceSupply'           => 'Закупка',
                'itemPriceRecommendedRetail'=> 'РРЦ',
                'itemPriceDefault'          => 'ВИ МСК',
                'projectItemRRPR'           => 'Регламент РРЦ',
                'projectItemMinMargin'      => 'Мин. наценка',
            ]
        );
    }

    public function getName() {
        return number_format(floatval($this->price),2,',',' ');
    }
    public function getItemBrand(){
        return $this->item->brand->name;
    }
    public function getItemPriceSupply(){
        return $this->item->price_supply;
    }
    public function getItemPriceRecommendedRetail(){
        return $this->item->price_recommended_retail;
    }
    public function getItemPriceDefault(){
        return $this->item->price_default;
    }
    public function getProjectItemRRPR(){
        return ($this->projectItem)?$this->projectItem->rrp_regulations:null;
    }
    public function getProjectItemMinMargin(){
        return ($this->projectItem)?$this->projectItem->min_margin:null;
    }

    public function addExportQuery(ActiveQuery $query)
    {
        $query->joinWith(['i' => Item::tableName()])->leftJoin(['b' => Brand::tableName()], "i.brand_id = b.id");
        return $query;
    }


    private static $time = null;
    public static function ltime($strin) {
//        if (!self::$time) {
//            self::$time = microtime(true) ;
//        }
//        $diff = round(microtime(true) - self::$time, 6);
//
//        echo sprintf("[%f] %s \n", $diff, $strin );
//
//        self::$time = microtime(true) ;
    }

    /**
     * @param ProjectExecution $projectExecution
     * @param NomenclatureDocumentItem|ProjectItem $projectItem
     * @return PriceCalculated|null|static
     * @throws InvalidParamException
     */
    public static function createCalculatePrice($projectExecution, $projectItem, $debug = false) {

        self::ltime('start');
        $project = $projectExecution->project();

        self::ltime('started');
//        $calculatedPrice = PriceCalculated::findOne([
//            'project_execution_id'  => $projectExecution->id,
//            'item_id'               => $projectItem->item_id,
//        ]);
        self::ltime('Got PriceCalculated');

//        if (!$calculatedPrice) {
//
//        }
        $calculatedPrice                        = new PriceCalculated();
        $calculatedPrice->item_id               = $projectItem->item_id;
        $calculatedPrice->project_execution_id  = $projectExecution->id;
        $calculatedPrice->project_id            = $project->id;
        $calculatedPrice->projectItem($projectItem);

        // Не расчитывать цену
        $noPrice = false;
        if (!$projectItem->item->price_supply) {
            $calculatedPrice->price = 0;
            $noPrice = true;
        }

        self::ltime('got ProjectItem');

        $rr = $project->getProjectRegionIds();

        $find = PriceRefined::find()
            ->alias('t')
            ->andWhere([
                't.source_id'     => $project->getProjectSourceIds(),
                't.item_id'       => $projectItem->item_id,
            ])
            ->andWhere('ci.id IS NOT NULL')
            ->leftJoin(['c' => Competitor::tableName()], 'c.id = t.competitor_id')
            ->leftJoin(['ci' => CompetitorItem::tableName()], 'ci.item_id = t.item_id AND ci.competitor_id = t.competitor_id AND ci.status_id = ' . Status::STATUS_ACTIVE)
            // TODO: regions json filter
            ->orderBy([
                't.extracted_at'  => SORT_DESC
            ]);


        $relevanceDate = $projectExecution->getRelevanceDate();
        if ($relevanceDate) {
            $find->andWhere('
                CASE WHEN c.price_lifetime > 0
                    THEN t.extracted_at > ((NOW()) - make_interval(secs := c.price_lifetime))
                    ELSE t.extracted_at > \'' . date('Y-m-d H:i:s', $relevanceDate) . '\'
                END
            ');
        } else {
            $find->andWhere(['>', 't.extracted_at', new Expression('((NOW()) - make_interval(secs := c.price_lifetime))')]);
        }

        if (empty($rr) === 0) {
            $find->andWhere(new Expression("t.regions @> '1'"));
        }
        if (count($rr) === 1) {
            $find->andWhere(new Expression("t.regions @> '{$rr[0]}'"));

        } else if (count($rr) === 2) {
            $find->andWhere(['or', new Expression("t.regions @> '{$rr[0]}'"), new Expression("t.regions @> '{$rr[1]}'")]);
        } else {
            $jsonRegArr = implode("','", $rr);
            $jsonRegArr = "'$jsonRegArr'";
            $find->andWhere(new Expression("t.regions @> ANY (ARRAY [$jsonRegArr]::jsonb[])"));
        }

        self::ltime('found refined');

        $project->projectCompetitors();
        self::ltime('found competitors');
        $keyCompetitorsIds      = $project->projectKeyCompetitorsIds();
        $allCompetitorsIds      = $project->projectCompetitorsIds();
        $inactiveCompetitorsIds  = $project->inactiveProjectCompetitorsIds();
        $marketplaceCompetitorsIds = $project->projectMarketplaceCompetitorsIds();
        /**
         * https://glpi.vseinstrumenti.ru/front/ticket.form.php?id=137700
         */
        $projectCompetitorFinalModifiers = $project->projectCompetitorFinalModifiers();

        self::ltime('got ids');

        switch ($project->competition_mode_id) {
            case CompetitionMode::MODE_COMPETITORS:
                $find->andWhere([
                    't.competitor_id' => $allCompetitorsIds,
                ]);
                self::ltime(' comp 1');
                break;
            case CompetitionMode::MODE_ALL_KNOWN_SHOPS:
                $find->andWhere(['not',
                    ['t.competitor_id' => null]
                ]);
                self::ltime(' comp 2');
                break;
            default:
            case CompetitionMode::MODE_ALL_SHOPS_MIN;
            case CompetitionMode::MODE_ALL_SHOPS;
                self::ltime(' comp3');
                break;
        }

        $excludeCompetitorsIds = $project->excludedCompetitors(
            $projectItem->item->brand_id,
            $projectItem->item->getCategoryItems()->select('category_id')->column(),
            $projectItem->item_id
        );
        if (!$excludeCompetitorsIds) {
            $excludeCompetitorsIds = [];
        }
        //        if ($excludeCompetitorsIds && !empty($excludeCompetitorsIds) && count($excludeCompetitorsIds) > 0) {
//            $find->andWhere(['not', [
//                'competitor_id' => $excludeCompetitorsIds
//            ]]);
//        }

        self::ltime('excluded');
//        $find->andWhere('(CASE WHEN t.competitor_id IN (\''
//            . implode('\', \'',
//                Competitor::find()
//                    ->select('id')
//                    ->andWhere(['is_marketplace' => true])
//                    ->column()
//            )
//            . '\') THEN t.out_of_stock = false ELSE true END)');
        // echo $find->createCommand()->rawSql.PHP_EOL;
        $competitorPrices = [];
        foreach($find->each() as $price) {
            $competitorPrices[] = $price;
            if ($debug) {
                print_r($price->price. ' ' . $price->competitor . PHP_EOL);
            }
        }
        //Error::logError(print_r($find->createCommand()->getRawSql(), true));

        self::ltime('found comp prices');
        // Отфильтровать повторяющиеся и ключевые/не ключевые

        /** @var PriceRefined[] $allCompetitorPrices */
        $allCompetitorPrices  = [];
        /** @var PriceRefined[] $keyCompetitorPrices */
        $keyCompetitorPrices  = [];
        /** @var PriceRefined[] $historyCompetitorPrices */
        $historyCompetitorPrices  = [];
        /** @var PriceRefined[] $competitorPrices */

        if ($debug) {
            print_r( 'Цены по данному товару:' . PHP_EOL);
            foreach ($competitorPrices as $log) {
                print_r( $log->price. ' ' . $log->competitor . PHP_EOL);
            }
        }
        $competitorPrices = self::filterPrices($competitorPrices, $projectItem, $project);
        self::ltime('filter prices');


        // ID ВсехИнструментов = a4a9e65e-eab7-4f1b-bbb5-17c9ec976dee TODO: замеить на выбор из базы
        $viId = 'a4a9e65e-eab7-4f1b-bbb5-17c9ec976dee';

        $noKeyCompetitors = false;

        if ($competitorPrices) {
            foreach ($competitorPrices as $i => $nextPrice) {

                if (!$project->use_vi)  {
                    if ($nextPrice->competitor_id === $viId) {
                        continue;
                    }
                    if (preg_match('/всеинструменты/ui', trim($nextPrice->competitor_shop_name))) {
                        continue;
                    }
                }
                $comp       = $nextPrice->competitor_id ? $nextPrice->competitor_id : $nextPrice->competitor_shop_name;
                $key        = $nextPrice->item_id . '_' . $comp;
                $replace    = true;
                if (isset($allCompetitorPrices[$key]) && $allCompetitorPrices[$key]) {
                    switch ($project->price_filter_type) {
                        case Project::PRICE_FILTER_TYPE_MIN:
                            $replace = $allCompetitorPrices[$key]->price > $nextPrice->price;
                            break;
                        case Project::PRICE_FILTER_TYPE_LAST:
                            $replace = $allCompetitorPrices[$key]->extracted_at < $nextPrice->extracted_at;
                            break;
                        case Project::PRICE_FILTER_TYPE_DEFAULT:
                        default:
                            $replace = (
                                (
                                    in_array($nextPrice->competitor_id, $marketplaceCompetitorsIds)
                                    && (
                                        $allCompetitorPrices[$key]->out_of_stock > $nextPrice->out_of_stock
                                        ||
                                        ($nextPrice->price != 0 && !$nextPrice->out_of_stock && $allCompetitorPrices[$key]->price > $nextPrice->price)
                                    )
                                )
                                || $allCompetitorPrices[$key]->extracted_at < $nextPrice->extracted_at
                            );
                            break;
                    }
                    // В первую очередь брать данные с сайта
                    if ($nextPrice->source_id === Source::SOURCE_WEBSITE &&
                        $allCompetitorPrices[$key]->source_id !== Source::SOURCE_WEBSITE) {
                        $replace = true;
                    }
                }
                if ($replace) {
                    if (!in_array($nextPrice->competitor_id, $inactiveCompetitorsIds, true) &&
                        !in_array($nextPrice->competitor_id, $excludeCompetitorsIds, true)) {
                        if ($nextPrice->competitor_id && in_array($nextPrice->competitor_id, $keyCompetitorsIds, true)) {
                            $keyCompetitorPrices[$key] = $nextPrice;
                        }
                        $allCompetitorPrices[$key] = $nextPrice;
                    }
                    $historyCompetitorPrices[$key] = $nextPrice;
                }
            }
            foreach ($competitorPrices as $i => $price) {
                if ($price->out_of_stock || $price->price === 0) {
                    unset($competitorPrices[$i]);
                }
            }
            foreach ($allCompetitorPrices as $i => $price) {
                if ($price->out_of_stock || $price->price === 0) {
                    unset($allCompetitorPrices[$i]);
                }
            }
            foreach ($keyCompetitorPrices as $i => $price) {
                if ($price->out_of_stock || $price->price === 0) {
                    unset($keyCompetitorPrices[$i]);
                }
            }
            foreach ($historyCompetitorPrices as $i => $price) {
                if ($price->out_of_stock || $price->price === 0) {
                    unset($historyCompetitorPrices[$i]);
                }
            }

            $allCompetitorPrices = array_values($allCompetitorPrices);
            $keyCompetitorPrices = array_values($keyCompetitorPrices);
            $historyCompetitorPrices = array_values($historyCompetitorPrices);


            self::ltime('sort prices');

            if (count($keyCompetitorPrices) > 0) {
                $competitorPrices = $keyCompetitorPrices;
            } else {
                $noKeyCompetitors = true;
                $competitorPrices = $allCompetitorPrices;
            }
        }
        if ($debug) {
            print_r('Участвует:'. PHP_EOL);
            foreach ($competitorPrices as $log) {
                print_r($log->price . ' ' . $log->competitor . PHP_EOL);
            }
        }
        /** @var PriceRefined[] $competitorPrices */
        if (!$competitorPrices || count($competitorPrices) == 0) {
            $calculatedPrice->price = 0;
            $noPrice = true;
        }

        if (!$noPrice) {
            $RRP = $projectItem->item->price_recommended_retail;

            // Если РРЦ не ноль
            if ($RRP) {
                // Если Регламент РРЦ
                if ($projectItem->rrp_regulations) {

                    /**
                     * «Алгоритм»
                     * 1) Если есть цены ключевых конкурентов, то РЦ = средняя по ценам КК;
                     * 2) Если нет цен КК, то РЦ = средняя цена по НЕ КК;
                     * 3) Подсчет средней цены от указанных цен, с проверкой на максимальное отклонение РЦ от цены конкурентов с проставленным % отклонения по каждому
                     */
                    self::ltime('algorytm start');
                    $RC = static::calculateAveragePrice($project, $projectItem, $competitorPrices, $allCompetitorPrices, $inactiveCompetitorsIds, $noKeyCompetitors);
                    self::ltime('algorytm end');
                    if ($debug) print_r('средняя = ' . $RC . PHP_EOL);

                    # Если РЦ больше РЦЦ то РЦ = РРЦ
                    if ($RC > $RRP) {
                        # РЦ = РРЦ
                        $calculatedPrice->price = $RRP;
                    }
                    else {
                        # РЦ = «Алгоритм» (В данном случае Результат Алгоритма уже записан в РЦ)
                        $calculatedPrice->price =  $RC;
                    }
                } // Если НЕ регламент РРЦ
                else {
                    # РЦ = РРЦ
                    $calculatedPrice->price = $RRP;
                }
            }
            // Если РРЦ = 0
            else {
                # РЦ = «Алгоритм»
                /**
                 * «Алгоритм»
                 * 1) Если есть цены ключевых конкурентов, то РЦ = средняя по ценам КК;
                 * 2) Если нет цен КК, то РЦ = средняя цена по НЕ КК;
                 * 3) Подсчет средней цены от указанных цен, с проверкой на максимальное отклонение РЦ от цены конкурентов с проставленным % отклонения по каждому
                 */
                self::ltime('algorytm start');
                $calculatedPrice->price = static::calculateAveragePrice($project, $projectItem, $competitorPrices, $allCompetitorPrices, $inactiveCompetitorsIds, $noKeyCompetitors);
                self::ltime('algorytm end');
                if ($debug) print_r('средняя = ' . $calculatedPrice->price . PHP_EOL);
            }

            self::ltime('findMaxFinalModifier start');
            $finalModifier = self::findMaxFinalModifier($competitorPrices, $projectCompetitorFinalModifiers); // Изменить РЦ на процент указанный у конкурентов https://glpi.vseinstrumenti.ru/front/ticket.form.php?id=137700
            self::ltime('findMaxFinalModifier end');

            $calculatedPrice->price = $calculatedPrice->price * $finalModifier;

            if ($debug)
                print_r("с процентом (x$finalModifier) = " . $calculatedPrice->price . PHP_EOL);

            self::ltime('getMinimumPossibleModelPrice start');
            $minPossiblePrice           = static::getMinimumPossibleModelPrice($projectItem, $project);
            self::ltime('getMinimumPossibleModelPrice end');
            # РЦ = Мax(МЦ; РЦ)
            $calculatedPrice->price     = max($minPossiblePrice, $calculatedPrice->price);

            if ($debug)
                print_r("max($minPossiblePrice, {$calculatedPrice->price}) = " . $calculatedPrice->price . PHP_EOL);

            // Округляем цену
            $calculatedPrice->price                 = ($calculatedPrice->price == $RRP) ? $calculatedPrice->price : static::round9($calculatedPrice->price);
            $calculatedPrice->project_execution_id  = $projectExecution->id;
            $calculatedPrice->project_id            = $project->id;

//            if (!$calculatedPrice->validate()) {
//                throw new InvalidParamException(Json::encode($calculatedPrice->errors));
//            }
            self::ltime('validated');
            // Сохраняем цену
            $calculatedPrice->save(false);
            self::ltime('saved');
        }

        // История
        if ($project->is_logging) {
            $calculatedPrice->saveHistory($project,$projectItem, $historyCompetitorPrices, $keyCompetitorsIds, $inactiveCompetitorsIds, $excludeCompetitorsIds);
        }

        if ($noPrice) {
            return null;
        }
        if ($debug) {
            print_r('РЦ = ' . $calculatedPrice->price . PHP_EOL);
        }
        return $calculatedPrice;
    }

    /**
     * Найти максимальный финальный модификатор цены среди всех модификатор конкурентов
     * https://glpi.vseinstrumenti.ru/front/ticket.form.php?id=137700
     * Пример: цена на товар  "Перфоратор " рассчитывает исходя из цен конкурентов  А и В и равноа 100 руб.
     * Если по конкурентам А и В установлен параметр "Изменить РЦ на" : "-2%" и "-3%" соответственно - берётся максимальный показатель
     * (т.е. "-2%") и расчётная цена корректируется как 100*(1-0.02) = 98 руб
     * @param PriceRefined[] $competitorPrices
     * @param array $projectCompetitorFinalModifiers
     * @return float|int
     */
    public static function findMaxFinalModifier($competitorPrices, $projectCompetitorFinalModifiers) {
        $finalModifier = 1;
        $finalModifiers = [];
        foreach ($competitorPrices as $competitorPrice) {
            if (isset($projectCompetitorFinalModifiers[$competitorPrice->competitor_id])) {
                $finalModifiers[] = $projectCompetitorFinalModifiers[$competitorPrice->competitor_id];
            }
        }
        if (!empty($finalModifiers)) {
            $finalModifier = ((100 + max($finalModifiers)) / 100);
        }
        return $finalModifier;
    }

    /**
     * «Алгоритм»
     * 1) Если есть цены ключевых конкурентов, то РЦ = средняя по ценам КК;
     * 2) Если нет цен КК, то РЦ = средняя цена по НЕ КК;
     * 3) Подсчет средней цены от указанных цен, с проверкой на максимальное отклонение РЦ от цены конкурентов с проставленным % отклонения по каждому
     * @param Project $project
     * @param NomenclatureDocumentItem|ProjectItem $projectItem
     * @param PriceRefined[] $competitorsPrices
     * @param PriceRefined[] $allCompetitorPrices
     * @param array $inactiveCompetitorsIds
     * @param bool $noAverage
     * @return float
     */
    public static function calculateAveragePrice(Project $project, $projectItem , $competitorsPrices, $allCompetitorPrices, $inactiveCompetitorsIds = [], $noAverage = false) {
        if (empty($competitorsPrices) || empty($allCompetitorPrices)) {
            return 0;
        }

        // Тип «Превая цена по конкурентам» ?
        if ($project->competition_mode_id == CompetitionMode::MODE_ALL_SHOPS_MIN) {
            $allShopMinPrices = [];
            /**
            Берутся цены всех магазины по 
            данному региону, в том числе 
            магазины НЕ из спрпвочника 
            «Конкуренты» и цены 
            конкурентов без региона;
             */
            foreach ($allCompetitorPrices as $rp) {
                if (!in_array($rp->competitor_id, $inactiveCompetitorsIds)) {
                    $allShopMinPrices[] = $rp->price;
                }
            }
            // РЦ = Минимальная цена магазаина
            return min($allShopMinPrices);
        }

        $average = 0;
        if (!$noAverage) {
            foreach ($competitorsPrices as $price) {
                $average += floatval($price->price);
            }
            // Находим среднюю цену
            $average = $average / count($competitorsPrices);
        }

        $projectVariationCompetitors = $project->projectVariationCompetitors();
        $projectVariationCompetitorsIds = array_keys($projectVariationCompetitors);

        $prices = [];

        foreach ($allCompetitorPrices as $rp) {
            if (in_array($rp->competitor_id, $projectVariationCompetitorsIds, false)) {
                // Добавляем к массиву цены пороговых конкурентов
                $prices[] = round($rp->price * (1 + $projectVariationCompetitors[$rp->competitor_id] / 100));
            }
        }


        // Добавляем к массиву среднюю цену, если
        if(!$noAverage && $average) {
            $prices[] = $average;
        }

        if (count($prices) == 0) {
            if ($average) {
                return $average;
            }
            return 0;
        }
        return ceil(min($prices));
    }

    /**
     * Отфильтровать цены
     *
     * Выше пороговой суммы работает формула:
     * (K1*Y) <= X <= (K2*Y),
     * Ниже пороговой суммы работает формула:
     * (K3*Y) <= X <= (K4*Y), где
     * X - искомая цена на товар у конкурента,
     * Y - закупочная цена на товар из ВТИСа,
     * K1 - коэффициент № 1,
     * K2 - коэффициент № 2,
     * K3 - коэффициент № 3,
     * K4 - коэффициент № 4
     *
     * @param NomenclatureDocumentItem|ProjectItem $projectItem
     * @param Project $project
     * @return PriceRefined[]
     */
    public static function filterPrices($refinedPrices, $projectItem, Project $project) {
        if ($refinedPrices) {
            $supplyPrice     = floatval($projectItem->item->price_supply);
            $defaultPrice    = floatval($projectItem->item->price_default);

            /** @var PriceRefined[] $filteredPrices */
            $filteredPrices = array();

            $k1         = $supplyPrice      * $project->price_range_k1;//
            $k2         = $defaultPrice     * $project->price_range_k2;
            $k3         = $supplyPrice      * $project->price_range_k3;
            $k4         = $defaultPrice     * $project->price_range_k4;
            $threshold  = $project->price_range_threshold;


            foreach ($refinedPrices as $price) {
                /** @var PriceRefined $price */
                // print_r("$supplyPrice * {$project->price_range_k1}  < {$price->price } < $supplyPrice * {$project->price_range_k2}".PHP_EOL);
                // print_r("$k1 < {$price->price } < $k2".PHP_EOL);
                $add = false;

                if ($price->price < $threshold) {
                    if ($k3 <= $price->price && $price->price <= $k4) {
                        $add = true;
                    }
                } else {
                    if ($k1 <= $price->price && $price->price <= $k2) {
                        $add = true;
                    }
                }
                if ($price->price == 0 && $price->out_of_stock) {
                    $add = true;
                }
                if ($add) {
//                    if (!$price->out_of_stock) {
                    $filteredPrices[] = $price;
//                    }
                }
            }
            // фильтрация по региону (если в проекте несколько регионов, то берется минимальная цена)
//            $regionPrices = [
//                1 => [
//                    'comp_id-item_id'
//                ]
//            ];
//            foreach ($filteredPrices as $i => $price) {
//                /** @var PriceRefined $price */
//                $regions = $price->regions;
//                foreach ($regions as $regionId) {
//                    if (!isset($regionPrices[$regionId])) {
//                        $regionPrices[$regionId] = [];
//                    }
//                    $itemKey = $price->competitor_id . '-' . $price->item_id;
//                    if (!isset($regionPrices[$regionId][$itemKey])) {
//                        $regionPrices[$regionId][$itemKey] = $price;
//                    }
//                }
////                if (isset($regionPrices[$price->region]))
//            }
            return $filteredPrices;
        } else {
            return null;
        }
    }

    /**
     * Округление до девяток
     * @param float $price
     * @return float
     */
    public static function round9($price) {
        $price = ceil($price);
        if ($price <= 0) {
            return 0;
        }
        if ($price > 300) {
            $price = floor($price /  10) * 10 + 9;
        }
//        if (true || $price < 5000){
//            $price = floor($price /  10) * 10 + 9;
//        } else {
//            $price = floor($price /  100) * 100 + 90;
//        }
        return $price;
    }

    /**
     * Мин цена(МЦ) = Мин наценка *З.Ц.
     * @param NomenclatureDocumentItem|ProjectItem $projectItem
     * @param Project $project
     * @return float
     */
    public static function getMinimumPossibleModelPrice($projectItem, Project $project) {
        $supplyPrice            = floatval($projectItem->item->price_supply);
        $min_markup             = floatval($projectItem->min_margin === null ? $project->min_margin : $projectItem->min_margin);
        $modifier = 1 + $min_markup/100;
        return $supplyPrice * $modifier;
    }

    /***
     * Сохранение истории
     * @param PriceRefined[] $allCompetitorPrices
     * @param array $keyCompetitorIds
     * @param array $inactiveCompetitorsIds
     * @param array $excludeCompetitorsIds
     */
    public function saveHistory($project, $projectItem, $allCompetitorPrices, $keyCompetitorIds = [], $inactiveCompetitorsIds = [], $excludeCompetitorsIds = []) {

        // Расчет цены по конкурентам
        $logPriceCalculations = [];

        foreach ($allCompetitorPrices as $priceRefined) {
            $logPriceCalculation = [
                'item_id'                   => $projectItem->item_id,
                'project_id'                => $project->id,
                'project_item_id'           => $projectItem->id,
                'project_execution_id'      => $this->project_execution_id,
                'price_calculated_id'       => $this->id,
                'price_calculated'          => $this->price,
                'price_refined_id'          => $priceRefined->id,
                'price_refined'             => $priceRefined->price,
                'source_id'                 => $priceRefined->source_id,
                'regions'                   => is_array($priceRefined->regions) ? implode(',', $priceRefined->regions) : null,
                'competitor_id'             => $priceRefined->competitor_id,
                'extracted_at'              => $priceRefined->extracted_at->format('Y-m-d H:i:s'),
                'rrp_regulations'           => $projectItem->rrp_regulations,
                'out_of_stock'              => $priceRefined->out_of_stock,
                'created_at'                => date('Y-m-d H:i:s'),
                'is_key_competitor'         => in_array($priceRefined->competitor_id, $keyCompetitorIds),
                'price_supply'              => null,
                'price_recommended_retail'  => null,
                'price_default'             => null,
                'item_name'                 => null,
                'item_brand_name'           => null,
                'item_ym_index'             => null,
                'margin'                    => null,
                'competitor_shop_name'      => $priceRefined->competitor_shop_name,
                'status_id'                 => Status::STATUS_ACTIVE,
                'delivery_days'             => $priceRefined->delivery_days,
                'competitor_item_seller'    => $priceRefined->competitor_item_seller,
            ];

            if (in_array($priceRefined->competitor_id, $inactiveCompetitorsIds)) {
                $logPriceCalculation['status_id'] = Status::STATUS_REMOVED;
            }
            if (in_array($priceRefined->competitor_id, $excludeCompetitorsIds)) {
                $logPriceCalculation['status_id'] = Status::STATUS_DISABLED;
            }

            if ($projectItem->item) {
                $logPriceCalculation['brand_id']                    = $projectItem->item->brand_id;
                $logPriceCalculation['price_supply']                = $projectItem->item->price_supply;
                $logPriceCalculation['price_recommended_retail']    = $projectItem->item->price_recommended_retail;
                $logPriceCalculation['price_default']               = $projectItem->item->price_default;
                $logPriceCalculation['price_weighted']              = $projectItem->item->price_weighted;
                $logPriceCalculation['item_name']                   = $projectItem->item->name;
                $logPriceCalculation['item_brand_name']             = $projectItem->item->brand ? $projectItem->item->brand->name : null;
                $logPriceCalculation['item_ym_index']               = "{$projectItem->item->ym_index}";
                $logPriceCalculation['item_ym_url']                 = $projectItem->item->ymUrl;
                if ($this->price != 0) {
                    $logPriceCalculation['margin'] = round(100 * ($this->price - $projectItem->item->price_supply) / $this->price,2);
                }
            }
            $logPriceCalculation['url']             = $priceRefined->url;
            $logPriceCalculations[] = $logPriceCalculation;
        }

        if (count($logPriceCalculations) > 0) {
            $this->projectExecution()->publishHistory($logPriceCalculations);
        }

        if ($this->price != 0) {

            $historyItem = [
                'item_id' => $projectItem->item_id,
                'project_id' => $project->id,
                'project_item_id' => $projectItem->id,
                'project_execution_id' => $this->project_execution_id,
                'price_calculated_id' => $this->id,
                'price_calculated' => $this->price,
                'rrp_regulations' => $projectItem->rrp_regulations,
                'created_at' => date('Y-m-d H:i:s'),
                'is_export' => false,
            ];

            if ($projectItem->item) {
                $historyItem['price_supply'] = $projectItem->item->price_supply;
                $historyItem['price_recommended_retail'] = $projectItem->item->price_recommended_retail;
                $historyItem['price_default'] = $projectItem->item->price_default;
                $historyItem['price_weighted'] = $projectItem->item->price_weighted;
                $historyItem['item_name'] = $projectItem->item->name;
                $historyItem['item_brand_name'] = $projectItem->item->brand ? $projectItem->item->brand->name : null;
                $historyItem['brand_id'] = $projectItem->item->brand_id;
                $historyItem['item_ym_index'] = $projectItem->item->ym_index;
                $historyItem['item_ym_url'] = $projectItem->item->ymUrl;
                $historyItem['margin'] = round(100 * ($this->price - $projectItem->item->price_supply) / $this->price);
            }

            $this->projectExecution()->publishHistoryExec($historyItem);
        }
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'item',
            'price',
            'created_at',
            'projectExecution',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations() {
        return [
            'projectExecution',
            'item',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'projectExecution',
            'project',
            //'logPriceCalculation',
            'item',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectExecution()
    {
        return $this->hasOne(ProjectExecution::className(), ['project_id' => 'project_id', 'id' => 'project_execution_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectItem()
    {
        return $this->hasOne(ProjectItem::className(), ['project_id' => 'project_id', 'item_id' => 'item_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogPriceCalculation()
    {
        return $this->hasMany(LogPriceCalculation::className(), ['price_calculated_id' => 'id']);
    }


}