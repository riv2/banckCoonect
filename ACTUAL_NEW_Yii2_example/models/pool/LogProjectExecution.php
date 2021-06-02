<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\ValidationRules;
use app\models\reference\Item;
use app\models\reference\Project;
use app\models\document\ProjectExecution;
use app\models\enum\Region;
use app\models\reference\ProjectItem;
use app\validators\UuidValidator;

/**
 * Class PriceParsed
 * @package app\models\pool
 *
 * @property string region_id           Регион
 * @property string item_id             Товар
 * @property int    project_id              Проект
 * @property string project_item_id         Номенклатура проекта
 * @property string project_execution_id    Исполнение проекта
 * @property string price_calculated_id     Расчетная цена
 * @property string item_name
 * @property string item_brand_name
 * @property string item_ym_index
 * @property string item_ym_url
 * @property string brand_id
 *
 * @property float  price_calculated         
 * @property float  price_supply            
 * @property float  price_recommended_retail      
 * @property float  price_default
 * @property float price_weighted Средневзвешенная
 *
 * @property bool   is_export
 * @property bool   rrp_regulations
 *
 * @property float  margin
 *
 * @property Region             region              Регион
 * @property Item               item                Товар
 * @property Project            project             Проект
 * @property ProjectItem        projectItem         Номенклатура проекта
 * @property ProjectExecution   projectExecution    Исполнение проекта
 * @property PriceCalculated    priceCalculated     Расчетная цена
 */

class LogProjectExecution extends Pool
{
    public static function noCount() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Журнал расчёта цен';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Журнал расчёта цен';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleUuid('item_id'),
            ValidationRules::ruleUuid('brand_id'),
            ValidationRules::ruleUuid('project_id'),
            ValidationRules::ruleUuid('project_item_id'),
            ValidationRules::ruleUuid('project_execution_id'),
            ValidationRules::ruleUuid('price_calculated_id'),
            ValidationRules::ruleDefault('is_export', false),
            [
                [['price_calculated', 'price_supply', 'price_recommended_retail', 'price_default','margin','price_weighted'], 'number'],
                [['is_export'], 'boolean'],
                [['item_ym_index','item_ym_url'], 'string'],
            ]
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
                'region_id'             => 'Регион',
                'item_id'               => 'Товар',
                'brand_id'              => 'Бренд',
                'project_id'            => 'Проект',
                'project_item_id'       => 'Номенклатура проекта',
                'project_execution_id'  => 'ID Исполнения проекта',
                'price_calculated_id'   => 'Расчетная цена',
                'item_name' => 'Товар',
                'item_ym_index'     => 'YM ID',
                'item_ym_url'       => 'YM URL',
                'item_brand_name' => 'Бренд',

                'price_calculated'              => 'Расчетная цена',
                'price_supply'                  => 'Цена закупки',
                'price_recommended_retail'      => 'РРЦ',
                'price_weighted'                => 'Cред.взв. ЗЦ',
                'price_default'              => 'ВИ МСК',

                'is_export'             => 'Это экспорт',

                'region'                => 'Регион',
                'regions'               => 'Регионы',
                'item'                  => 'Товар',
                'project'               => 'Проект',
                'projectItem'           => 'Номенклатура проекта',
                'projectExecution'      => 'Исполнение проекта',
                'priceCalculated'       => 'Расчетная цена',
                'rrp_regulations'       => 'Регламент РРЦ',
                'margin'                => 'Маржа',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'item',
            'item_brand_name',
            'priceCalculated',
            'price_calculated',
            'price_supply',
            'price_recommended_retail',
            'price_default',
            'price_weighted',
            'margin',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations() {
        return [
             'item',
           // 'priceCalculated',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'item',
            //'project',
            //'priceCalculated',
            //'projectExecution',
        ]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
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
        return $this->hasOne(ProjectExecution::className(), ['id' => 'project_execution_id']);
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
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->item_id;
    }

    /**
     * @inheritdoc
     */
    public function processSearchToken($token, array $attributes, $tablePrefix = null)
    {
        //$c = parent::processSearchToken($token, $attributes, $tablePrefix);
        if (UuidValidator::test($token)) {
            return ['t.item_id' => $token];
        }
        return ['ILIKE', 't.item_name', $token];
    }
}