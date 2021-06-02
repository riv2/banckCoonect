<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\DateTime;
use app\components\ValidationRules;
use app\models\enum\Source;
use app\models\reference\Competitor;
use app\models\reference\Item;
use app\models\enum\Region;
use app\models\reference\ParsingProject;
use app\validators\UuidValidator;
use yii\bootstrap\Html;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class PriceRefined
 * @package app\models\pool
 *
 * @property float price                Цена
 * @property DateTime extracted_at      Дата сбора данных
 *
 * @property int source_id              Торговая площадка
 * @property string item_id             Товар
 * @property string competitor_id       Конкурнет
 * @property string competitor_shop_name
 * @property string competitor_item_name
 * @property string competitor_item_seller
 * @property string competitor_item_sku
 * @property string url
 * @property string parsing_id
 * @property string parsing_project_id
 * @property string price_parsed_id
 * @property array regions
 *
 * @property int delivery_days
 *
 *
 * @property boolean out_of_stock       Не в наличии
 * @property boolean http404        Признак 404
 *
 * @property Source     source          Торговая площадка
 * @property Region     region          Регион
 * @property Item       item            Товар
 * @property Competitor competitor      Конкурнет
 * @property ParsingProject parsingProject Проект парсинга
 * @property PriceParsed priceParsed
 */

class PriceRefined extends Pool
{
    /**
     * @inheritdoc
     */
//    public static function noCount() {
//        return false;
//    }


    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Цены конкурентов';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Цены конкурентов';
    }
    public function importOneFromFile($attributes) {
       if (!isset($attributes['item_id']) && isset($attributes['url'])) {
            $attributes['item_id'] = \app\models\reference\CompetitorItem::find()
                ->select('item_id')
                ->andWhere(['url' => $attributes['url']])
                ->asArray()
                ->scalar();
       }
       if (isset($attributes['regions']) && is_string($attributes['regions'])) {
           if ($attributes['regions'] === 'Великий Новгород') {
               $attributes['regions'] = 'Новгород';
           }
           $regionId = Region::find()
               ->select('id')
               ->andWhere(['name' => $attributes['regions']])
               ->scalar();
           if ($regionId) {
               $attributes['regions'] = [$regionId];
           }
       }
       return parent::importOneFromFile($attributes);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired('source_id', 'item_id'),
            [
                [['url','parsing_id','parsing_project_id','price_parsed_id','competitor_item_name','competitor_shop_name','competitor_item_seller','competitor_item_sku'],'string'],
                [['price','delivery_days'], 'number'],
                [['out_of_stock','http404'], 'boolean'],
                [['regions'], 'filter', 'filter' => function ($value) {
                    if (is_string($value)) {
                        return array_map('intval', explode(',', $value));
                    }
                    return $value;
                }],
                [['regions'],'safe'],
                [['extracted_at'], 'default', 'value' => new DateTime()],
            ],
            ValidationRules::ruleDefault('out_of_stock', false),
            ValidationRules::ruleDateTime('extracted_at'),
            ValidationRules::ruleUuid('item_id'),
            ValidationRules::ruleUuid('competitor_id'),
            ValidationRules::ruleEnum('source_id', Source::className()),
            []
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'price' => 'Цена',
                'extracted_at' => 'Дата сбора данных',
                'source_id' => 'Торговая площадка',
                'regions' => 'Регион',
                'item_id' => 'ID Товара',
                'competitor_id' => 'Конкурент',
                'parsing_project_id' => 'Проект парсинга',
                'price_parsed_id' => 'Спарсенная цена',
                'out_of_stock' => 'Не в наличии',
                'http404' => 'HTTP404',
                'source' => 'Торговая площадка',
                'item' => 'Товар',
                'competitor' => 'Конкурент',
                'parsingProject' => 'Проект парсинга',
                'competitor_shop_name' => 'Магазин',
                'competitor_item_seller' => 'Продавец',
                'competitor_item_sku' => 'sku',
                'delivery_days' => 'Дни доставки',
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function fileImportPresetColumns()
    {
        return array_merge(parent::fileImportPresetColumns(), [
            'regions',
            'source_id',
        ]);
    }


    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations() {
        return [
            //'item',
            //'competitor',
            //'source',
            //'parsingProject',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(), [
            'item',
            'competitor',
            'source',
            'parsingProject',
        ]);
    }


    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'extracted_at',
            'created_at',
            'item_id',
            'item',
            'price',
            'out_of_stock',
            'http404',
            'competitor_item_seller',
            'competitor_item_sku',
            'competitor' => [
                'label' => 'Конкурент',
                'format'=> 'raw',
                'value' => function($model) {
                    /** @var \app\models\pool\LogPriceCalculation $model */
                    return ($model->competitor_id) ? Html::a($model->competitor, ['/crud-competitor/view', 'id' => $model->competitor_id]):$model->competitor_shop_name;
                }
            ],
            'parsingProject',
            'delivery_days',
            'out_of_stock',
            'regions' =>  [
                'label' => 'Регионы',
                'format'=> 'raw',
                'value' => function($model) {
                    return Json::encode($model->regions);
                }
            ],
            //'source',
        ]);
    }

    public function getSort($config= [])
    {
        $config = ['defaultOrder' => ['extracted_at' => SORT_DESC]];
        return parent::getSort($config);
    }
    
    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id'])->cache(3600, new TagDependency(['tags' => ['calculation']]));
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(Source::className(), ['id' => 'source_id'])->cache(36000);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitor()
    {
        return $this->hasOne(Competitor::className(), ['id' => 'competitor_id'])->cache(3600);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParsingProject()
    {
        return $this->hasOne(ParsingProject::className(), ['id' => 'parsing_project_id'])->cache(3600);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceParsed()
    {
        return $this->hasOne(PriceParsed::className(), ['id' => 'price_parsed_id']);
    }

    public function getName() {
        return number_format($this->price,2,',',' ');
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

}