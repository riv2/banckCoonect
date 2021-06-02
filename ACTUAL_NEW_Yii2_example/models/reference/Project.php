<?php
namespace app\models\reference;

use app\components\base\ScheduleTrait;
use app\components\DateTime;
use app\components\base\Entity;
use app\components\base\type\Reference;
use app\components\ReportKpiProject;
use app\components\ValidationRules;
use app\models\cross\CategoryItem;
use app\models\cross\ProjectPriceFormerType;
use app\models\cross\ProjectRegion;
use app\models\cross\ProjectSource;
use app\models\document\ProjectExecution;
use app\models\enum\CompetitionMode;
use app\models\enum\ErrorType;
use app\models\enum\ProjectExecutionStatus;
use app\models\enum\Region;
use app\models\enum\PriceExportMode;
use app\models\enum\SelectPriceLogic;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\pool\LogPriceCalculation;
use app\models\pool\PriceRefined;
use app\models\pool\ProjectChart;
use app\models\pool\ReportKpi;
use app\models\register\Error;
use app\models\register\Task;
use app\validators\TimeSpanValidator;
use app\widgets\FormBuilder;
use netis\crud\db\ActiveQuery;
use yii;
use yii\base\InvalidValueException;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\Cookie;
use app\models\pool\NomenclatureDocumentItem;


/**
 * Class Project
 *
 * Проект
 *
 * @package app\components\base\type
 *
 * @property string     project_theme_id            Тема
 * @property string     region_id                   Регион
 * @property float      min_margin                  Мин. наценка
 * @property int        competition_mode_id         Режим выбора конкурентов
 * @property int        price_former_type_id        Тип цены ПрайсФормера                *Deprecated*
 * @property int        price_export_mode_id        Режим выгрузки цен
 * @property boolean    is_auto_export           Автоматически выгружать
 * @property int        price_relevance_time_span        Срок давности данных
 * @property float      price_range_k1        Коэффициент К1
 * @property float      price_range_k2        Коэффициент К2
 * @property float      price_range_k3        Коэффициент К3
 * @property float      price_range_k4        Коэффициент К4
 * @property float      price_range_threshold       Порог
 * @property boolean    is_logging                  Сохранять историю
 * @property boolean    use_vi                    Цены ВсеИнструментов учавствуют в проекте?
 * @property int        data_life_time_span       Порог
 * @property string priceRelevanceTimeSpan
 * @property string dataLifeTimeSpan
 * @property DateTime scheduled_daily_time
 * @property int        project_execution_status_id
 * @property string     scheduled_weekdays
 * @property float      supply_price_threshold  Цена закупки от
 * @property string nomenclature_document_id
 * @property boolean schedule_started
 * @property integer price_filter_type
 *
 * @property CompetitionMode        competitionMode
 * @property PriceExportMode        priceExportMode
 * @property PriceFormerType        priceFormerType         *Deprecated*
 * @property Region                 region
 * @property ProjectItem[]          projectItems'
 * @property ProjectCompetitor[]    projectCompetitors
 * @property Competitor[]           competitors
 * @property ProjectSource[]        projectSources
 * @property Source[]               sources
 * @property Item[]                 items
 * @property ProjectExecution[]     projectExecutions
 * @property ProjectExecutionStatus     projectExecutionStatus
 * @property array     scheduledWeekdays
 * @property        DateTime last_export_at
 * @property        int last_export_count
 * @property ProjectTheme           projectTheme
 * @property ProjectPriceFormerType[] projectPriceFormerTypes
 * @property PriceFormerType[]      priceFormerTypes
 * @property Region[]               regions
 * @property ProjectRegion[]        projectRegions
 * @property ProjectExecution       lastProjectExecution
 * @property NomenclatureDocument   nomenclatureDocument
 */
class Project extends Reference
{
    use ScheduleTrait;

    const PRICE_FILTER_TYPE_DEFAULT = 1;
    const PRICE_FILTER_TYPE_MIN     = 2;
    const PRICE_FILTER_TYPE_LAST    = 3;

    /** @var ProjectCompetitor[]  */
    private $_projectCompetitors    = false;
    private $_projectKeyCompetitorsIds      = false;
    private $_projectCompetitorsIds         = false;
    private $_projectVariationCompetitors         = false;
    private $_projectSourceIds      = false;
    private $_projectRegionIds      = false;
    private $_groupedProjectItems   = false;
    private $_projectPriceFormerTypeIds  = false;
    private $_inactiveProjectCompetitorsIds= false;
    private $_projectCompetitorFinalModifiers = false;
    private $_projectMarketplaceCompetitorsIds = false;

    /**
     * @var ProjectExecution|null
     */
    private $_lastProjectExecution      = false;

    private $_exclusiveBrands           = false;
    private $_excludedBrands            = false;
    private $_exclusiveCategories       = false;
    private $_excludedCategories        = false;
    private $_excludedItems             = false;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Проект';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Проекты';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleUuid('price_former_type_id'),
            ValidationRules::ruleUuid('project_theme_id'),
            ValidationRules::ruleUuid('nomenclature_document_id'),
            [
                [['priceRelevanceTimeSpan', 'dataLifeTimeSpan'] , TimeSpanValidator::className()],
                [['min_margin', 'price_range_k1', 'price_range_k2','price_range_k3','price_range_k4', 'price_range_threshold', 'supply_price_threshold'], 'number'],
                [['price_relevance_time_span','data_life_time_span','last_export_count'], 'number', 'integerOnly' => true],
                [['is_auto_export','is_logging','use_vi', 'schedule_started'], 'boolean'],
                [['scheduled_daily_time'], 'date', 'format' => 'php:' . DateTime::DB_TIME_FORMAT],
                [['last_export_at'], 'date', 'format' => 'php:' . DateTime::DB_DATETIME_FORMAT],
                [['price_filter_type'], 'in', 'range' => array_keys(self::getPriceFilterTypes())],
                [['scheduled_weekdays'], 'string'],
                [['scheduledWeekdays'], 'safe'],
            ],
            ValidationRules::ruleDefault('is_auto_export',false),
            ValidationRules::ruleDefault('is_logging',false),
            ValidationRules::ruleDefault('data_life_time_span', 2592000),
            ValidationRules::ruleDefault('price_relevance_time_span', 86400),
            ValidationRules::ruleDefault('supply_price_threshold', 300),
            ValidationRules::ruleDefault('price_range_threshold', 800),
            ValidationRules::ruleDefault('price_range_k1', 0.7),
            ValidationRules::ruleDefault('price_range_k2', 1.5),
            ValidationRules::ruleDefault('price_range_k3', 0.9),
            ValidationRules::ruleDefault('price_range_k4', 1.3),
            ValidationRules::ruleEnum('competition_mode_id', CompetitionMode::className()),
            ValidationRules::ruleEnum('price_export_mode_id', PriceExportMode::className()),
            ValidationRules::ruleEnum('region_id', Region::className()),
            ValidationRules::ruleEnum('project_execution_status_id', ProjectExecutionStatus::className()),
            [],
            []
        );
    }


    /**
     * @param array $params
     * @return string
     */
    public function getScheduleDuration($params = []) {
        return '00:15:00';
    }

    /**
     * @param array $params
     * @return string
     */
    public function getScheduleTitle($params = []) {
        return (string)$this;
    }

    /**
     * @param array $params
     * @return string
     */
    public function getScheduleDescription($params = []) {
        return (string)$this;
    }

    /**
     * Функция котрую запускает "Расписание"
     * @param array $params
     */
    public function schedule($params = []) {
        $this->prepareProjectExecution(false);
    }


    public static function getPriceFilterTypes()
    {
        return [
            self::PRICE_FILTER_TYPE_DEFAULT => 'Стандартная: самая последняя цена, если конкурент является торговой площадкой - минимальная',
            self::PRICE_FILTER_TYPE_MIN     => 'Самая минимальная цена',
            self::PRICE_FILTER_TYPE_LAST    => 'Самая последняя спарсенная цена',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'project_theme_id'              => 'Тематика проекта',
                'region_id'                     => 'Регион',
                'min_margin'                    => 'Минимальная наценка',
                'competition_mode_id'           => 'Режим выбора конкурентов',
                'price_former_type_id'          => 'Тип цены ПрайсФормера',
                'price_export_mode_id'          => 'Режим выгрузки цен',
                'is_auto_export'                => 'Автовыгрузка',
                'price_range_k1'                => 'К1',
                'price_range_k2'                => 'К2',
                'price_range_k3'                => 'К3',
                'price_range_k4'                => 'К4',
                'price_range_threshold'         => 'Пороговая сумма',
                'is_logging'                    => 'Сохранять историю',
                'competitionMode'               => 'Режим выбора конкурентов',
                'priceFormerType'               => 'Тип цены ПрайсФормера',
                'priceExportMode'               => 'Режим выгрузки цен',
                'region'                        => 'Регион',
                'project_execution_status_id'   => 'Статус выполнения проекта',
                'scheduled_daily_time'          => 'Запуск по расписанию',
                'schedule_started'              => 'Запущен',
                'price_relevance_time_span'     => 'Срок актуальности собранных цен',
                'data_life_time_span'           => 'Срок документа в PriceFormer',
                'supply_price_threshold'        => 'Цена закупки от',
                
                'priceRelevanceTimeSpan'        => 'Срок актуальности собранных цен',
                'dataLifeTimeSpan'              => 'Срок жизни данных проекта',
                'projectItems'                  => 'Номенклатура проекта',
                'projectCompetitors'            => 'Конкуренты проекта',
                'competitors'                   => 'Конкуренты',
                'projectSources'                => 'Торговые площадки',
                'sources'                       => 'Торговые площадки проекта',
                'items'                         => 'Товары',
                'projectTheme'                  => 'Тематика проекта',
                'use_vi'                        => 'Цены ВИ участвуют',
                'last_export_at'                => 'Последний запуск',
                'last_export_count'             => 'Экспортировано',
                'price_filter_type'             => 'Тип фильтрации',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns()
    {
        $pp                 = Yii::$app->request->get('Project', []);
        $regionsRegionId    = isset($pp['regions.id']) ? $pp['regions.id'] : null;
        return array_merge(parent::crudIndexColumns(),[
            '__actions',
            'name',
            'project_regions_id' => [
                'label'     => \app\models\enum\Region::getPluralNominativeName(),
                'format'    => 'raw',
                'filter'    => FormBuilder::renderSelect2(null, \app\models\enum\Region::className(), 'Project[regions.id]', $regionsRegionId, true, 0),
                'value'     => function($model) {
                    /** @var \app\models\reference\Project $model */
                    $ids = $model->getProjectRegionIds();
                    $str = [];
                    foreach ($ids as $id) {
                        $str[] = Region::getNameById($id);
                    }
                    return join(", ", $str);
                }
            ],
            'scheduled_daily_time'  => [
                'label' => 'Расписание',
                'attribute' => 'scheduled_daily_time',
                'value' => function($model) {
                    $days = str_replace(['1','2','3','4','5','6','7'],['пн','вт','ср','чт','пт','сб','вс'],$model->scheduled_weekdays);
                    return $model->scheduled_daily_time ? substr($model->scheduled_daily_time,0,-3 ) . ' ' . $days : 'Нет';
                }
            ],
            'is_logging',
            'is_auto_export',
            'last_export_at' => [
                'label'     => 'Последний экспорт',
                'attribute' => 'last_export_at',
                'value'     => function($model) {
                    /** @var Project $model */
                    if (!$model->last_export_at) {
                        return null;
                    }
                    $date = $model->last_export_at;
                    if (!$date) return null;
                    if ($date->format("Ymd") == date('Ymd')) {
                        return "Сегодня в ".$date->format("H:i");
                    }
                    return $date;
                }
            ],
            'last_export_count',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getSort($config = [])
    {
        $sort = parent::getSort(array_merge(['attributes' => [
            'scheduled_daily_time' => [
                'asc'       => ['scheduled_daily_time' => SORT_ASC],
                'desc'      => ['scheduled_daily_time' => SORT_DESC],
                'label'     => 'Расписание',
                'default'   => SORT_ASC
            ],
        ]],$config));
        return $sort;
    }
    /**
     * @inheritdoc
     */
    public function search($params = []) {
        if (isset($params['Project']['scheduled_daily_time'])) {
            $time = $params['Project']['scheduled_daily_time'];
            if (!preg_match("/\d\d:\d\d/", $time) || strtotime($params['Project']['scheduled_daily_time']) === FALSE) {
                $params['Project']['scheduled_weekdays'] =  strtr(strtolower($time), [
                    'пн' => 1,
                    'вт' => 2,
                    'ср' => 3,
                    'чт' => 4,
                    'пт' => 5,
                    'сб' => 6,
                    'вс' => 7,
                ]);
                unset($params['Project']['scheduled_daily_time']);
            }
        }
        $query = parent::search($params);
        return $query;
    }
    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations() {
        return [
           // 'regions',
            'projectRegions',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function relations() {
        return array_merge(parent::relations(),[
            'regions',
            'projectRegions',
            'priceExportMode',
            'priceFormerType',
            'competitionMode',
            'projectItems',
            'projectCompetitors',
            'competitors',
            'projectSources',
            'sources',
            'items',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function recycle()
    {
        // $this->setupSources([]);

        // $this->setupCompetitors([]);

        $this->clearProjectItems();

        ProjectExecution::updateAll([
            'status_id' => Status::STATUS_REMOVED
        ],[
            'project_id' => $this->id
        ]);

        return parent::recycle();
    }

    /**
     * Возвращает исключенных конкурентов
     * @param string $brandId
     * @param array $categoriesIds
     * @param $itemId
     * @return array|mixed
     */
    public function excludedCompetitors($brandId, $categoriesIds, $itemId) {
        $result = array_merge(
            $this->excludedCompetitorsByBrand($brandId),
            $this->excludedCompetitorsByCategories($categoriesIds),
            $this->excludedCompetitorsByItem($itemId)
        );
        if (empty($result) || !is_array($result) || count($result) == 0) {
            return null;
        }
        return array_values($result);
    }

    /**
     * Возвращает исключенных конкурентов на основе бренда
     * @param string $_brandId
     * @return array|mixed
     */
    public function excludedCompetitorsByBrand($_brandId) {
        if (!$_brandId) {
            return [];
        }
        if ($this->_excludedBrands === false) {
            $this->_exclusiveBrands = [];
            $this->_excludedBrands  = [];

            $projectCompetitorBrands = ProjectCompetitorBrand::find()
                ->andWhere([
                    'project_id' => $this->id
                ])
                ->asArray()
                ->all();
            foreach ($projectCompetitorBrands as $brand) {
                $brandId        = $brand['brand_id'];
                $competitorId   = $brand['competitor_id'];
                $statusId       = $brand['status_id'];

                if ($statusId == Status::STATUS_REMOVED || !$competitorId || !$brandId) {
                    continue;
                }

                if ($statusId == Status::STATUS_ACTIVE) {
                    if (!isset($this->_exclusiveBrands[$competitorId]))     {
                        $this->_exclusiveBrands[$competitorId] = [];
                    }
                    $this->_exclusiveBrands[$competitorId][] = $brandId;
                }
                else if ($statusId == Status::STATUS_DISABLED) {
                    if (!isset($this->_excludedBrands[$brandId]))     {
                        $this->_excludedBrands[$brandId] = [];
                    }
                    $this->_excludedBrands[$brandId][] = $competitorId;
                }
            }
        }

        $excluded = [];

        if (isset($this->_excludedBrands[$_brandId]) && count($this->_excludedBrands[$_brandId]) > 0) {
            foreach ($this->_excludedBrands[$_brandId] as $competitorId) {
                $excluded[$competitorId] = $competitorId;
            }
        }

        if (count($this->_exclusiveBrands) > 0)  {
            foreach ($this->_exclusiveBrands as $competitorId => $exclusive) {
                if (is_array($exclusive) && count($exclusive) > 0 && !in_array($_brandId, $exclusive) && $competitorId) {
                    $excluded[$competitorId] = $competitorId;
                }
            }
        }

        if (count($excluded) == 0) {
            return [];
        }

        return $excluded;
    }


    /**
     * Возвращает исключенных конкурентов на основе категорий
     * @param array $_categoriesIds
     * @return array|mixed
     */
    public function excludedCompetitorsByCategories($_categoriesIds) {
        if (!$_categoriesIds) {
            return [];
        }
        if (!is_array($_categoriesIds)) {
            $_categoriesIds = [$_categoriesIds];
        }
        if ($this->_excludedCategories === false) {
            $this->_excludedCategories      = [];
            $this->_exclusiveCategories     = [];

            $projectCompetitorCategories = ProjectCompetitorCategory::find()
                ->andWhere([
                    'project_id' => $this->id
                ])
                ->asArray()
                ->all();

            foreach ($projectCompetitorCategories as $category) {
                $categoryId     = $category['category_id'];
                $competitorId   = $category['competitor_id'];
                $statusId       = $category['status_id'];

                if ($statusId == Status::STATUS_REMOVED || !$competitorId || !$categoryId) {
                    continue;
                }

                if ($statusId == Status::STATUS_ACTIVE) {
                    if (!isset($this->_exclusiveCategories[$competitorId]))     {
                        $this->_exclusiveCategories[$competitorId] = [];
                    }
                    $this->_exclusiveCategories[$competitorId][] = $categoryId;
                }
                else if ($statusId == Status::STATUS_DISABLED) {
                    if (!isset($this->_excludedCategories[$categoryId]))     {
                        $this->_excludedCategories[$categoryId] = [];
                    }
                    $this->_excludedCategories[$categoryId][] = $competitorId;
                }
            }
        }

        $excluded   = [];
        $getBack    = [];
        if ($_categoriesIds) {
            foreach ($_categoriesIds as $_categoriesId) {
                if (isset($this->_excludedCategories[$_categoriesId]) && count($this->_excludedCategories[$_categoriesId]) > 0) {
                    foreach ($this->_excludedCategories[$_categoriesId] as $competitorId) {
                        $excluded[$competitorId] = $competitorId;
                    }
                }
                if (count($this->_exclusiveCategories) > 0) {
                    foreach ($this->_exclusiveCategories as $competitorId => $exclusive) {
                        if (is_array($exclusive) && count($exclusive) > 0 && $competitorId) {
                            if (!in_array($_categoriesId, $exclusive) ) {
                                $excluded[$competitorId] = $competitorId;
                            } else {
                                $getBack[$competitorId] = $competitorId;
                            }
                        }
                    }
                }
            }
            if (count($getBack) > 0) {
                foreach ($getBack as $competitorId) {
                    unset($excluded[$competitorId]);
                }
            }
        }

        if (count($excluded) == 0) {
            return [];
        }

        return $excluded;
    }

    /**
     * Возвращает конкурентов, в которых товар исключен из расчета
     * @param $itemId
     * @return array
     */
    public function excludedCompetitorsByItem($itemId)
    {
        if ($this->_excludedItems === false) {
            $this->_excludedItems = [];

            $items = ProjectCompetitorItem::find()
                ->select([
                    'competitor_id',
                    'item_id',
                ])
                ->andWhere([
                    'project_id' => $this->id,
                ])
                ->asArray()
                ->all();
            foreach ($items as $itemData) {
                if (!isset($this->_excludedItems[$itemData['competitor_id']])) {
                    $this->_excludedItems[$itemData['competitor_id']] = [];
                }
                $this->_excludedItems[$itemData['competitor_id']][] = $itemData['item_id'];
            }
        }

        $excluded = [];
        if ($itemId) {
            foreach ($this->_excludedItems as $competitorId => $itemsIds) {
                if (in_array($itemId, $itemsIds)) {
                    $excluded[] = $competitorId;
                }
            }
        }
        return $excluded;
    }

    /**
     * Присудствуетторговую площадку
     * @param int $sourceId
     * @return bool
     */
    public function hasSource($sourceId) {
        $sourceIds = $this->getProjectSourceIds();
        if (isset($sourceIds[$sourceId])) {
            return true;
        }
        return false;
    }

    /**
     * Получить типы цены прайсформера
     * @return array
     */
    public function getPriceFormerTypeIds() {
        if ($this->isNewRecord) {
            return [];
        }
        if ($this->_projectPriceFormerTypeIds === false) {
            $this->_projectPriceFormerTypeIds = ProjectPriceFormerType::find()->alias('ppft')->innerJoin(['pft' => PriceFormerType::tableName()],'ppft.price_former_type_id = pft.id')->andWhere([
                'ppft.project_id'    => $this->id,
                'pft.status_id'      => Status::STATUS_ACTIVE
            ])->select('ppft.price_former_type_id')->column();
        }
        return $this->_projectPriceFormerTypeIds;
    }

    /**
     * Получить торговые площадки проекта
     * @return array
     */
    public function getProjectSourceIds() {
        if ($this->isNewRecord) {
            return [];
        }
        if ($this->_projectSourceIds === false) {
            $this->_projectSourceIds = $this->getProjectSources()->select('source_id')->column();
        }
        if (!$this->_projectSourceIds) {
            return [];
        }
        return $this->_projectSourceIds;
    }


    /**
     * Получить торговые площадки проекта
     * @return array
     */
    public function getProjectRegionIds() {
        if ($this->isNewRecord) {
            return [];
        }
        if ($this->_projectRegionIds === false) {
            $this->_projectRegionIds = yii\helpers\ArrayHelper::getColumn($this->projectRegions,'region_id');
            //$this->_projectRegionIds = $this->getProjectRegions()->select('region_id')->column();
        }
        if (!$this->_projectRegionIds) {
            return [];
        }
        return $this->_projectRegionIds;
    }

    /**
     * Айдишники ключевых конкурентов
     * @return null|ProjectCompetitor[]
     */
    public function projectKeyCompetitorsIds() {
        if ($this->_projectKeyCompetitorsIds === false) {
            $this->_projectKeyCompetitorsIds = $this->getProjectCompetitors()
                ->andWhere([
                    'is_key_competitor' => true
                ])
                ->select('competitor_id')
                ->column();
        }
        return $this->_projectKeyCompetitorsIds;
    }

    /**
     * Айдишники конкурентов
     * @return null|ProjectCompetitor[]
     */
    public function projectCompetitorsIds() {
        if ($this->_projectCompetitorsIds === false) {
            $this->_projectCompetitorsIds = $this->getProjectCompetitors()
                ->select('competitor_id')
                ->column();
        }
        return $this->_projectCompetitorsIds;
    }

    /**
     * Финальные модификаторы цены https://glpi.vseinstrumenti.ru/front/ticket.form.php?id=137700
     * @return null|array
     */
    public function projectCompetitorFinalModifiers() {
        if ($this->_projectCompetitorFinalModifiers === false) {
            $this->_projectCompetitorFinalModifiers = $this->getProjectCompetitors()
                ->andWhere(['status_id' => Status::STATUS_ACTIVE])
                ->andWhere(['not',
                    ['price_final_modifier' => null]
                ])
                ->indexBy('competitor_id')
                ->select('price_final_modifier')
                ->column();
        }
        return $this->_projectCompetitorFinalModifiers;
    }

    /**
     * Айдишники конкурентов которые не стоит учитывать при расчете
     * @return null|ProjectCompetitor[]
     */
    public function inactiveProjectCompetitorsIds() {
        if ($this->_inactiveProjectCompetitorsIds === false) {
            $this->_inactiveProjectCompetitorsIds = $this->getProjectCompetitors()
                ->andWhere(['not', ['status_id' => Status::STATUS_ACTIVE]])
                ->select('competitor_id')
                ->column();
        }
        return $this->_inactiveProjectCompetitorsIds;
    }

    /**
     * Пороговые конкуренты
     * @return null|ProjectCompetitor[]
     */
    public function projectVariationCompetitors() {
        if ($this->_projectVariationCompetitors === false) {
            $this->_projectVariationCompetitors = $this->getProjectCompetitors()
                ->andWhere(['status_id' => Status::STATUS_ACTIVE])
                ->andWhere(['not',
                    ['price_variation_modifier' => null]
                ])
                ->select('price_variation_modifier')
                ->indexBy('competitor_id')
                ->column();
        }
        return $this->_projectVariationCompetitors;
    }

    /**
     * Идентификаторы торговых площадок
     * @return array|bool
     */
    public function projectMarketplaceCompetitorsIds()
    {
        if ($this->_projectMarketplaceCompetitorsIds === false) {
            $this->_projectMarketplaceCompetitorsIds = $this->getProjectCompetitors()
                ->alias('pc')
                ->leftJoin(['c' => Competitor::tableName()], 'c.id = pc.competitor_id')
                ->andWhere([
                    'pc.status_id' => Status::STATUS_ACTIVE,
                    'c.is_marketplace' => true,
                ])
                ->select('competitor_id')
                ->indexBy('competitor_id')
                ->column();
        }
        return $this->_projectMarketplaceCompetitorsIds;
    }


    /**
     * Получить конкурента проекта
     * @return null|ProjectCompetitor[]
     */
    public function projectCompetitors() {
        if ($this->_projectCompetitors === false) {
            $this->_projectCompetitors          = $this->getProjectCompetitors()
                ->alias('pc')
                ->addSelect(['*', 'c.is_marketplace'])
                ->leftJoin(['c' => Competitor::tableName()], 'c.id = pc.competitor_id')
                ->indexBy('competitor_id')
                ->asArray()
                ->all();
            $this->_projectCompetitorsIds       = [];
            $this->_projectKeyCompetitorsIds    = [];
            $this->_inactiveProjectCompetitorsIds = [];
            $this->_projectVariationCompetitors = [];
            $this->_projectCompetitorFinalModifiers = [];
            foreach ($this->_projectCompetitors  as $competitorId => $competitor) {
                if ($competitor['status_id'] !== Status::STATUS_ACTIVE) {
                    $this->_inactiveProjectCompetitorsIds[] = $competitor['competitor_id'];
                }
                else{
                    if ($competitor['price_variation_modifier'] !== null) {
                        $this->_projectVariationCompetitors[$competitor['competitor_id']] = $competitor['price_variation_modifier'];
                    }
                    if ($competitor['price_final_modifier'] !== null) {
                        $this->_projectCompetitorFinalModifiers[$competitor['competitor_id']] = $competitor['price_final_modifier'];
                    }
                    if ($competitor['is_key_competitor']) {
                        $this->_projectKeyCompetitorsIds[] = $competitor['competitor_id'];
                    }
                    if ($competitor['is_marketplace']) {
                        $this->_projectMarketplaceCompetitorsIds[] = $competitor['competitor_id'];
                    }
                }
                $this->_projectCompetitorsIds[] = $competitor['competitor_id'];
            }
        }
        return $this->_projectCompetitors;
    }
    
    /**
     * Получить конкурента проекта
     * @param $competitorId
     * @return null|ProjectCompetitor
     */
    public function projectCompetitor($competitorId) {
        if ($this->isNewRecord) {
            return null;
        }

        $this->projectCompetitors();

        if ($this->_projectCompetitors) {
            if (isset($this->_projectCompetitors[$competitorId])) {
                return $this->_projectCompetitors[$competitorId];
            }
        }
        return null;
    }

    public function clearProjectItems() {
        ProjectItem::deleteAll([
            'project_id' => $this->id
        ]);
        $this->_groupedProjectItems = false;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->_projectCompetitors = false;
        $this->_projectCompetitorsIds = false;
        $this->_projectKeyCompetitorsIds = false;
        $this->_projectSourceIds = false;
        $this->_groupedProjectItems = false;
    }

    /**
     * @return ActiveQuery
     */
    public function findRefinedPriceQuery() {
        $rr = $this->getProjectRegionIds();
        $findRefinedPriceQuery = PriceRefined::find()
            ->alias('t')
            ->andWhere([
                't.source_id'     => $this->getProjectSourceIds(),
            ])
            ->leftJoin(['c' => Competitor::tableName()], 'c.id = t.competitor_id')
        ;

        if (empty($rr) === 0) {
            $findRefinedPriceQuery->andWhere(new Expression("t.regions @> '1'"));
        }
        if (count($rr) === 1) {
            $findRefinedPriceQuery->andWhere(new Expression("t.regions @> '{$rr[0]}'"));

        } else if (count($rr) === 2) {
            $findRefinedPriceQuery->andWhere(['or', new Expression("t.regions @> '{$rr[0]}'"), new Expression("regions @> '{$rr[1]}'")]);
        } else {
            $jsonRegArr = implode("','", $rr);
            $jsonRegArr = "'$jsonRegArr'";
            $findRefinedPriceQuery->andWhere(new Expression("t.regions @> ANY (ARRAY [$jsonRegArr]::jsonb[])"));
        }
        if ($this->price_relevance_time_span) {
            $findRefinedPriceQuery->andWhere('
                CASE WHEN c.price_lifetime > 0
                    THEN t.extracted_at > ((NOW()) - make_interval(secs := c.price_lifetime))
                    ELSE t.extracted_at > \'' . date('Y-m-d H:i:s', strtotime('-' . $this->price_relevance_time_span . ' seconds')) . '\'
                END
            ');
        } else {
            $findRefinedPriceQuery->andWhere(['>', 't.extracted_at', new Expression('((NOW()) - make_interval(secs := c.price_lifetime))')]);
        }
        return $findRefinedPriceQuery;
    }

    public function prepareProjectExecution($tryToStartNow = true) {
        $projectExecution = new ProjectExecution;
        $projectExecution->createProjectSnapshot($this);
        $projectExecution->project_id = $this->id;
        $projectExecution->project_execution_status_id = ProjectExecutionStatus::STATUS_QUEUED;
        $projectExecution->save();
        
        $task = new Task;
        $task->name                 = $projectExecution->name;
        $task->requester_id         = $projectExecution->id;
        $task->requester_entity_id  = Entity::ProjectExecution;
        $task->task_function        = 'prepareProjectExecution';
        $task->task_type_id         = TaskType::TYPE_PROJECT_EXECUTION_PREPARE;
        $task->enqueue($tryToStartNow);

//        $projectExecution->execute($tryToStartNow);

        $this->project_execution_status_id = ProjectExecutionStatus::STATUS_QUEUED;
        $this->save();
    }

    /**
     * Автоматически заполнить номенклатуру проекта на основе конкурентов
     * @param Task $task
     * @return string
     */
    public function taskProjectAutoFill(Task $task = null)
    {
        $this->project_execution_status_id = ProjectExecutionStatus::STATUS_QUEUED;
        $this->save();

        $task->task_status_id   = TaskStatus::STATUS_RUNNING;
        $task->started_at       = new DateTime();
        $task->save();

        $projectCompetitors     = $this->projectCompetitors();
        $projectCompetitorsIds  = array_keys($projectCompetitors);

        $count = 0;
        $errors = 0;

        foreach ($projectCompetitors as $projectCompetitor) {
            try {
                // Ищем товары конкурента
                $find = Item::find()
                    ->alias('i')
                    ->innerJoin(['ci' => CompetitorItem::tableName()], 'i.id = ci.item_id')
                    ->andWhere([
                        'ci.competitor_id'  => $projectCompetitorsIds,
                        'ci.status_id'      => Status::STATUS_ACTIVE,
                        'i.status_id'       => Status::STATUS_ACTIVE,
                    ]);

                // Фильтр по брендам
                $brandIds = array_keys($projectCompetitor->brands());
                if (count($brandIds) > 0) {
                    if ($projectCompetitor->brandsBanned()) {
                        $find->andWhere(['not', [
                            'i.brand_id' => $brandIds
                        ]]);
                    } else {
                        $find->andWhere([
                            'i.brand_id' => $brandIds
                        ]);
                    }
                }

                // Фильтр по категориям
                $categoryIds = array_keys($projectCompetitor->categories());
                if (count($categoryIds) > 0) {

                    $itemsIdsQuery = CategoryItem::find()
                        ->andWhere([
                            'category_id' => $categoryIds
                        ])
                        ->select('item_id');

                    if ($projectCompetitor->categoriesBanned()) {
                        $find->andWhere(['not', [
                            'i.id' => $itemsIdsQuery
                        ]]);
                    } else {
                        $find->andWhere([
                            'i.id' => $itemsIdsQuery
                        ]);
                    }
                }

                $itemIds = $find->groupBy(['i.id'])->select(['id' => 'i.id'])->column();

                $task->total = $task->total + count($itemIds);
                $task->save();

                try {
                    foreach ($itemIds as $itemId) {
                        $count++;
                        $projectItem = ProjectItem::find()
                            ->andWhere([
                                'project_id'    => $this->id,
                                'item_id'       => $itemId,
                            ])
                            ->limit(1)
                            ->one();
                        if (!$projectItem) {
                            $projectItem = new ProjectItem;
                        }
                        $projectItem->item_id           = $itemId;
                        $projectItem->project_id        = $this->id;
                        $projectItem->min_margin        = $projectCompetitor->min_margin;
                        $projectItem->status_id         = Status::STATUS_ACTIVE;
                        $projectItem->select_price_logic_id = SelectPriceLogic::LOGIC_A;
                        $projectItem->save();

                        if (($count) % 500) {
                            $task->progress = $count;
                            $task->errors   = $errors;
                            $task->had_errors = ($errors > 0);
                            $task->save();
                        }
                    }
                } catch (\Exception $e) {
                    $errors++;
                    Error::logError($e, ErrorType::TYPE_TASK, Entity::Task, $task->id);
                }
            } catch (\Exception $e) {
                $errors++;
                Error::logError($e, ErrorType::TYPE_TASK, Entity::Task, $task->id);
            }

            $task->progress = $count;
            $task->errors = $errors;
            $task->had_errors = ($errors > 0);
            $task->save();
        }

        $task->task_status_id   = TaskStatus::STATUS_FINISHED;
        $task->finished_at      = new DateTime();
        $task->had_errors = ($errors > 0);
        $task->save();

        $this->project_execution_status_id = ProjectExecutionStatus::STATUS_READY;
        $this->save();
    }

    /**
     * Обновить YM URL у товаров которые используются в этом проекте
     * @param Task $task
     */
    public function taskItemUpdateUrls(Task $task) {
        Item::taskItemUpdateUrls($task, $this->id);
    }

    /**
     * расчет KPI
     * @param Task $task
     */
    public function taskReportKpi(Task $task) {
        $task->task_status_id   = TaskStatus::STATUS_RUNNING;
        $task->started_at       = new DateTime();
        $task->save();

        if ($this->lastProjectExecution) {

            $reportProject = new ReportKpiProject([
                'project' => $this,
                'projectExecution' => $this->lastProjectExecution
            ]);

            $competitors = $this->competitors;

            $task->total = count($competitors);
            $task->save();

            $reportProject->calculate();

            foreach ($competitors as $competitor) {
                $rk = ReportKpi::find()
                    ->andWhere([
                        'project_execution_id' => $this->lastProjectExecution->id,
                        'competitor_id' => $competitor->id
                    ])
                    ->limit(1)
                    ->one();

                if (!$rk) {
                    $rk = new ReportKpi();
                    $rk->competitor_id          = $competitor->id;
                    $rk->project_execution_id   = $this->lastProjectExecution->id;
                    $rk->project_id             = $this->id;
                }

                $reportProject->populate($rk, $competitor);
                $rk->save(false);
                $task->progress++;
                $task->save();
            }

            if ($this->lastProjectExecution->created_at->getTimestamp() > strtotime('today')) {
                $projectChart = ProjectChart::findOne([
                    'project_id' => $this->id,
                    'date' => date(DateTime::DB_DATE_FORMAT . ' 00:00:00'),
                    'type' => ProjectChart::TYPE_VI_COMPARE
                ]);

                if (!$projectChart) {
                    $projectChart = new ProjectChart();
                    $projectChart->date = date(DateTime::DB_DATE_FORMAT . ' 00:00:00');
                    $projectChart->type = ProjectChart::TYPE_VI_COMPARE;
                }
                $projectChart->project_id = $this->id;
                $projectChart->project_execution_id = $this->lastProjectExecution->id;
                $resultData = [];
                foreach ($this->projectCompetitors as $projectCompetitor) {
                    $data = LogPriceCalculation::find()
                        ->alias('t')
                        ->select([
                            '> 5%' => 'json_agg(
                                CASE WHEN
                                    t.price_refined > t.price_calculated
                                    AND (((t.price_refined/t.price_calculated) * 100 - 100) > 5)
                                THEN item_id ELSE null END
                            )',
                            '+1% - +5%' => 'json_agg(
                                CASE WHEN
                                    t.price_refined > t.price_calculated
                                    AND 1 < ((t.price_refined/t.price_calculated) * 100 - 100)
                                    AND ((t.price_refined/t.price_calculated) * 100 - 100) < 5
                                THEN item_id ELSE null END
                            )',
                            '+-1%' => 'json_agg(
                                CASE WHEN
                                    -1 < ((t.price_refined/t.price_calculated) * 100 - 100)
                                    AND ((t.price_refined/t.price_calculated) * 100 - 100) < 1
                                THEN item_id ELSE null END
                            )',
                            '-1% - -5%' => 'json_agg(
                                CASE WHEN
                                    t.price_refined < t.price_calculated
                                    AND -1 > ((t.price_refined/t.price_calculated) * 100 - 100)
                                    AND ((t.price_refined/t.price_calculated) * 100 - 100) > -5
                                THEN item_id ELSE null END
                            )',
                            '< -5%' => 'json_agg(
                                CASE WHEN
                                    t.price_refined < t.price_calculated
                                    AND (((t.price_refined/t.price_calculated) * 100 - 100) < -5)
                                THEN item_id ELSE null END
                            )',
                        ])
                        ->andWhere([
                            't.project_execution_id' => $this->lastProjectExecution->id,
                            't.competitor_id' => $projectCompetitor->competitor_id,
                            't.status_id' => Status::STATUS_ACTIVE,
                        ])
                        ->asArray()
                        ->one()
                    ;
                    if (!isset($resultData[$projectCompetitor->competitor_id])) {
                        $resultData[$projectCompetitor->competitor_id] = [[],[],[],[],[]];
                    }
                    $resultData[$projectCompetitor->competitor_id][0] = array_filter(json_decode(!is_null($data['> 5%']) ? $data['> 5%'] : '[]', true));
                    $resultData[$projectCompetitor->competitor_id][1] = array_filter(json_decode(!is_null($data['+1% - +5%']) ? $data['+1% - +5%'] : '[]', true));
                    $resultData[$projectCompetitor->competitor_id][2] = array_filter(json_decode(!is_null($data['+-1%']) ? $data['+-1%'] : '[]', true));
                    $resultData[$projectCompetitor->competitor_id][3] = array_filter(json_decode(!is_null($data['-1% - -5%']) ? $data['-1% - -5%'] : '[]', true));
                    $resultData[$projectCompetitor->competitor_id][4] = array_filter(json_decode(!is_null($data['< -5%']) ? $data['< -5%'] : '[]', true));
                }
                $projectChart->data = json_encode($resultData);
                $projectChart->save();

                $yesterdayProjectExecutionId = ProjectExecution::find()
                    ->select('id')
                    ->andWhere([
                        'AND',
                        ['project_id' => $this->id],
                        ['!=', 'id', $this->lastProjectExecution->id]
                    ])
                    ->orderBy('created_at DESC')
                    ->scalar();
                if ($yesterdayProjectExecutionId) {
                    $projectChart = ProjectChart::findOne([
                        'project_id' => $this->id,
                        'date' => date(DateTime::DB_DATE_FORMAT . ' 00:00:00'),
                        'type' => ProjectChart::TYPE_PRICE_DYNAMICS
                    ]);

                    if (!$projectChart) {
                        $projectChart = new ProjectChart();
                        $projectChart->date = date(DateTime::DB_DATE_FORMAT . ' 00:00:00');
                        $projectChart->type = ProjectChart::TYPE_PRICE_DYNAMICS;
                    }
                    $projectChart->project_id = $this->id;
                    $projectChart->project_execution_id = $this->lastProjectExecution->id;
                    $resultData = [];

                    foreach ($this->projectCompetitors as $projectCompetitor) {
                        $data = (new Query())
                            ->from(['data' => (new Query())
                                ->from(['jsont' => LogPriceCalculation::find()
                                    ->alias('t')
                                    ->select([
                                        'json' => 'json_agg(price_refined ORDER BY created_at DESC)',
                                        'item_id'
                                    ])
                                    ->andWhere([
                                        't.project_execution_id' => [$this->lastProjectExecution->id, $yesterdayProjectExecutionId],
                                        't.competitor_id' => $projectCompetitor->competitor_id,
                                        't.status_id' => Status::STATUS_ACTIVE,
                                    ])
                                    ->groupBy('item_id, competitor_id')
                                    ->having('count(item_id) > 1')
                                    ->asArray()
                                ])
                                ->select([
                                    'down' => '(CASE WHEN ((json->>0)::float != 0 AND (json->>1)::float != 0
                                             AND (json->>0)::float < (json->>1)::float
                                             AND ((((json->>0)::float/(json->>1)::float) * 100 - 100) < -1)
                                            ) THEN item_id ELSE null END)',
                                    'up' => '(CASE WHEN ((json->>0)::float != 0 AND (json->>1)::float != 0
                                             AND (json->>0)::float > (json->>1)::float
                                             AND ((((json->>0)::float/(json->>1)::float) * 100 - 100) > 1)
                                            ) THEN item_id ELSE null END)',
                                    'near' => '(CASE WHEN ((json->>0)::float != 0 AND (json->>1)::float != 0
                                         AND (json->>0)::float != (json->>1)::float
                                         AND ((((json->>0)::float/(json->>1)::float) * 100 - 100) >= -1)
                                         AND ((((json->>0)::float/(json->>1)::float) * 100 - 100) <= 1)
                                         ) THEN item_id ELSE null END)',
                                    'no' => '(CASE WHEN (
                                                (json->>0)::float != 0 AND (json->>1)::float != 0
                                             AND ((((json->>0)::float/(json->>1)::float) * 100 - 100) > -1)
                                             AND ((((json->>0)::float/(json->>1)::float) * 100 - 100) < 1)
                                            ) THEN item_id ELSE null END)'
                                ])
                            ])
                            ->select([
                                'down' => 'COALESCE(json_agg(down) FILTER (WHERE down IS NOT NULL), \'[]\')',
                                'up'   => 'COALESCE(json_agg(up) FILTER (WHERE up IS NOT NULL), \'[]\')',
                                'no'   => 'COALESCE(json_agg(no) FILTER (WHERE no IS NOT NULL), \'[]\')',
                                'near' => 'COALESCE(json_agg(near) FILTER (WHERE near IS NOT NULL), \'[]\')',
                            ])
                            ->one();
                        if (!isset($resultData[$projectCompetitor->competitor_id])) {
                            $resultData[$projectCompetitor->competitor_id] = [[],[],[]];
                        }
                        $resultData[$projectCompetitor->competitor_id][0] = json_decode($data['up'], true);
                        $resultData[$projectCompetitor->competitor_id][1] = json_decode($data['down'], true);
                        $resultData[$projectCompetitor->competitor_id][2] = json_decode($data['no'], true);
                        $resultData[$projectCompetitor->competitor_id][3] = json_decode($data['near'], true);
                    }

                    $projectChart->data = json_encode($resultData);
                    $projectChart->save();
                }
            }
        }


        $task->task_status_id   = TaskStatus::STATUS_FINISHED;
        $task->finished_at      = new DateTime();
        $task->had_errors       = 0;
        $task->save();
    }

    /**
     * @param array $config Конфиг
     * @throws InvalidValueException
     */
    public function setupRegions($config) {

        ProjectRegion::deleteAll([
            'project_id' => $this->id
        ]);

        if (is_array($config)) {
            foreach ($config as $regionId) {
                $projectRegion = new ProjectRegion();
                $projectRegion->project_id = $this->id;
                $projectRegion->region_id = $regionId;
                $projectRegion->save();
            }
        }

        $this->_projectRegionIds = false;
    }
    /**
     * @param array $config Конфиг
     * @throws InvalidValueException
     */
    public function setupProjectPriceFormerTypes($config) {

        ProjectPriceFormerType::deleteAll([
            'project_id' => $this->id
        ]);

        if (is_array($config)) {
            foreach ($config as $priceFormerTypeId) {
                $projectSource = new ProjectPriceFormerType();
                $projectSource->project_id = $this->id;
                $projectSource->price_former_type_id = $priceFormerTypeId;
                $projectSource->save();
            }
        }

        $this->_projectPriceFormerTypeIds = false;
    }

    /**
     * @param array $config Конфиг
     * @throws InvalidValueException
     */
    public function setupSources($config) {

        ProjectSource::deleteAll([
            'project_id' => $this->id
        ]);

        if (is_array($config)) {
            foreach ($config as $sourceId) {
                $projectSource = new ProjectSource();
                $projectSource->project_id = $this->id;
                $projectSource->source_id = $sourceId;
                $projectSource->save();
            }
        }

        $this->_projectSourceIds = false;
    }

    
    /**
     * @param array $config Конфиг
     * @throws InvalidValueException
     */
    public function setupCompetitors($config) {
        if ($this->isNewRecord) {
            $this->save();
        }

        $trx = self::getDb()->beginTransaction();

        try {
            ProjectCompetitorCategory::deleteAll([
                'project_id' => $this->id
            ]);
            ProjectCompetitorBrand::deleteAll([
                'project_id' => $this->id
            ]);
            ProjectCompetitorItem::deleteAll([
                'project_id' => $this->id
            ]);
            ProjectCompetitor::deleteAll([
                'project_id' => $this->id
            ]);

            if (isset($config['ProjectCompetitors']) && is_array($config['ProjectCompetitors'])) {
                foreach ($config['ProjectCompetitors'] as $competitorId) {
                    if (!isset($config['ProjectCompetitor'][$competitorId])) {
                        $config['ProjectCompetitor'][$competitorId] = [];
                    }

                    $projectCompetitor = ProjectCompetitor::create($this, $competitorId, $config['ProjectCompetitor'][$competitorId]);

                    if (isset($config['ProjectCompetitorBrands'][$competitorId])) {
                        $brands = array_merge([
                            'brandsSelected' => "",
                            'brandsBanned' => false,
                        ], $config['ProjectCompetitorBrands'][$competitorId]);

                        $selected = explode(',', $brands['brandsSelected']);
                        $banned = $brands['brandsBanned'] ? true : false;
                        $brands = [];
                        foreach ($selected as $brandId) {
                            $brands[] = ['brand_id' => $brandId, 'status_id' => $banned ? Status::STATUS_DISABLED : Status::STATUS_ACTIVE];
                        }
                        $projectCompetitor->newBrands = $brands;
                    }

                    if (isset($config['ProjectCompetitorCategories'][$competitorId])) {
                        $categories = array_merge([
                            'categoriesSelected' => "",
                            'categoriesBanned' => false,
                        ], $config['ProjectCompetitorCategories'][$competitorId]);
                        $selected = explode(',', $categories['categoriesSelected']);
                        $banned = $categories['categoriesBanned'] ? true : false;

                        $categories = [];
                        foreach ($selected as $categoryId) {
                            $categories[] = ['category_id' => $categoryId, 'status_id' => $banned ? Status::STATUS_DISABLED : Status::STATUS_ACTIVE];
                        }
                        $projectCompetitor->newCategories = $categories;
                    }

                    if (isset($config['ProjectCompetitorItems'][$competitorId])) {
                        $items = explode(',', $config['ProjectCompetitorItems'][$competitorId]['itemsSelected']);
                        $projectCompetitor->newExcludedItems = $items;
                    }
                }
            }

            $this->_projectCompetitors = false;
            $this->_projectCompetitorsIds = false;
            $this->_projectKeyCompetitorsIds = false;

            $trx->commit();

        } catch (\Exception $exception ) {

            $trx->rollBack();
        }
    }

    
    public function setupGroupedProjectItemsParams($params) {

        if (isset($params['GroupedProjectItem']) && is_array($params['GroupedProjectItem'])) {
            foreach ($params['GroupedProjectItem'] as $brandId => $category) {
                foreach ($category as $categoryId => $data) {
                    $update = ['rrp_regulations' => false];
                    if (isset($data['rrp_regulations']) && intval($data['rrp_regulations']) > 0) {
                        $update['rrp_regulations'] = true;
                    }
                    if (count($update) > 0) {
                        $ids =  ProjectItem::find()
                            ->alias('pi')
                            ->innerJoin(['i' => Item::tableName()], 'i.id = pi.item_id')
                            ->innerJoin(['ci' => CategoryItem::tableName()], 'i.id = ci.item_id')
                            ->innerJoin(['c' => Category::tableName()], 'c.id = ci.category_id AND c.is_top = TRUE AND c.status_id ='.Status::STATUS_ACTIVE)
                            ->innerJoin(['b' => Brand::tableName()], 'b.id = i.brand_id')
                            ->andWhere([
                                'project_id' => $this->id,
                                'i.brand_id' => $brandId,
                                'ci.category_id' => $categoryId,
                            ])
                            ->select('pi.id')
                            ->column();
                        ProjectItem::updateAll($update, [
                            'id' => $ids
                        ]);
                    }
                }
            }
        }
    }

    public function groupedProjectItems() {
        if ($this->_groupedProjectItems === false) {
            $this->_groupedProjectItems = [];
            $rows = (new Query())->from(
                [
                    'g' => $this->nomenclature_document_id
                        ? NomenclatureDocumentItem::find()->alias('ndi')
                            ->innerJoin(['i' => Item::tableName()], 'i.id = ndi.item_id')
                            ->innerJoin(['ci' => CategoryItem::tableName()], 'ci.item_id = ndi.item_id AND ci.is_top = TRUE AND ci.status_id ='.Status::STATUS_ACTIVE)
                            ->andWhere([
                                'ndi.nomenclature_document_id' => $this->nomenclature_document_id,
                            ])
                            ->groupBy([
                                'i.brand_id',
                                'ci.category_id',
                                'ndi.rrp_regulations',
                            ])
                            ->select([
                                'brand_id'              => 'i.brand_id',
                                'category_id'           => 'ci.category_id',
                                'count'                 => 'count(ndi.id)',
                                'rrp_regulations'       => 'ndi.rrp_regulations',
                            ])
                        : ProjectItem::find()->alias('pi')
                            ->innerJoin(['i' => Item::tableName()], 'i.id = pi.item_id')
                            ->innerJoin(['ci' => CategoryItem::tableName()], 'pi.item_id = ci.item_id AND ci.is_top = TRUE AND ci.status_id ='.Status::STATUS_ACTIVE)
                            ->andWhere(['project_id' => $this->id])
                            ->groupBy([
                                'i.brand_id',
                                'ci.category_id',
                                'pi.rrp_regulations',
                            ])
                            ->select([
                                'brand_id'              => 'i.brand_id',
                                'category_id'           => 'ci.category_id',
                                'count'                 => 'count(pi.id)',
                                'rrp_regulations'       => 'pi.rrp_regulations',
                            ])
                ])
                ->leftJoin(['category' => Category::tableName()], 'category.id = g.category_id')
                ->leftJoin(['brand' => Brand::tableName()], 'brand.id = g.brand_id')
                ->select([
                    'brand_id'          => 'g.brand_id',
                    'category_id'       => 'g.category_id',
                    'brand_name'        => 'brand.name',
                    'category_name'     => 'category.name',
                    'count'             => 'g.count',
                    'rrp_regulations'   => 'g.rrp_regulations',
                ])
                ->orderBy([
                    'brand.name' => SORT_ASC,
                    'category.name' => SORT_ASC,
                ])
                ->all();

            foreach ($rows as $row) {
                if (!isset($this->_groupedProjectItems[$row['brand_id']])) {
                    $this->_groupedProjectItems[$row['brand_id']] = [
                        'brand_id' => $row['brand_id'],
                        'brand_name' => $row['brand_name'],
                        'categories' => [],
                    ];
                }
                if (!isset($this->_groupedProjectItems[$row['brand_id']]['categories'])) {
                    $this->_groupedProjectItems[$row['brand_id']]['categories'] = [];
                }
                $idx = $row['category_id'];
                if (!$idx) {
                    $idx = '_';
                }
                if (!isset($this->_groupedProjectItems[$row['brand_id']]['categories'][$idx])) {
                    $this->_groupedProjectItems[$row['brand_id']]['categories'][$idx] = $row;
                } else {
                    $this->_groupedProjectItems[$row['brand_id']]['categories'][$idx]['count']              += $row['count'];
                    if ($this->_groupedProjectItems[$row['brand_id']]['categories'][$idx]['rrp_regulations'] !== null) {
                        if ($this->_groupedProjectItems[$row['brand_id']]['categories'][$idx]['rrp_regulations'] !== $row['rrp_regulations']) {
                            $this->_groupedProjectItems[$row['brand_id']]['categories'][$idx]['rrp_regulations'] = null;
                        }
                    }
                }
            }
        }
        return $this->_groupedProjectItems;
    }

    public static function getWorkingProjects() {
        $cookieName = 'working_projects';
        if(Yii::$app->getRequest()->getCookies()->has($cookieName)){
            $projects = Yii::$app->getRequest()->getCookies()->get($cookieName);
            return Json::decode($projects, true);
        }
        return [];
    }

    public static function addWorkingProject($projectId, $projectName, $hours = 8) {
        $cookieName = 'working_projects';
        $projects = self::getWorkingProjects();
        unset($projects[$projectId]);
        $projects[$projectId] = $projectName;
        $newCookie          = new Cookie();
        $newCookie->name    = $cookieName;
        $newCookie->value   = Json::encode($projects);
        $newCookie->expire  = time() + 60 * 60 * $hours;
        Yii::$app->getResponse()->getCookies()->add($newCookie);
    }


    /**
     * Клонировать
     * @throws yii\db\Exception
     * @return ParsingProject
     */
    public function cloneProject() {


        $attrToClone            = $this->getAttributes();
        unset($attrToClone['updated_at']);
        unset($attrToClone['created_at']);
        unset($attrToClone['created_user_id']);
        unset($attrToClone['updated_user_id']);
        unset($attrToClone['id']);
        unset($attrToClone['index']);

        $clone = new Project();
        $clone->setAttributes($attrToClone);
        $clone->name = $this->name . ' - Копия';
        $clone->save();

        $userId = Yii::$app->user->identity->getId();

        // -- Cross --
        $fields = "project_id";
        $values = "'{$clone->id}'";

        //ProjectSource
        $tableFields = "source_id";
        ProjectSource::getDb()->createCommand("INSERT INTO ".ProjectSource::tableName()." ($fields, $tableFields) SELECT $values, $tableFields FROM ".ProjectSource::tableName()." WHERE project_id = '{$this->id}'")->execute();

        //ProjectPriceFormerType
        $tableFields = "price_former_type_id";
        ProjectPriceFormerType::getDb()->createCommand("INSERT INTO ".ProjectPriceFormerType::tableName()." ($fields, $tableFields) SELECT $values, $tableFields FROM ".ProjectPriceFormerType::tableName()." WHERE project_id = '{$this->id}'")->execute();

        // -- Referneces --
        $values .= ", '{$userId}', '{$userId}', NOW(), NOW()";
        $fields .= ", created_user_id, updated_user_id, created_at, updated_at";

        //ProjectCompetitor
        $tableFields = "competitor_id, select_price_logic_id, status_id, min_margin, is_key_competitor, price_variation_modifier, name";
        ProjectCompetitor::getDb()->createCommand("INSERT INTO ".ProjectCompetitor::tableName()." ($fields, $tableFields) SELECT $values, $tableFields FROM ".ProjectCompetitor::tableName()." WHERE project_id = '{$this->id}'")->execute();

        //ProjectItem
        $tableFields = "select_price_logic_id, status_id, item_id, min_margin, rrp_regulations, name";
        ProjectItem::getDb()->createCommand("INSERT INTO ".ProjectItem::tableName()." ($fields, $tableFields) SELECT $values, $tableFields FROM ".ProjectItem::tableName()." WHERE project_id = '{$this->id}'")->execute();

        foreach ($clone->projectCompetitors as $projectCompetitor) {
            //ProjectCompetitorBrand
            $tableFields = "competitor_id, brand_id, status_id, name";
            ProjectCompetitorBrand::getDb()->createCommand("INSERT INTO ".ProjectCompetitorBrand::tableName()." ($fields, project_competitor_id, $tableFields) SELECT $values, '{$projectCompetitor->id}', $tableFields FROM ".ProjectCompetitorBrand::tableName()." WHERE project_id = '{$this->id}' AND competitor_id = '{$projectCompetitor->competitor_id}'")->execute();

            //ProjectCompetitorCategory
            $tableFields = "competitor_id, category_id, status_id, name";
            ProjectCompetitorCategory::getDb()->createCommand("INSERT INTO ".ProjectCompetitorCategory::tableName()." ($fields, project_competitor_id, $tableFields) SELECT $values, '{$projectCompetitor->id}', $tableFields FROM ".ProjectCompetitorCategory::tableName()." WHERE project_id = '{$this->id}' AND competitor_id = '{$projectCompetitor->competitor_id}'")->execute();
        }

        return $clone;
    }


    /**
     * @return string
     */
    public function getPriceRelevanceTimeSpan() {
        return TimeSpanValidator::integer2timeSpan($this->price_relevance_time_span);
    }

    /**
     * @param $value
     * @return int
     */
    public function setPriceRelevanceTimeSpan($value) {
        $this->price_relevance_time_span = TimeSpanValidator::timeSpan2integer($value);
    }

    /**
     * @return string
     */
    public function getDataLifeTimeSpan() {
        return TimeSpanValidator::integer2timeSpan($this->data_life_time_span);
    }

    /**
     * @param $value
     */
    public function setDataLifeTimeSpan($value) {
       $this->data_life_time_span = TimeSpanValidator::timeSpan2integer($value);
    }

    /**
     * @return array
     */
    public function getScheduledWeekdays() {
        return $this->scheduled_weekdays?explode(',', $this->scheduled_weekdays):[];
    }

    /**
     * @param array $array
     */
    public function setScheduledWeekdays($array) {
        $this->scheduled_weekdays = join(',',$array);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceFormerTypes() {
        return $this->hasMany(PriceFormerType::className(), ['id' => 'price_former_type_id'])->via('projectPriceFormerTypes');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectPriceFormerTypes() {
        return $this->hasMany(ProjectPriceFormerType::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSources() {
        return $this->hasMany(Source::className(), ['id' => 'source_id'])->via('projectSources');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectSources() {
        return $this->hasMany(ProjectSource::className(), ['project_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectItems() {
        if ($this->nomenclature_document_id) {
            return $this->hasMany(NomenclatureDocumentItem::className(),
                ['nomenclature_document_id' => 'nomenclature_document_id']
            );
        }
        return $this->hasMany(ProjectItem::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems() {
        return $this->hasMany(Item::className(), ['id' => 'item_id'])->via('projectItems');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCompetitors() {
        return $this->hasMany(ProjectCompetitor::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitors() {
        return $this->hasMany(Competitor::className(), ['id' => 'competitor_id'])->orderBy(['name'=>SORT_ASC])->via('projectCompetitors');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceExportMode() {
        return $this->hasOne(PriceExportMode::className(), ['id' => 'price_export_mode_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceFormerType() {
        return $this->hasOne(PriceFormerType::className(), ['id' => 'price_former_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitionMode() {
        return $this->hasOne(CompetitionMode::className(), ['id' => 'competition_mode_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion() {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectRegions() {
        return $this->hasMany(ProjectRegion::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegions() {
        return $this->hasMany(Region::className(), ['id' => 'region_id'])->via('projectRegions');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectExecutions() {
        return $this->hasMany(ProjectExecution::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectExecutionStatus() {
        return $this->hasOne(ProjectExecutionStatus::className(), ['id' => 'project_execution_status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectTheme() {
        return $this->hasOne(ProjectTheme::className(), ['id' => 'project_theme_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchedule() {
        return $this->hasMany(Schedule::className(), ['requester_id' => 'id'])->andWhere(['requester_entity_id' => Entity::Project]);
    }

    /**
     * @return ProjectExecution|array|\yii\db\ActiveRecord|null
     */
    public function getLastProjectExecution() {
        if ($this->_lastProjectExecution === false) {
            $this->_lastProjectExecution = ProjectExecution::find()
                ->andWhere([
                    'project_id' => $this->id
                ])
                ->orderBy([
                    'number' => SORT_DESC
                ])
                ->limit(1)
                ->one();
        }
        return $this->_lastProjectExecution;
    }

}