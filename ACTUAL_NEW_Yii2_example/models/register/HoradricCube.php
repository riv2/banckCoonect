<?php
namespace app\models\register;

use app\components\base\type\Register;
use app\components\DateTime;
use app\components\ValidationRules;
use app\models\enum\HoradricCubeStatus;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\pool\NomenclatureDocumentItem;
use app\models\reference\Brand;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\reference\CompetitorShopName;
use app\models\reference\Item;
use app\models\reference\Project;
use app\models\reference\ProjectItem;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class HoradricCube
 * @package app\models\register
 *
 * Парсинг
 *
 * @property string brand_id
 * @property string item_id
 * @property string competitor_id
 * @property string parsing_id
 * @property string parsing_project_id
 * @property int  horadric_cube_status_id
 * @property string competitor_item_id
 * @property string competitor_shop_name
 * @property string competitor_item_name
 * @property float competitor_item_price
 * @property string competitor_item_url
 * @property string competitor_item_sku
 * @property string competitor_item_seller
 * @property string vi_item_name
 * @property float vi_item_price
 * @property string vi_item_url
 * @property string vi_item_sku
 * @property float percent
 * @property float predict
 * @property string vi_item_brand_name
 * @property string vi_item_id
 * @property string vi_item_matrix
 * @property bool vi_item_in_msk
 * @property string filter_reason
 * @property int sales_rank
 * @property bool auto_match
 *
 *
 * @property Brand brand
 * @property Item item
 * @property Competitor competitor
 * @property Parsing parsing
 * @property HoradricCubeStatus  horadricCubeStatus
 */

class HoradricCube extends Register
{
    public $parallel_main_id = null;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Идентификация товаров';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Идентификация товаров';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            ValidationRules::ruleUuid('parsing_id'),
            ValidationRules::ruleUuid('parsing_project_id'),
            parent::rules(),
            [
                [['brand_id','item_id','competitor_id','competitor_item_id'], 'string'],
                [['competitor_shop_name','competitor_item_name','competitor_item_url','competitor_item_seller','competitor_item_sku','vi_item_name','vi_item_url','vi_item_sku','vi_item_brand_name','vi_item_id','filter_reason'], 'string'],
                [['vi_item_matrix'], 'string'],
                [['vi_item_in_msk','auto_match'], 'boolean'],
                [['sales_rank','predict','percent'], 'number'],
                [['horadric_cube_status_id','competitor_item_price','vi_item_price'], 'number'],
                [['created_user_id', 'updated_user_id', 'parallel_main_id'], 'safe'],
            ],
            ValidationRules::ruleDefault('horadric_cube_status_id', HoradricCubeStatus::STATUS_NEW),
            ValidationRules::ruleEnum('horadric_cube_status_id', HoradricCubeStatus::className())
        );
    }

    /**
     * @inheritDoc
     */
    public function crudIndexSearchRelations()
    {
        return [
            'status',
            'competitor',
            'item',
            'brand',
            'horadricCubeStatus',
            'parsing',
            'updatedUser',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'brand_id'                  => 'Бренд',
                'item_id'                   => 'Товар',
                'competitor_id'             => 'Конкурент',
                'parsing_id'                => 'Парсинг',
                'parsing_project_id'                => 'Парсинг',
                'horadric_cube_status_id'   => 'Статус',
                'competitor_item_id'        => 'Товар конкурента',
                'competitor_shop_name'      => 'Магазин',
                'competitor_item_name'      => 'Название у конкурента',
                'competitor_item_price'     => 'Цена у конкурента',
                'competitor_item_url'       => 'УРЛ конкурента',
                'competitor_item_sku'       => 'Артикул',
                'competitor_item_seller' => 'Продавец',
                'vi_item_name'              => 'Название ВИ',
                'vi_item_price'             => 'Цена ВИ',
                'vi_item_url'               => 'УРЛ ВИ',
                'vi_item_sku'               => 'Артикул ВИ',
                'vi_item_id'                => 'ID сайта ВИ',
                'vi_item_brand_name'        => 'Бренд ВИ',
                'percent'                   => 'Процент отклонения',
                'brand'                     => 'Бренд',
                'item'                      => 'Товар',
                'competitor'                => 'Конкурент',
                'parsing'                   => 'Парсинг',
                'auto_match'                => 'Автосопоставлен',
                'horadricCubeStatus'        => 'Статус',
                'vi_item_in_msk'            => 'Наличие в ВИ МСК',
                'vi_item_matrix'            => 'Матрица',
                'predict'                   => 'Совпадение',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(
            parent::relations(),
            [
                'item',
                'brand',
                'competitor',
                'parsingProject',
                'competitorItem',
                'horadricCubeStatus',
            ]
        );
    }


    /**
     * @inheritdoc
     */
    public function crudIndexColumns()
    {
        return array_merge(
            parent::crudIndexColumns(),
            [
                'vi_item_name' => [
                    'attribute' => 'vi_item_name',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->renderViItemName();
                    }
                ],
                'competitor_item_name' => [
                    'attribute' => 'competitor_item_name',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->renderCompetitorItemName();
                    }
                ],
                'percent' => [
                    'attribute' => 'percent',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->renderPercent();
                    }
                ],
                'predict' => [
                    'attribute' => 'predict',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->predict;
                    }
                ],
                'competitor',
                'competitor_item_sku',
                'brand',
                'vi_item_price',
                'competitor_item_price',
                'horadricCubeStatus',
                'auto_match',
                'created_at',
                'parsing',
                'updated_at',
                'updated_user_id' => [
                    'attribute' => 'updatedUser',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::a(Html::encode($model->updatedUser), ['/users/update', 'id' => $model->updated_user_id], ['target' => '_blank']);
                    }
                ],
            ]
        );
    }

    public function renderViItemName() {
        return Html::a($this->vi_item_name, $this->vi_item_url, ['target' => '_blank','data-horadric-item' => $this->id, 'class' => 'vi-item-name']);
    }

    public function renderCompetitorItemName() {
        return Html::a($this->competitor_item_name, ANON_URL.$this->competitor_item_url, ['target' => '_blank']);
    }

    public function renderPercent() {
        return '<span class="horadric-percent" style="color: '.self::getColorString($this->percent).'">'.round($this->percent,0).'%</span>';
    }


    public static function getColorString ($value, $alpha = 1)
    {
        $min4 = ($value) ^ 2;
        $r = round(250/20 * $value,0);
        $g = round(200 - $min4,0);
        return "rgba($r, $g, 50, $alpha)";
    }

    public static $competitorNameCache = [];


    public function setupCompetitorId() {
        if (!$this->competitor_id && $this->competitor_shop_name) {
            if (isset(self::$competitorNameCache[$this->competitor_shop_name])) {
                $this->competitor_id = self::$competitorNameCache[$this->competitor_shop_name];
            } else {
                $this->competitor_id = Competitor::find()
                    ->where([
                        'name' => $this->competitor_shop_name,
                    ])
                    ->select(['id'])
                    ->scalar();
                if (!$this->competitor_id) {
                    $this->competitor_id = CompetitorShopName::find()
                        ->where([
                            'name' => $this->competitor_shop_name,
                        ])
                        ->select(['competitor_id'])
                        ->scalar();
                }
                self::$competitorNameCache[$this->competitor_shop_name] = $this->competitor_id;
            }
        }

    }

    public function rollbackCompetitorItem() {
        $this->setupCompetitorId();
        if ($this->competitor_id && $this->item_id && $this->horadric_cube_status_id === HoradricCubeStatus::STATUS_MATCHED) {
            CompetitorItem::updateAll([
                'status_id' => Status::STATUS_REMOVED,
            ], [
                'url' => $this->competitor_item_url,
                'competitor_id' => $this->competitor_id,
                'item_id' => $this->item_id,
                'source_id' => Source::SOURCE_WEBSITE,
            ]);
        }
    }


    public function createCompetitorItem() {
        $this->setupCompetitorId();
        if ($this->competitor_id) {
            $competitorItem = CompetitorItem::findOne([
                'competitor_id' => $this->competitor_id,
                'item_id'       => $this->item_id,
                'sku'           => $this->competitor_item_sku,
                'source_id'     => Source::SOURCE_WEBSITE,
            ]);
            if (!$competitorItem) {
                $competitorItem = new CompetitorItem();
            }
            $competitorItem->item_id        = $this->item_id;
            $competitorItem->competitor_id  = $this->competitor_id;
            $competitorItem->source_id      = Source::SOURCE_WEBSITE;
            $competitorItem->sku            = $this->competitor_item_sku;
            $competitorItem->competitor_item_seller = $this->competitor_item_seller;
            $competitorItem->competitor_item_name   = $this->competitor_item_name;
            $competitorItem->name           = $this->competitor_item_name;
            $competitorItem->url            = $this->competitor_item_url;
            $competitorItem->status_id      = Status::STATUS_ACTIVE;
            $competitorItem->save();

            $this->horadric_cube_status_id = HoradricCubeStatus::STATUS_MATCHED;

            return $competitorItem->id;
        }
        return false;
    }

    function validateMatchingYandex() {

        // - Провести фильтрацию: бренд, который входит в наименование конкурента - также должен содержаться в наименовании бренда ВИ(с приведением к одному регистру), иначе - отфильтровывается
        if (!preg_match('/'.preg_quote(preg_replace('/[\/\+&]/',' ',$this->vi_item_brand_name)).'/ui', preg_replace('/[\/\+&]/',' ',$this->competitor_item_name))) {
            $this->horadric_cube_status_id = HoradricCubeStatus::STATUS_FILTERED_OUT;
            $this->filter_reason = 'Не найден бренд "'.$this->vi_item_brand_name.'" в названии - '.$this->competitor_item_name;
            return false;
        }

        // - Прошедшие проверку товары на бренды проходят проверку на отклонение цены: 0.7 < Цена конкурента / Цена ВИ < 1.5. Если неравенство НЕ выполняется - товар отфильтровывается
        $priceDelta = ($this->vi_item_price ) ? $this->competitor_item_price / $this->vi_item_price : 0;
        $this->percent = round(abs(1 - $priceDelta) * 100,0);
        if (!(0.7 < $priceDelta  && $priceDelta < 1.5)) {
            $this->horadric_cube_status_id = HoradricCubeStatus::STATUS_FILTERED_OUT;
            $this->filter_reason = "Отклонение цены {$this->percent}%. [ВИ:{$this->vi_item_price} ] [Кон:{$this->competitor_item_price}]";
            return false;
        }

        $url = strtolower($this->competitor_item_url);
        $url = trim($url,'/');
        $url = preg_replace('/https?:\/\/(www\.)?/','',$url);

        // - получившиеся в итоге пары "товар конкурента - товар ВИ" проверяются на наличие в журнале несоответствий
        $wrongMatchExists = self::find()
            ->andWhere([
                'vi_item_sku'               => $this->vi_item_sku,
                //'horadric_cube_status_id'   => HoradricCubeStatus::STATUS_WRONG, // Да в принципе тут при любом статусе не надо второй раз добавлять
                'status_id'                 => Status::STATUS_ACTIVE
            ])
            ->andWhere(['ilike', 'competitor_item_url' , $url])
            ->exists();

        if ($wrongMatchExists) {
            $this->horadric_cube_status_id = HoradricCubeStatus::STATUS_FILTERED_OUT;
            $this->filter_reason = 'Уже есть в журнале несоответствия (или на ручном разборе)';
            return false;
        }

        $itemId = Item::find()
            ->andWhere([
                'sku' => $this->vi_item_sku,
                'status_id' => Status::STATUS_ACTIVE
            ])
            ->select(['id','brand_id'])
            ->asArray()
            ->one();

        if (!$itemId) {
            $this->horadric_cube_status_id = HoradricCubeStatus::STATUS_FILTERED_OUT;
            $this->filter_reason = 'Не найден такой товар у ВИ';
            return false;
        }


        $this->item_id = $itemId['id'];
        $this->brand_id = $itemId['brand_id'];

        // https://hrenot.com/T282
        // 1.2 Необходимо изменить алгоритм попадания товаров на ручной разбор: если в справочнике “товары конкурентов” у guid товара ВИ  уже есть некий прикрепленный  товар ДАННОГО конкурента, то данный guid товара считается
        // идентифицированный и на ручной разбор НЕ должен попадать (относится при Яндекс поиске, так и при идентификации через api)
        if ($this->competitor_id) {
            $alreadyMatched = CompetitorItem::find()
                ->andWhere([
                    'competitor_id'             => $this->competitor_id,
                    'item_id'                   => $this->item_id,
                    'competitor_item_sku'    => $this->competitor_item_sku,
                    'status_id' => Status::STATUS_ACTIVE,
                ])
                ->exists();
            if ($alreadyMatched) {
                $this->horadric_cube_status_id = HoradricCubeStatus::STATUS_FILTERED_OUT;
                $this->filter_reason = 'Данный GUID товара уже сопоставлен в справочнике конкурентов с SKU '.$this->competitor_item_sku;
                return false;
            }
        }


        // 10.07.2018 17:38 Евгений
        // Смотри, есть проблемка - нам на ручной разбор летит много товара, который давно не в наличии у нас на сайте. Т.е. получается, что мы тратим время на разбор на неживой товар. У меня предложение: отправлят на ручной разбор только номенклатуру, которая есть хотя бы в одном из проектов расчёта - сделать последний фильтр. Можешь сделать ? по идее, не должно быть долго
        $exists = ProjectItem::find()
            ->alias('pi')
            ->innerJoin(['p' => Project::tableName()], 'pi.project_id = p.id')
            ->andWhere([
                'pi.item_id' => $this->item_id,
                'pi.status_id' => Status::STATUS_ACTIVE,
                'p.status_id' => Status::STATUS_ACTIVE,
            ])
            ->select(['pi.item_id'])
            ->limit(1)
            ->exists();

        if (!$exists) {
            $this->horadric_cube_status_id = HoradricCubeStatus::STATUS_FILTERED_OUT;
            $this->filter_reason = 'Товар отсутствует в проектах расчета';
            return false;
        }


        //- Полученные пары "Урл конкурента - артикул ВИ" - ищутся в справочнике "Товары конкурентов" (чтобы не добавлять, если там уже есть). Если в справочнике такие пары найдены - они отфильтровываются
// убрано в рамках https://hrenot.com/T282
        $competitorItemId = CompetitorItem::find()
            ->andWhere([
                'item_id'   => $itemId['id'],
                'status_id' => Status::STATUS_ACTIVE
            ])
            ->andWhere(['ilike', 'url' , $url])
            ->asArray()
            ->select(['id','competitor_id'])
            ->one();

        if ($competitorItemId) {
            $this->competitor_item_id       = $competitorItemId['id'];
            $this->competitor_id            = $competitorItemId['competitor_id'];
            $this->horadric_cube_status_id  = HoradricCubeStatus::STATUS_FILTERED_OUT;
            $this->filter_reason            = 'Уже есть в справочнике товаров конкурентов';
            return false;
        }

        return true;
    }


    function validateMatching() {

        $url = strtolower($this->competitor_item_url);
        $url = trim($url,'/');
        $url = preg_replace('/https?:\/\/(www\.)?/','',$url);

        $item = Item::find()
            ->andWhere([
                'id'        => $this->vi_item_id,
                'status_id' => Status::STATUS_ACTIVE
            ])
            ->select(['id','brand_id','price_default','sales_rank'])
            ->asArray()
            ->one();

        if (!$item || !isset($item['id']) || $item['id'] === '') {
            $this->horadric_cube_status_id = HoradricCubeStatus::STATUS_FILTERED_OUT;
            $this->filter_reason = 'Не найден такой товар у ВИ (' . $this->vi_item_id . ')';
            return false;
        }

        // - получившиеся в итоге пары "товар конкурента - товар ВИ" проверяются на наличие в журнале несоответствий
        $wrongMatchExists = self::find()
            ->andWhere([
                'vi_item_id'                => $this->vi_item_id,
            ])
            ->andWhere(['ilike', 'competitor_item_url' , $url])
            ->exists();

        if ($wrongMatchExists) {
            $this->horadric_cube_status_id = HoradricCubeStatus::STATUS_FILTERED_OUT;
            $this->filter_reason = 'Уже есть в журнале несоответствия (или на ручном разборе)';
            return false;
        }

        $this->item_id       = $item['id'];
        $this->sales_rank    = $item['sales_rank'];
        $this->brand_id      = $item['brand_id'];
        $this->vi_item_price = $item['price_default'];

        $priceDelta = ($this->vi_item_price ) ? $this->competitor_item_price / $this->vi_item_price : 0;
        $this->percent = round(abs(1 - $priceDelta) * 100,0);

        // https://hrenot.com/T282
        // 1.2 Необходимо изменить алгоритм попадания товаров на ручной разбор: если в справочнике “товары конкурентов” у guid товара ВИ  уже есть некий прикрепленный  товар ДАННОГО конкурента, то данный guid товара считается
        // идентифицированный и на ручной разбор НЕ должен попадать (относится при Яндекс поиске, так и при идентификации через api)
        if ($this->competitor_id) {
            $alreadyMatched = CompetitorItem::find()
                ->andWhere([
                    'competitor_id'             => $this->competitor_id,
                    'item_id'                   => $this->item_id,
                    'sku'                       => $this->competitor_item_sku,
                    'status_id'                 => Status::STATUS_ACTIVE
                ])
                ->exists();
            if ($alreadyMatched) {
                $this->horadric_cube_status_id = HoradricCubeStatus::STATUS_FILTERED_OUT;
                $this->filter_reason = 'Данный GUID товара уже сопоставлен в справочнике конкурентов';
                return false;
            }
        }

        // 10.07.2018 17:38 Евгений
        // Смотри, есть проблемка - нам на ручной разбор летит много товара, который давно не в наличии у нас на сайте. Т.е. получается, что мы тратим время на разбор на неживой товар. У меня предложение: отправлят на ручной разбор только номенклатуру, которая есть хотя бы в одном из проектов расчёта - сделать последний фильтр. Можешь сделать ? по идее, не должно быть долго
        $exists = ProjectItem::find()
            ->alias('pi')
            ->innerJoin(['p' => Project::tableName()], 'pi.project_id = p.id')
            ->andWhere([
                'pi.item_id' => $this->item_id,
                'pi.status_id' => Status::STATUS_ACTIVE,
                'p.status_id' => Status::STATUS_ACTIVE,
            ])
            ->select(['pi.item_id'])
            ->limit(1)
            ->exists();

        if (!$exists) {
            $exists = NomenclatureDocumentItem::find()
                ->alias('ndi')
                ->innerJoin(['p' => Project::tableName()], 'ndi.nomenclature_document_id = p.nomenclature_document_id')
                ->andWhere([
                    'ndi.item_id' => $this->item_id,
                    //'ndi.status_id' => Status::STATUS_ACTIVE,
                    'p.status_id' => Status::STATUS_ACTIVE,
                ])
                ->select(['ndi.item_id'])
                ->limit(1)
                ->exists();
            if (!$exists) {
                $this->horadric_cube_status_id = HoradricCubeStatus::STATUS_FILTERED_OUT;
                $this->filter_reason = 'Товар отсутствует в проектах расчета';
                return false;
            }
        }

        return true;
    }


    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHoradricCubeStatus() {
        return $this->hasOne(HoradricCubeStatus::className(),['id' => 'horadric_cube_status_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParsing()
    {
        return $this->hasOne(Parsing::className(), ['id' => 'parsing_id'])->cache(3600);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParsingProject()
    {
        return $this->hasOne(Parsing::className(), ['id' => 'parsing_project_id'])->cache(3600);
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
    public function getCompetitorItem()
    {
        return $this->hasOne(CompetitorItem::className(), ['id' => 'competitor_item_id']);
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
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }
}