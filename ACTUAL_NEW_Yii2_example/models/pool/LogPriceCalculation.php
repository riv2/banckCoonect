<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\DateTime;
use app\components\ValidationRules;
use app\models\cross\CategoryItem;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\reference\Item;
use app\models\reference\Project;
use app\models\document\ProjectExecution;
use app\models\enum\Region;
use app\models\reference\ProjectItem;
use app\validators\UuidValidator;
use yii\bootstrap\Html;
use yii\db\Expression;
use yii\db\Query;

/**
 * Class LogPriceCalculation
 *
 * @package app\models\pool
 *
 * @property int    source_id           Торговая площадка
 * @property string regions           Регион
 * @property string item_id             Товар
 * @property string competitor_id       Конкурнет
 * @property string item_name
 * @property string item_brand_name
 * @property string item_ym_index
 * @property string item_ym_url
 * @property string brand_id
 *
 * @property string competitor_shop_name           Название магазина
 * @property string competitor_shop_domain         Домен
 * @property string competitor_item_seller         Продавец
 *
 * @property string competitor_item_name    Название товара на торговой площадке
 * @property string competitor_item_sku     Артикул или YM ID
 * @property string url     УРЛ в магазине
 * @property int    delivery_days
 * @property int    status_id
 * @property int    project_id              Проект
 * @property string project_item_id         Номенклатура проекта
 * @property string project_execution_id    Исполнение проекта
 * @property string price_calculated_id     Расчетная цена
 * @property string price_refined_id        Цена конкурента
 *
 * @property boolean out_of_stock        Инф. о неналичии
 * @property boolean is_key_competitor
 * @property boolean rrp_regulations
 * 
 * @property float  price_refined           
 * @property float  price_calculated         
 * @property float  price_supply            
 * @property float  price_recommended_retail      
 * @property float  price_default
 * @property float price_weighted Средневзвешенная
 * @property DateTime extracted_at      Дата сбора данных
 * 
 * @property float  margin
 *
 * @property Source             source              Торговая площадка
 * @property Item               item                Товар
 * @property Competitor         competitor          Конкурнет
 * @property Project            project             Проект
 * @property ProjectItem        projectItem         Номенклатура проекта
 * @property ProjectExecution   projectExecution    Исполнение проекта
 * @property PriceCalculated    priceCalculated     Расчетная цена
 * @property PriceRefined    priceRefined    Цена конкурента
 * @property CompetitorItem     competitorItem
 */

class LogPriceCalculation extends Pool
{
    public $comparePrice = null;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Детализация расчета цены';
    }

    public static function noCount() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Детализация расчета цен';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleDateTime('extracted_at'),
            ValidationRules::ruleUuid('item_id'),
            ValidationRules::ruleUuid('brand_id'),
            ValidationRules::ruleUuid('competitor_id'),
            ValidationRules::ruleUuid('project_id'),
            ValidationRules::ruleUuid('project_item_id'),
            ValidationRules::ruleUuid('project_execution_id'),
            ValidationRules::ruleUuid('price_calculated_id'),
            ValidationRules::ruleUuid('price_refined_id'),
            [
                [['price_refined', 'price_calculated', 'price_supply', 'price_recommended_retail', 'price_default','margin','price_weighted','delivery_days'], 'number'],
                [['competitor_shop_name', 'competitor_shop_domain'], 'string'],
                [['competitor_item_name', 'competitor_item_sku', 'url'], 'string'],
                [['item_name', 'item_brand_name', 'item_ym_index','item_ym_url'], 'string'],
                [['out_of_stock','is_key_competitor'], 'boolean'],
                [['regions', 'comparePrice'], 'safe'],
            ],
            ValidationRules::ruleDefault('out_of_stock', false),
            ValidationRules::ruleEnum('source_id', Source::className()),
            ValidationRules::ruleEnum('status_id', Status::className()),

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
                'id'            => 'ID',
                'extracted_at' => 'Дата сбора',
                'source_id' => 'ID Торговой площадки',
                'regions' => 'ID Региона',
                'item_id'   => 'ID Товара',
                'brand_id'   => 'ID Бренда',
                'item_name' => 'Наименование',
                'item_brand_name'   => 'Бренд',
                'item_ym_index'     => 'YM ID',
                'item_ym_url'       => 'YM URL',
                'competitor_id'     => 'ID Конкурента',

                'competitor_shop_name' => 'Название магазина',
                'competitor_shop_domain' => "Домен магазина",
                'competitor_item_name' => 'Наименование товара',
                'competitor_item_sku' => 'Артикул конкур.',
                'url' => 'URL конкур.',
                'out_of_stock' => 'Инф. о неналичии',
                
                'project_id' => 'ID проекта',
                'project_item_id' => 'ID номенклатуры проекта',
                'project_execution_id' => 'ID исполнения проекта',
                'price_calculated_id' => 'ID расчетной цены',
                'price_refined' => 'Цена конкурента',
                'price_refined_id' => 'ID цены конкурента',
                'price_calculated' => 'Расчетная цена',
                'price_supply' => 'Цена закупки',
                'price_weighted'    => 'Cредневзвешенная ЗЦ',
                'price_recommended_retail' => 'РРЦ',
                'price_default' => 'ВИ МСК',
                'priceRefined'      => 'Цена конкурента',

                'source' => 'Торговая площадка',
                'item' => 'Товар',
                'competitor' => 'Конкурент',
                'project' => 'Проект',
                'projectItem' => 'Нменклатура проекта',
                'projectExecution' => 'Исполнение проекта',
                'priceCalculated' => 'Расчетная цена',

                'is_key_competitor' => 'Ключевой конкурент',
                'rrp_regulations'       => 'Регламент РРЦ',
                'margin'                => 'Маржа',
                'status_id' => 'Статус',
                'competitorItem' => 'Товар конкурента',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations() {
        return [
            'item',
            //'source',
            //'competitor',
        ];
    }

    public function getSort($config = [])
    {
        $config['attributes'] = [
            'item' => [
                'asc'   => ['item_name' => SORT_ASC],
                'desc'  => ['item_name' => SORT_DESC],
                'label' => 'Товар',
                'default' => SORT_ASC
            ],
            'priceCalculated' => [
                'asc'   =>   ['price_calculated' => SORT_ASC],
                'desc'  =>   ['price_calculated' => SORT_DESC],
                'label' => 'Расчетная цена',
                'default' => SORT_ASC
            ],
            'priceRefined' => [
                'asc'   =>   ['price_refined' => SORT_ASC],
                'desc'  =>   ['price_refined' => SORT_DESC],
                'label' => 'Цена конкурента',
                'default' => SORT_ASC
            ],
        ];
        $sort = parent::getSort($config);
        return $sort;
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'created_at',
            'extracted_at',
            'item_id',
            'item',
            'item_brand_name',
            'price_calculated',
            'price_refined',
            'priceCalculated',
            'priceRefined',
            'competitor' => [
                'label' => 'Конкурент',
                'format'=> 'raw',
                'value' => function($model) {
                    /** @var \app\models\pool\LogPriceCalculation $model */
                    return ($model->competitor_id) ? Html::a($model->competitor, ['/crud-competitor/view', 'id' => $model->competitor_id]):$model->competitor_shop_name;
                }
            ],
            'source',
            'rrp_regulations',
            'price_supply',
            'price_recommended_retail',
            'price_default',
            'price_weighted',
            'margin' => [
                'label' => 'Маржа',
                'value' => function($model) {
                    /** @var \app\models\pool\LogPriceCalculation $model */
                    return $model->margin.'%';
                }
            ],
            'url' => [
                'label' => 'УРЛ',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model->source_id == Source::SOURCE_YANDEX_MARKET) {
                        $url = $model->item_ym_url;
                    } else {
                        /** @var \app\models\pool\LogPriceCalculation $model */
                        if (!$model->competitorItem) {
                            return null;
                        }
                        $url = $model->competitorItem->url;
                    }
                    if (!$url) {
                        return null;
                    }
//                    $pos = mb_strpos($url,'[PARAM]');
//                    if ($pos && $pos > -1) {
//                        $url = mb_substr($url, 0, mb_strlen($url) - $pos);
//                    }
                    return '<a href="'.ANON_URL.$url.'" target="_blank">'.$url.'</a>';
                }
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'source',
            //'project',
            'item',
            'competitor',
            //'priceRefined',
            //'priceCalculated',
            //'projectItem',
            'projectExecution',
            //'status',
        ]);
    }

    /*
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(Source::className(), ['id' => 'source_id']);
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
    public function getProjectItem()
    {
        return $this->hasOne(ProjectItem::className(), ['project_id' => 'project_id', 'id' => 'project_item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitor()
    {
        return $this->hasOne(Competitor::className(), ['id' => 'competitor_id']);
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
    public function getPriceCalculated()
    {
        return $this->hasOne(PriceCalculated::className(), ['project_execution_id' => 'project_execution_id', 'id' => 'price_calculated_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceRefined()
    {
        return $this->hasOne(PriceRefined::className(), ['competitor_id' => 'competitor_id','id' => 'price_refined_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitorItem()
    {
        return $this->hasOne(CompetitorItem::className(), ['competitor_id' => 'competitor_id','item_id' => 'item_id']);
    }

    /**
     * @inheritdoc
     */
    protected function addQuickSearchConditions(\yii\db\ActiveQuery $query)
    {
        $query = parent::addQuickSearchConditions($query);
        $query->leftJoin(['i' => Item::tableName()], 'i.id = t.item_id');
        return $query;
    }

    /**
     * @inheritdoc
     */
    public function processSearchToken($token, array $attributes, $tablePrefix = null)
    {
        //$c = parent::processSearchToken($token, $attributes, $tablePrefix);
        if (UuidValidator::test($token)) {
            return ['i.id' => $token];
        }
        return ['ILIKE', 'i.name', $token];
    }

    public function crudSearch($params = [])
    {
        $chartDate = isset($params['chart_date']) ? $params['chart_date'] : null;
        $chartType = isset($params['chart_type']) ? $params['chart_type'] : null;
        $categoryId = isset($params['category_id']) ? $params['category_id'] : null;
        $chartCompetitorName = isset($params['chart_competitor_name']) ? $params['chart_competitor_name'] : null;
        if ($chartDate) {
            $projectExecutionId = ProjectChart::find()
                ->andWhere([
                    'project_id' => $params['LogPriceCalculation']['project_id'],
                    'type' => $chartType,
                    'date' => $chartDate,
                ])
                ->select('project_execution_id')
                ->scalar();
            if (!$projectExecutionId) {
                $projectExecutionId = '00000000-0000-0000-0000-000000000000';
            }
            $params['LogPriceCalculation']['project_execution_id'] = $projectExecutionId;
        }
        if ($chartCompetitorName) {
            $competitorId = Competitor::find()
                ->andWhere([
                    'name' => $chartCompetitorName,
                ])
                ->select('id')
                ->scalar();
            if (!$competitorId) {
                $competitorId = '00000000-0000-0000-0000-000000000000';
            }
            $params['LogPriceCalculation']['competitor_id'] = $competitorId;
        }
        if ($chartType) {
            $params['LogPriceCalculation']['status_id'] = [
                Status::STATUS_ACTIVE,
                Status::STATUS_REMOVED,
                Status::STATUS_DISABLED,
            ];
        }

        $query = parent::crudSearch($params)->alias('t');

        if ($chartType) {
            switch ($chartType) {
                case ProjectChart::TYPE_VI_COMPARE:
                    $chartSeriesIndex = $params['chart_series_index'];
                    switch ($chartSeriesIndex) {
                        case 0: // > 5%
                            $query->andWhere('
                                t.price_refined > t.price_calculated
                                AND (((t.price_refined/t.price_calculated) * 100 - 100) > 5)
                            ');
                        break;
                        case 1: // +1% - +5%
                            $query->andWhere('
                                t.price_refined > t.price_calculated
                                AND 1 < ((t.price_refined/t.price_calculated) * 100 - 100)
                                AND ((t.price_refined/t.price_calculated) * 100 - 100) < 5
                            ');
                        break;
                        case 2: // +-1%
                            $query->andWhere('
                                -1 < ((t.price_refined/t.price_calculated) * 100 - 100)
                                AND ((t.price_refined/t.price_calculated) * 100 - 100) < 1
                            ');
                        break;
                        case 3: // -1% - -5%
                            $query->andWhere('
                                t.price_refined < t.price_calculated
                                AND -1 > ((t.price_refined/t.price_calculated) * 100 - 100)
                                AND ((t.price_refined/t.price_calculated) * 100 - 100) > -5
                           ')
                           ;
                        break;
                        case 4: // < -5%
                            $query->andWhere('
                                t.price_refined < t.price_calculated
                                AND (((t.price_refined/t.price_calculated) * 100 - 100) < -5)
                            ');
                        break;
                    }
                break;
                case ProjectChart::TYPE_PRICE_DYNAMICS:
                    $chartSeriesIndex = $params['chart_series_index'];
                    $chartItems = (new Query())
                        ->select('json_array_elements_text(value->' . $chartSeriesIndex . ')')
                        ->from(['t' =>
                            'json_each((' . ProjectChart::find()
                                ->select(new Expression('data::json'))
                                ->andWhere([
                                    'type' => ProjectChart::TYPE_PRICE_DYNAMICS,
                                    'project_id' => $this->project_id,
                                    'project_execution_id' => $this->project_execution_id,
                                ])
                                ->orderBy('date DESC')
                                ->limit(1)
                                ->createCommand()
                                ->getRawSql()
                            . '))'
                        ])
                        ->all();
                    $query->andWhere([
                        'item_id' => $chartItems,
                    ]);
                break;
            }
        }
        if ($categoryId) {
            $query
                ->leftJoin(
                    ['ci' => CategoryItem::tableName()],
                    'ci.item_id = t.item_id AND ci.is_top = true'
                )
                ->andWhere([
                    'ci.category_id' => $categoryId,
                ]);
        }
        var_dump($query->createCommand()->getRawSql());
        die;
        return $query;
    }
}