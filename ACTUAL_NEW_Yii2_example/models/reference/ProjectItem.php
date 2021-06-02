<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\exchange\Exchange;
use app\components\ValidationRules;
use app\models\enum\SelectPriceLogic;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;

/**
 * Номенклатура проекта
 *
 * Class ProjectItem
 *
 * @package app\components\base\type
 * @property string     item_id                 Товар
 * @property string     project_id              Проект
 * @property int        select_price_logic_id   Алгоритм выбора цены
 * @property float      price_variation_modifier    Процент отклонения
 *
 * @property boolean    rrp_regulations         Регламент РРЦ
 * @property float      min_margin              Мин. наценка
 *
 * @property Item       item                    Товар
 * @property Project    project                 Проект
 *
 * @property SelectPriceLogic  selectPriceLogic Алгоритм выбора цены
 */
class ProjectItem extends Reference
{

    /** @var  Project кешированный проект */
    private $_project;

    public static function isBigData() {
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Ассортимент проекта';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Ассортимент проекта';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleUuid('item_id'),
            ValidationRules::ruleUuid('project_id'),
            ValidationRules::ruleRequired('project_id','item_id'),
            [
                [['price_variation_modifier'], 'filter', 'filter' => function ($value) {
                    return floatval($value);
                } ],
                [['price_variation_modifier'], 'number'],
                [['min_margin'], 'number'],
                [['rrp_regulations'], 'boolean'],
                [['rrp_regulations'], 'default', 'value' => false],
                [['item_id'], 'unique', 'targetAttribute' => ['project_id', 'item_id'], 'except' => self::SCENARIO_SEARCH],
            ],
            ValidationRules::ruleDefault('rrp_regulations',false),
            //ValidationRules::ruleDefault('select_price_logic_id',SelectPriceLogic::LOGIC_A),
            //ValidationRules::ruleEnum('select_price_logic_id', SelectPriceLogic::className()),
            //[],
            []
        );
    }

    public function recycle()
    {
        return $this->delete();
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if ($this->scenario != static::SCENARIO_SEARCH) {
            if (!$this->name && $this->item_id) {
                if ($this->item) {
                    $this->name = $this->item->name;
                } else {
                    $this->name = 'Неизвестно';
                }
            }
        }
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'item_id'               => 'Товар',
                'name'                  => 'Товар',
                'project_id'            => 'Проект',
                'min_margin'            => 'Мин. наценка',
                'price_variation_modifier'  => 'Процент отклонения',
                'select_price_logic_id' => 'Алгоритм средней цены',
                'rrp_regulations'       => 'Регламент РРЦ',
                'item'                  => 'Товар',
                'project'               => 'Проект',
                'selectPriceLogic'      => 'Алгоритм средней цены',
            ]
        );
    }

    /**
     * Кешированный проект
     * @param $project
     * @return Project
     */
    public function project($project = null) {
        if (!$this->_project) {
            if ($project && $project instanceof Project) {
                $this->_project = $project;
            } else {
                $this->_project = $this->project;
            }
        }
        return $this->_project;
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'project',
            'item',
            'brand.name' => [
                'label'     => 'Бренд',
                'attribute' => 'brand.name'
            ],
            'price_variation_modifier',
            'rrp_regulations',
            'min_margin',
        ]);
    }


    /**
     * @inheritdoc
     */
    public function excludeFieldsFileImportColumns()
    {
        return array_merge(parent::excludeFieldsFileImportColumns(),[
            'id',
            'select_price_logic_id',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function fileImportPresetColumns()
    {
        return array_merge(parent::fileImportPresetColumns(),[
            'project_id'
        ]);
    }

    /**
     * @inheritdoc
     */
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
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'project',
            'item',
            'selectPriceLogic',
            'brand',
        ]);
    }

    public function crudIndexSearchRelations()
    {
        return array_merge(parent::crudIndexSearchRelations(),[
            //'project',
            //'item',
            //'selectPriceLogic',
            //'brand',
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
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id'])->via('item');
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
    public function getSelectPriceLogic()
    {
        return $this->hasOne(SelectPriceLogic::className(), ['id' => 'select_price_logic_id']);
    }

}