<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\ValidationRules;
use app\models\cross\CategoryCategory;
use app\models\cross\CategoryItem;
use app\models\enum\Status;

/**
 * Class Category
 * @package app\models\reference
 *
 * @property boolean is_top
 * @property Item[] items
 * @property CategoryItem[] categoryItems
 * @property Category[] children
 * @property Category[] parents
 * @property CategoryCategory[] categoryChildren
 * @property CategoryCategory[] categoryParents
 */
class Category extends Reference
{

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Категория';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Категории';
    }

    public static function isBigData() {
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            [
                [['is_top'], 'boolean'],
                [['is_top'], 'default', 'value' => true],
            ],
            ValidationRules::ruleDefault('is_top',false)
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
                'is_top'            => 'Верхнего уровня',
                'items'             => 'Товары',
                'children'          => 'Подкатегории',
                'parents'           => 'Надкатегории',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(), [
            'children',
            'parents',
//            'items',
        ]);
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        return $this->hasMany(Item::className(), ['id' => 'item_id'])->via('categoryItems', function($query) {
            /** @var \yii\db\ActiveQuery $query */
            $query->innerJoin(['item2' => Item::tableName()],CategoryItem::tableName().'.item_id = item2.id')->andWhere(['item2.status_id' => Status::STATUS_ACTIVE]);
        });
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryItems()
    {
        return $this->hasMany(CategoryItem::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Category::className(), ['id' => 'child_id'])->via('categoryChildren');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParents()
    {
        return $this->hasMany(Category::className(), ['id' => 'parent_id'])->via('categoryParents');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryChildren()
    {
        return $this->hasMany(CategoryCategory::className(), ['parent_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryParents()
    {
        return $this->hasMany(CategoryCategory::className(), ['child_id' => 'id']);
    }
}