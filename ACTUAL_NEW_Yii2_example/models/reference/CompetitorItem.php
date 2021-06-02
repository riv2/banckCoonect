<?php
namespace app\models\reference;

use app\components\base\Entity;
use app\components\base\type\Reference;
use app\components\DateTime;
use app\components\exchange\Exchange;
use app\components\ValidationRules;
use app\models\enum\ErrorType;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\pool\ParsingError;
use app\models\register\Error;
use app\models\register\Task;
use app\processing\CompetitorItemProcessing;
use yii\helpers\Html;

/**
 * Class CompetitorItem
 *
 * Номенклатура магазина
 *
 * @package app\components\base\type
 * @property int    source_id           Торговая площадка
 * @property string item_id             Товар
 * @property string competitor_id       Конкурнет
 *
 * @property string     competitor_item_name
 * @property string competitor_item_seller
 * @property int        price
 * @property DateTime   price_updated_at
 * @property DateTime   error_last_date
 * @property int   errors_count
 *
 * @property string sku                 Артикул или YM ID
 * @property string url                 УРЛ в магазине
 *
 * @property Source     source          Торговая площадка
 * @property Item       item            Товар
 * @property Competitor competitor      Конкурнет
 */

class CompetitorItem extends Reference
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Товар конкурента';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Товары конкурентов';
    }

    public static function noCount()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired('item_id','competitor_id'),
            ValidationRules::ruleUuid('item_id'),
            ValidationRules::ruleUuid('competitor_id'),
            ValidationRules::ruleDateTime('price_updated_at','error_last_date'),
            ValidationRules::ruleDefault('source_id', Source::SOURCE_WEBSITE),
            [
                [['sku', 'url','competitor_item_name','competitor_item_seller'], 'string'],
                [['price','errors_count'], 'number'],
                [['source_id'], 'default', 'value' => Source::SOURCE_WEBSITE],
                [['item_id'], 'unique', 'targetAttribute' => ['item_id', 'competitor_id', 'sku']],
                [['created_user_id', 'updated_user_id'], 'safe'],
            ],
            ValidationRules::ruleEnum('source_id', Source::className()),
            //[],
            []
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
            'updatedUser',
        ];
    }

    /**
     * @param Task $task
     */
    public static function taskUpdatePrices(Task $task)
    {
        CompetitorItemProcessing::updatePrices($task);
    }
    /**
     * @param Task $task
     */
    public static function taskUpdateErrors(Task $task) {
        CompetitorItemProcessing::updateErrors($task);
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        $this->source_id = Source::SOURCE_WEBSITE;
        if (!$this->name) {
            $this->name = $this->item_id;
        }
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {

        if (parent::beforeSave($insert)) {
            if (!$this->source_id) {
                $this->source_id = Source::SOURCE_WEBSITE;
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'source_id'     => 'Торговая площадка',
                'item_id'       => 'Товар',
                'competitor_id' => 'Конкурент',
                'sku'           => 'Артикул',
                'url'           => 'URL',
                'competitor_item_name'  => 'Название',
                'competitor_item_seller'=>'Продавец',
                'price'                 => 'Цена',
                'price_updated_at'      => 'Цена обновлена',
                'source'        => 'Торговая площадка',
                'item'          => 'Товар',
                'competitor'    => 'Конкурент',
                'error_last_date' => 'Посл. ошибка',
                'errors_count'    => 'Кол-во ошибок',
            ]
        );
    }


    public function recycle()
    {
        return $this->delete();
    }

    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'status',
            'competitor',
            'item',
            'competitor_item_name',
            'price',
            'url' => [
                'label' => 'URL конкурента',
                'attribute' => 'url',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->url, ANON_URL.$model->url, ['target' => '_blank']);
                }
            ],
            'sku',
            'competitor_item_seller',
            'error_last_date',
            'errors_count',
            'price_updated_at',
            'created_at',
            'updated_at',
            'updated_user_id' => [
                'attribute' => 'updatedUser',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a(Html::encode($model->updatedUser), ['/users/update', 'id' => $model->updated_user_id], ['target' => '_blank']);
                }
            ],
        ]);
    }

    public function fileImportPresetColumns()
    {
        return array_merge(parent::fileImportPresetColumns(),[
            'competitor_id',
        ]);
    }

    public function excludeFieldsFileImportColumns()
    {
        return array_merge(parent::excludeFieldsFileImportColumns(),[
            'id',
            'name',
            'source_id',
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

    public static function relations()
    {
        return array_merge(parent::relations(), [
            'source',
            'item',
            'competitor',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource() {
        return $this->hasOne(Source::className(), ['id' => 'source_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem() {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitor() {
        return $this->hasOne(Competitor::className(), ['id' => 'competitor_id']);
    }
}