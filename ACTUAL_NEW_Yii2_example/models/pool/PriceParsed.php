<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\DateTime;
use app\components\exchange\Exchange;
use app\components\ValidationRules;
use app\models\enum\PriceParsedStatus;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\reference\Brand;
use app\models\reference\BrandFilter;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\reference\CompetitorShopDomain;
use app\models\reference\CompetitorShopIndex;
use app\models\reference\CompetitorShopName;
use app\models\reference\Item;
use app\models\enum\Region;
use app\models\reference\ParsingProject;
use app\models\reference\Setting;
use app\models\register\Parsing;
use app\widgets\FormBuilder;
use yii;
use yii\base\InvalidValueException;
use yii\db\IntegrityException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Данные о ценах с сайтов конкурнетов
 * Извлеченные цены
 * 
 * Class PriceParsed
 * @package app\models\pool
 *
 * @property string price
 * @property DateTime extracted_at
 * 
 * # Для быстрого сопоставления (преимущество если заполнено)
 * @property int source_id              Торговая площадка
 * @property string item_id             Товар
 * @property string competitor_id       Конкурнет
 *
 * @property string parsing_project_id  Проект парсинга
 * @property string parsing_id          Парсинг
 * @property int    price_parsed_status_id Статус
 * @property string error_message           Ошибка
 *
 * @property int delivery_days
 * @property int thread
 * 
 * # Для определения магазина конкурента
 * @property string competitor_shop_name           Название магазина
 * @property string competitor_shop_domain         Домен
 * 
 * # Для определения товара
 * @property string competitor_item_name    Название товара на торговой площадке
 * @property string competitor_item_sku     Артикул или YM ID
 * @property string competitor_item_url УРЛ в магазине
 * @property string competitor_item_rubric1
 * @property string competitor_item_rubric2
 * @property string competitor_item_seller
 * @property string competitor_item_brand
 * @property string url
 *
 * @property string original_url
 *
 * @property boolean out_of_stock        Инф. о неналичии
 * @property string delivery            Инф. о доставке.
 * @property boolean http404        Признак 404
 *
 * @property string brand_id             Бренд
 *
 * @property array     regions
 * @property Source     source          Торговая площадка
 * @property Region     region          Регион
 * @property Item       item            Товар
 * @property Competitor competitor      Конкурнет
 * @property ParsingProject  parsingProject Проект парсинга
 * @property Parsing         parsing        Парсинг
 * @property string parsingName
 */

class PriceParsed extends Pool
{
    /**
     * @inheritdoc
     */
    public static function noCount() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Спарсенные цены';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Спарсенные цены';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            [
//                [['source_id'], function () {
//                    if (!empty($this->item_id) || !empty($this->competitor_item_sku)) {} else {
//                        $this->addError('item_id', "Вы должны указать либо GUID товара (item_id) либо его артикул в магазине конкурента (competitor_item_sku), либо его YM ID (competitor_item_sku)");
//                    }
//                    if (!empty($this->competitor_shop_name) ||  !empty($this->competitor_shop_domain) || !empty($this->competitor_id)) {} else {
//                        $this->addError('competitor_id', "Вы должны указать либо GUID  конкурента(competitor_id), либо его название (competitor_shop_name), либо его домен (competitor_shop_domain).");
//                    }
//                }],
                [['regions'], function () {
                    if (empty($this->regions)) {
                        $this->addError('regions', "Вы должны указать регион(ы) цены передав указав ID регионов через запятую в параметре regions");
                    }
                }],
                [['regions'], 'filter', 'filter' => function ($value) {
                    if (is_string($value)) {
                        return array_map('intval', explode(',', $value));
                    }
                    return $value;
                }],
                [['source_id','competitor_shop_name','competitor_shop_domain','competitor_item_name','competitor_item_sku','url','delivery','item_id','price'], 'safe'],
                [['price'], 'string', 'length' => [0, 255]],
                [['competitor_item_url', 'url','original_url'], 'string', 'length' => [0, 2048]],
                [['delivery_days'], 'number'],
                [['competitor_shop_name', 'competitor_shop_domain','competitor_item_rubric1','competitor_item_rubric2','competitor_item_seller','competitor_item_brand'], 'string', 'length' => [0, 255]],
                [['competitor_item_name', 'competitor_item_sku'], 'string', 'length' => [0, 255]],
                [[ 'delivery'], 'string', 'length' => [0, 255]],
                [['error_message'], 'string'],
                [['out_of_stock','http404'], 'boolean'],
                [['regions'], 'safe'],
                [['thread','price_parsed_status_id'], 'number'],
            ],
            ValidationRules::ruleUuid('brand_id'),
            ValidationRules::ruleRequired('source_id', ['message' => 'Вы должны указать source_id (source_id=1 - Маркет, source_id=2 - Сайты)','skipOnEmpty' => false]),
            ValidationRules::ruleUuid('parsing_project_id'),
            ValidationRules::ruleUuid('parsing_id'),
            ValidationRules::ruleUuid('item_id'),
            ValidationRules::ruleUuid('competitor_id'),
            ValidationRules::ruleDateTime('extracted_at')
        );
    }
    
    public static function crudAsRelationFilter($model, $relation, $activeRelation, $multiple) {
        return '';
    }

    public function getFloatPrice() {
        $p = preg_replace('/[^\d,\.]/', '', "{$this->price}");
        $p = str_ireplace(',','.',$p);
        $p = trim($p, '.');
        return floatval($p);
    }

    public static $competitorNameCache  = [];
    public static $competitorIndexCache = [];
    public static $competitorIdNameCache = [];

    /**
     * Создать цену конкурнета
     * @throws IntegrityException
     */
    public function createRefinedPrice() {

        $competitorId           = $this->competitor_id;
        $competitorShopName     = $this->competitor_shop_name;
        $itemId                 = $this->item_id;

        // =====================================  Поиск competitor_id

        /** @var CompetitorItem $competitorItem */
        $competitorItem   = null;

        if (!$competitorId && $this->competitor_shop_domain) {
            $competitorId = CompetitorShopDomain::find()
                ->where([
                    'name'      => CompetitorShopDomain::normalizeDomain($this->competitor_shop_domain),
                ])
                ->select(['competitor_id'])
                ->scalar();
        }

        if (!$competitorId && $competitorShopName) {
            if (isset(self::$competitorNameCache[$competitorShopName])) {
                $competitorId = self::$competitorNameCache[$competitorShopName];
            } else {
                $competitorId = Competitor::find()
                    ->where([
                        'name' => $competitorShopName,
                    ])
                    ->select(['id'])
                    ->scalar();
                if (!$competitorId) {
                    $competitorId = CompetitorShopName::find()
                        ->where([
                            'name' => $competitorShopName,
                        ])
                        ->select(['competitor_id'])
                        ->scalar();
                }
                self::$competitorNameCache[$competitorShopName] = $competitorId;
            }
        }

        if ($competitorId) {
            if (!isset(self::$competitorIdNameCache[$competitorId])) {
                self::$competitorIdNameCache[$competitorId] = Competitor::find()
                    ->andWhere(['id' => $competitorId])
                    ->limit(1)
                    ->select('name')
                    ->scalar();
            }
            if (!$competitorShopName) {
                $competitorShopName = self::$competitorIdNameCache[$competitorId];
            }
        }

        if (!$competitorId) {
            throw new IntegrityException('По какой-то невероятной причине не указан competitor_id');
        }


        // =====================================  Поиск item_id
        /** @var Item $item */
        $item             = null;
        if (!$itemId) {
            if ($this->source_id === Source::SOURCE_YANDEX_MARKET) {
                $item = Item::findOne([
                    'ym_index'          => $this->competitor_item_sku,
                ]);
                if (!$item && $this->competitor_item_name) {
                    $item = Item::findOne([
                        'name' => $this->competitor_item_name,
                    ]);
                }
                if ($item) {
                    $itemId = $item->id;
                }
            } else {
                if ($this->competitor_item_sku) {
                    $competitorItem = CompetitorItem::findOne([
                        'competitor_id' => $competitorId,
                        'sku'           => $this->competitor_item_sku,
                        'source_id'     => $this->source_id,
                        'status_id' => Status::STATUS_ACTIVE,
                    ]);
                }
                if ($competitorItem) {
                    $itemId = $competitorItem->item_id;
                }
            }
        }

        // =====================================  Проставление item_id и competitor_id
        if (!$this->item_id && $itemId) {
            $this->item_id = $itemId;
        }
        if (!$this->competitor_id) {
            $this->competitor_id = $competitorId;
        }

        // =====================================  Проверка регионов
        $regions = $this->regions;
        if (empty($regions)) {
            throw new IntegrityException("Не указан регион(ы)");
        }


        // =====================================  Проверка сопоставления с ВИ
        if (!$itemId) {
            if ($this->competitor_item_brand || $this->competitor_item_name) {
                $brandIdVi = BrandFilter::filter($this->competitor_item_brand ?: $this->competitor_item_name , false);
                if ($brandIdVi) {
                    $this->brand_id = $brandIdVi;
                    $this->price_parsed_status_id = PriceParsedStatus::ABSENT_IN_COM_ITEMS;
                    $this->error_message = "Отсутствует сопоставление с товаром ВИ";
                    $this->save(false);
                    return false;
                }
            }

            throw new IntegrityException("Не удалось определить ТОВАР для спарсеной цены $this [".Json::encode($this->toArray())."]");
        }

        $price = $this->getFloatPrice();

        if (!$this->out_of_stock) {
            // =====================================  Обработка цены
            $words = preg_replace('/[\d,\.]/', '', "{$this->price}");
            if ($price <= 0) {
                throw new IntegrityException("Нулевая цена \"{$this->price}\" [" . Json::encode($this->toArray()) . "]");
            }
            if (strlen($words) > strlen("{$price}")) {
                throw new IntegrityException("Цена \"{$this->price}\" не похожа на число $price [" . Json::encode($this->toArray()) . "]");
            }
            // ====================================== проверка отклонения
//            /** @var PriceParsed $lastParsedPrice */
//            $lastParsedPrice = PriceParsed::find()
//                ->andWhere([
//                    'competitor_id' => $this->competitor_id,
//                    'item_id' => $this->item_id,
//                    'out_of_stock' => false,
//                    'price_parsed_status_id' => PriceParsedStatus::STATUS_REFINED,
//                ])
//                ->orderBy('extracted_at DESC')
//                ->one();
//            ;
//            if ($lastParsedPrice) {
//                $priceVarianceUp = Setting::getValue('price_variance_up', 200);
//                $priceVarianceDown = Setting::getValue('price_variance_down', 50);
//                if (
//                    (
//                        $this->price > $lastParsedPrice->price
//                        && ((($this->price/$lastParsedPrice->price) * 100 - 100) > 200)
//                    )
//                    || ((100 - ($this->price/$lastParsedPrice->price) * 100) > 50)
//                ) {
//                    throw new IntegrityException("Слишком большое отклонение \"$this->price\" от последней спарсенной цены $lastParsedPrice->price");
//                }
//            }
        }


//        // =====================================  Проверка наличия
//        if ($this->out_of_stock && $this->out_of_stock !== 'false' ) {
//            $this->price_parsed_status_id = PriceParsedStatus::STATUS_FILTERED_OUT;
//            $this->error_message = 'Товар не в наличии';
//            $this->save(false);
//            return false;
//        }

        // =====================================  Фильтрация по ЯндексМаркету (устарело?)
        $filterByCompetitorItemsSettings = $competitorId ? true : false;
        if ($filterByCompetitorItemsSettings && $this->source_id === Source::SOURCE_YANDEX_MARKET) {
            if (!$item && $itemId) {
                $item = Item::findOne($itemId);
            }
            if ($this->competitor_item_name) {
                if ($item->pricing_must_be) {
                    $keywords = explode('||', $item->pricing_must_be);
                    $valid = true;
                    $word  = "";
                    foreach ($keywords as $kw) {
                        if (!empty(trim($kw)) && !preg_match('/' . preg_quote($kw,'/') . '/i', $this->competitor_item_name)) {
                            $valid = false;
                            $word  = $kw;
                        }
                    }
                    if (!$valid) {
                        $this->price_parsed_status_id = PriceParsedStatus::STATUS_FILTERED_OUT;
                        $this->error_message = "Отсутствует Must Be слово \"$word\"";
                        $this->save(false);
                        return false;
                    }
                }
                if ($item->pricing_dont_be) {
                    $keywords = explode('||', $item->pricing_dont_be);
                    foreach ($keywords as $kw) {
                        if (!empty(trim($kw)) && preg_match('/' . preg_quote($kw,'/') . '/i', $this->competitor_item_name)) {
                            $this->price_parsed_status_id = PriceParsedStatus::STATUS_FILTERED_OUT;
                            $this->error_message = "Присутствует Dont Be слово \"$kw\"";
                            $this->save(false);
                            return false;
                        }
                    }
                }
            }
        }

        // =====================================  Создание обработанной цены
        $priceRefined = new PriceRefined;
        $priceRefined->loadDefaultValues();
        if ($competitorId) {
            $priceRefined->competitor_id        = $competitorId;
        }
        $priceRefined->item_id                  = $itemId;
        $priceRefined->competitor_shop_name     = $competitorShopName;
        $priceRefined->url                      = $this->url;
        $priceRefined->regions                  = $regions;
        if (empty($priceRefined->regions))      {
            $priceRefined->regions = [1];
        }
        $priceRefined->out_of_stock             = $this->out_of_stock;
        $priceRefined->http404                  = $this->http404;
        $priceRefined->source_id                = $this->source_id;
        $priceRefined->price                    = $price;
        $priceRefined->parsing_id               = $this->parsing_id;
        $priceRefined->parsing_project_id       = $this->parsing_project_id;
        $priceRefined->price_parsed_id          = $this->id;
        $priceRefined->extracted_at             = $this->extracted_at;
        $priceRefined->delivery_days            = $this->delivery_days;
        $priceRefined->competitor_item_name     = $this->competitor_item_name;
        $priceRefined->competitor_item_seller   = $this->competitor_item_seller;
        $priceRefined->competitor_item_sku      = $this->competitor_item_sku;
        $priceRefined->save(false);

        $this->price_parsed_status_id = PriceParsedStatus::STATUS_REFINED;
        $this->save(false);
        return true;
    }

    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'id'                    => 'Цена',
                'source_id'             => 'Площадка',
                'item_id'               => 'Товар',
                'competitor_id'         => 'Конкурент',

                'competitor_shop_name'             => 'Название магазина',
                'competitor_shop_domain'           => "Домен магазина",
                'competitor_item_name'        => 'Наименование товара',
                'competitor_item_sku'         => 'Артикул/YM_ID товара',
                'competitor_item_rubric1'     => 'Рубрика 1',
                'competitor_item_rubric2'     => 'Рубрика 2',
                'competitor_item_seller'    => 'Продавец',
                'competitor_item_brand'          => 'Бренд',
                'brand'                 => 'Бренд',
                'url'                   => 'URL парсинга',
                'competitor_item_url'   => 'УРЛ товара',
                
                'out_of_stock'          => 'Инф. о неналичии',
                'http404'               => 'HTTP404',
                'extracted_at'          => 'Дата получения цены',

                'source'                => 'Площадка',
                'region'                => 'Регион',
                'regions'               => 'Регионы',
                'item'                  => 'Товар',
                'competitor'            => 'Конкурент',

                'price'                 => 'Цена',
                'parsing_project_id'    => 'Проект парсинга',
                'parsing_id'            => 'Парсинг',
                'parsingProject'        => 'Проект парсинга',
                'parsingName'        => 'Парсинг',
                'parsing'               => 'Парсинг',
                'price_parsed_status_id' => 'Статус',
                'priceParsedStatus'     => 'Статус',
                'error_message'         => 'Ошибка',
                'delivery_days'         => 'Доставка',
                'delivery'              => 'Доставка',
                'created_at'            => 'Дата изменения',
            ]
        );
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            if (!$this->extracted_at) {
                $this->extracted_at = new DateTime();
            }
            $this->created_at = new DateTime();
            return true;
        }
        return false;
    }

    public function fileImportPresetColumns()
    {
        return array_merge(parent::fileImportPresetColumns(),[
            'regions',
            'source_id',
        ]);
    }

    public function excludeFieldsFileImportColumns()
    {
        return array_merge(parent::excludeFieldsFileImportColumns(),[
            'id',
        ]);
    }

    public function importOneFromFile($attributes)
    {
        parent::importOneFromFile($attributes);

        if ($this->item_id) {
            Exchange::runImport([
                'Items' => ['importIds' => [$this->item_id], 'forced' => false]
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations() {
        return [
            'item',
            //'competitor',
            //'source',
            'priceParsedStatus',
            //'parsing',
            //'parsingProject',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'item',
            'competitor',
            //'source',
            //'brand',
            'parsingProject',
            //'parsing',
            'priceParsedStatus',
        ]);
    }


    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        if ($this->scenario === 'matching') {
            return [
                'parsingProject',
                'created_at',
                'priceParsedStatus',
                'error_message',
                'http404',
                'competitor_item_name',
                'url' => [
                    'label' => 'URL конкурента',
                    'attribute' => 'url',
                    'format' => 'raw',
                    'value' => function($model) {
                        if ($model->price_parsed_status_id < PriceParsedStatus::MATCHING_NEW) {
                            return Html::a($model->competitor_item_url, ANON_URL.$model->competitor_item_url, ['target' => '_blank']);
                        }
                        return Html::a($model->url, ANON_URL.$model->url, ['target' => '_blank']);
                    }
                ],
                'competitor_item_url' => [
                    'label' => 'URL ВИ',
                    'attribute' => 'competitor_item_url',
                    'format' => 'raw',
                    'value' => function($model) {
                        if ($model->price_parsed_status_id < PriceParsedStatus::MATCHING_NEW) {
                            return '';
                        }
                        return Html::a('...'.substr($model->competitor_item_url,-40), $model->competitor_item_url, ['target' => '_blank']);
                    }
                ],
                'price',
                'competitor_shop_name',
                'competitor',
                'delivery_days',
            ];
        }
        $pp     = Yii::$app->request->get('PriceParsed', []);
        $oos    = isset($pp['out_of_stock']) ? $pp['out_of_stock'] : '';
        return array_merge(parent::crudIndexColumns(),[
            'parsingProject',
            'extracted_at',

            'created_at',
            'priceParsedStatus',
            'price',
            'outOfStock' => [
                'label' => 'Налич',
                'filter'    => Html::dropDownList('PriceParsed[out_of_stock]', $oos, [
                    ''  => 'Все',
                    'true'  => 'Отсутствует',
                    'false' => 'В наличии',
                ],[
                    'class' => 'form-control'
                ]),
                'format'    => 'raw',
                'value' => function($model) {
                    return $model->out_of_stock == 'true' ? 'Отсутствует' : 'В наличии' ;
                }
            ],
            'error_message',
            'http404',
            'competitor',
            'item',
            'delivery_days',
            'delivery',
            'competitor_item_sku',
            'competitor_item_name',
            'competitor',
            'competitor_item_rubric1',
            'competitor_item_rubric2',
            'competitor_item_seller',
            'competitor_item_brand',
            'brand_id',
            'competitor_shop_name',
            'source',
            'regions' => [
                'label' => 'Регионы',
                'attribute' => 'regions',
                'format'    => 'raw',
                'value' => function($model) {
                    return $model->regions ? implode(',', $model->regions) : null;
                }
            ],
            'url',
            'competitor_item_url' => [
                'label' => 'URL Конкурента',
                'attribute' => 'competitor_item_url',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a(substr($model->competitor_item_url,-0, 40).'...', $model->competitor_item_url, ['target' => '_blank']);
                }
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        foreach ([
            'matching'
                 ] as $scenario) {

            if (!isset($scenarios[$scenario])) {
                $scenarios[$scenario] = $scenarios[self::SCENARIO_DEFAULT];
            }
        }
        return $scenarios;
    }

    public function getCompetitorName() {
        return $this->competitor_id && $this->competitor ? $this->competitor->name : $this->competitor_shop_name;
    }

    public function getSort($config = [])
    {

        if ($this->scenario == 'matching') {
            $config = ['defaultOrder' => ['created_at' => SORT_DESC]];
        }else {
            $config = ['defaultOrder' => ['extracted_at' => SORT_DESC]];
        }
        return parent::getSort($config);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id'])->cache(3600);
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
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id'])->cache(3600);
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
    public function getParsing()
    {
        return $this->hasOne(Parsing::className(), ['id' => 'parsing_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParsingName()
    {
        $key = '#parsing[' . $this->parsing_id . '].name';
        $name = Yii::$app->cache->get($key);
        if ($name === false) {
            $name = Parsing::find()
                ->andWhere(['id' => $this->parsing_id])
                ->select('name')
                ->scalar();
            Yii::$app->cache->set($key, $name, 300);
        }
        return $name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceParsedStatus()
    {
        return $this->hasOne(PriceParsedStatus::className(), ['id' => 'price_parsed_status_id']);
    }


    public function getName() {
        return $this->price;
    }
}