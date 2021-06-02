<?php
namespace app\models\reference;

use app\components\DateTime;
use app\components\base\Entity;
use app\components\base\type\Reference;
use app\components\exchange\Exchange;
use app\components\ValidationRules;
use app\models\cross\CategoryItem;
use app\models\enum\ErrorType;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\pool\NomenclatureDocumentItem;
use app\models\register\Error;
use app\models\register\Task;
use yii\base\UserException;
use yii\helpers\ArrayHelper;

/**
 * Class Item
 * @package app\models\reference
 *
 * @property string ymUrl
 *
 * @property string brand_id
 * @property int wtis_index
 * @property int site_index
 * @property int sku
 * @property boolean is_expendable
 * @property boolean is_liquid
 * @property string vendor_type_text
 * @property string pricing_keyword
 * @property string pricing_must_be
 * @property string pricing_dont_be
 * @property int ym_index
 * @property string ym_url
 * @property string main_id
 * @property boolean is_duplicate
 * @property int sales_rank
 *
 * @property float price_supply  Закупочная цена
 * @property float price_recommended_retail  РРЦ
 * @property float price_default Цена ВИ МСК
 * @property float price_weighted Средневзвешенная
 *
 * @property Brand          brand
 * @property Category[]     categories
 * @property Category[]     topCategories
 * @property CategoryItem[] categoryItems
 * @property CompetitorItem[]     competitorItems
 * @property ProjectItem[]  projectItems
 *
 */
class Item extends Reference
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Товар';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Товары';
    }
    /**
     * @inheritdoc
     */
    public static function noCount() {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            [
                [['wtis_index', 'site_index', 'sku', 'ym_index'], 'number', 'integerOnly' => true],
                [['price_supply', 'price_recommended_retail', 'price_default','price_weighted'], 'number'],
                [['is_expendable', 'is_liquid','is_duplicate'], 'boolean'],
                [['sales_rank'], 'number'],
                [['main_id','vendor_type_text','pricing_keyword','pricing_must_be','pricing_dont_be','ym_url'], 'string'],
            ],
            []
        );
    }

    public static function isBigData() {
        return true;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->ym_url = $this->ymUrl;
            return true;
        }
        return false;
    }

    /**
     * Построение YM URL
     * 1. Если поле ""ID YM"" является НЕ пустым и числовым ( не равным 0!), тогда URL YM формируется как : https://market.yandex.ru/product/YM ID/offers?&how=aprice&grhow=shop&deliveryincluded=0&pricefrom=ЗАКУПОЧНАЯ МСК*0,9&priceto=ПРОДАЖНАЯ МСК*1,3&[PARAM]:ID=GUID$RRC=РРЦ$,Здесь: вместо выделенного нужно вставить значения по товару. Числовые значения цены должны быть округлены до це
     * 2. Иначе, при условии что ключевое слово ен содержит слово ""нет"": https://market.yandex.ru/search.xml?text=КЛЮЧЕВОЕ СЛОВО&how=aprice&grhow=shop&deliveryincluded=0&pricefrom=ЗАКУПОЧНАЯ МСК*0,9&priceto=ПРОДАЖНАЯ МСК*1,3&[PARAM]:ID=GUID$RRC=РРЦ$
     * 3. Если Ключевое слово равно ""нет"" и ID YM отсутствует или равно нулю - значение URL YM должно очищаться (т.е. url YM по такому товару должен отсутствовать//затираться)
     * @return null|string
     */
    public function getYmUrl() {
        $url = 'https://market.yandex.ru';
        $priceFrom      = $this->price_supply?round($this->price_supply * 0.9):0;
        $priceTo        = $this->price_default?round($this->price_default * 1.3):0;

        if ($this->ym_index) {
            $url .= '/product/'.$this->ym_index.'/offers?';
        } else if ($this->pricing_keyword && mb_strtolower($this->pricing_keyword != 'нет')){
            $url .= '/search.xml?text=' . urlencode($this->pricing_keyword);
        } else {
            return null;
        }
        $url .= '&how=aprice&grhow=shop&deliveryincluded=0&pricefrom='.$priceFrom;
        if ($priceTo) {
            $url .= '&priceto=' . $priceTo;
        }
        $url .= "&cpa=";
        return $url;
    }

    /**
     * Обновить цены у всех товаров которые спользуются в проектах
     * @param Task $task
     * @param null $projectId
     */
    public static function taskItemUpdatePrices(Task $task, $projectId = null) {
        try {
            $task->task_status_id        = TaskStatus::STATUS_RUNNING;
            $task->save();
            $itemsQuery = Item::find()
                ->alias('i')
                ->select('i.id as item_id')
                ->indexBy('item_id')
                ->asArray();
            if ($projectId) {
                /** @var Project $project */
                $project = Project::findOne($projectId);
                if (!$project) {
                    throw new UserException('Указан неверный ID проекта: ' . $projectId);
                }
                if ($project->nomenclature_document_id) {
                    $itemsQuery->innerJoin(
                        ['pi' => NomenclatureDocumentItem::tableName()],
                        'pi.item_id = i.id AND pi.nomenclature_document_id = :nomenclature_document_id',
                        [':nomenclature_document_id' => $project->nomenclature_document_id]
                    );
                } else {
                    $itemsQuery->innerJoin(
                        ['pi' => ProjectItem::tableName()],
                        'pi.item_id = i.id AND pi.project_id = :project_id',
                        [':project_id' => $project->id]
                    );
                }
            }
            
            $count = 0;
                
            foreach($itemsQuery->batch(1000) as $rows) {
                $itemIds    = array_keys($rows);
                $count      +=  count($itemIds);

                Exchange::runImport([
                    'PricesSupply' => [
                        'importIds' => $itemIds
                    ],
                    'PricesVi' => [
                        'importIds' => $itemIds
                    ]
                ]);

                $task->progress = $count;
                $task->save();
            }
            
            //-
            $task->task_status_id   = TaskStatus::STATUS_FINISHED;
            $task->finished_at      = new DateTime();
            $task->status_id        = Status::STATUS_DISABLED;
            $task->save();

        } catch (\Exception $e) {
            Error::logError($e, ErrorType::TYPE_TASK, Entity::Task, $task->id);
            $task->task_status_id   = TaskStatus::STATUS_QUEUED;
            $task->save();
        }
    }

    public static function updateAllPrices($data = []) {
        $tryToStart = isset($data['tryToStart']) ? true : false;
        $task = new Task;
        $task->requester_entity_id  = Entity::Item;
        $task->task_function        = 'itemUpdatePrices';
        $task->task_type_id         = TaskType::TYPE_PRICE_ORIGINS_UPDATE;
        $task->enqueue($tryToStart);
    }

    public static function updateAllUrls($data = []) {
        $tryToStart = isset($data['tryToStart']) ? true : false;
        $task = new Task;
        $task->requester_entity_id  = Entity::Item;
        $task->task_function        = 'itemUpdateUrls';
        $task->task_type_id         = TaskType::TYPE_ITEM_UPDATE_URLS;
        $task->enqueue($tryToStart);
    }
    
    
    /**
     * Обновить YM URL у товаров которые используются в проекте $projectId, либо во всех проектах если $projectId не указан
     * @param Task $task
     * @param null $projectId
     */
    public static function taskItemUpdateUrls(Task $task, $projectId = null) {
        $errors     = 0;
        $count      = 0;
        $find       = Item::find()
            ->alias('i')
            ->innerJoin(['pi' => ProjectItem::tableName()],'i.id = pi.item_id');

        if ($projectId) {
            $find->andWhere([
                'pi.project_id' => $projectId
            ]);
        }

        $items                  = $find->all();
        $task->task_status_id   = TaskStatus::STATUS_RUNNING;
        $task->started_at       = new DateTime();
        $task->total            = count($items);
        $task->progress         = $count;
        $task->save();

        foreach ($items as $item) {
            try{
                // Так-то он при сохранении товара обновляется
                $item->save();
            } catch (\Exception $e) {
                $errors++;
                Error::logError($e, ErrorType::TYPE_TASK, Entity::Task, $task->id);
            }
            $count++;
            if ($count % 1000 == 0 || $count == $task->total) {
                $task->progress = $count;
                $task->save();
            }
        }

        $task->task_status_id   = TaskStatus::STATUS_FINISHED;
        $task->finished_at      = new DateTime();
        $task->progress         = $count;
        $task->had_errors       = ($errors > 0);
        $task->status_id        = Status::STATUS_DISABLED;
        $task->save();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'id'                => 'WTIS GUID Товара',
                'wtis_index'        => 'Номер',
                'site_index'        => 'Сайт ID',
                'brand_id'          => 'Бренд',
                'sku'               => 'Артикул',
                'is_expendable'     => 'Расходка',
                'is_liquid'         => 'Ликвид',
                'price_supply'      => 'Закупочная цена',
                'price_recommended_retail' => 'РРЦ',
                'price_default'     => 'Цена ВИ МСК',
                'price_weighted'    => 'Cредневзвешенная ЗЦ',
                'brand'             => 'Бренд',
                'categories'        => 'Категории',
                'projects'          => 'Проекты',
                'competitors'       => 'Конкуренты',
                'vendor_type_text'  => 'Вендор тип',
                'pricing_keyword'   => 'Ключевое слово',
                'pricing_must_be'   => 'Must Be слово',
                'pricing_dont_be'   => "Don't be слово",
                'ym_index'          => "YM ID",
                'ym_url'            => "YM URL",
                'sales_rank'            => "Ранг",
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations() {
        return [
            'brand',
            //'categories',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(), [
            'brand',
            'categories',
//            'priceOrigins',
//            'shops',
//            'projects',
//            'competitors',
        ]);
    }


    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'id',
            'site_index',
            'sales_rank',
            'name',
            'brand',
            'sku',
            'pricing_keyword',
            'ym_index',
            'pricing_must_be',
            'pricing_dont_be' ,
            'price_supply',
            'price_default',
            'price_weighted',
            'topCategories' => [
                'label'  => 'Корневые рубрики',
                'format' => 'raw',
                'value'  => function($model) {
                    /** @var Item $model */
                    $r = [];
                    foreach ($model->topCategories as $cat) {
                        $r[] = $cat;
                    }
                    return '<nobr>'.join("</nobr>, <nobr>", $r).'</nobr>';
                }
            ],
            'ym_url',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitors()
    {
        return $this->hasMany(Competitor::className(), ['id' => 'competitor_id'])->via('shops');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::className(), ['id' => 'project_id'])->via('projectItems');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectItems()
    {
        return $this->hasMany(ProjectItem::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitorItems()
    {
        return $this->hasMany(CompetitorItem::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTopCategories()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])->andOnCondition([
            'or',
            [
                Category::tableName().'.is_top' => true,
                Category::tableName().'.status_id' => Status::STATUS_ACTIVE
            ],
            [
                Category::tableName().'.id' => null,
            ],
        ])->via('categoryItems');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])->via('categoryItems');
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryItems()
    {
        return $this->hasMany(CategoryItem::className(), ['item_id' => 'id']);
    }
}