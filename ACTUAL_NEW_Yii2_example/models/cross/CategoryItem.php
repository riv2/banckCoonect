<?php
namespace app\models\cross;

use \app\components\base\type\Cross;
use app\components\ValidationRules;
use app\models\enum\Status;
use app\models\reference\Category;
use app\models\reference\Item;

/**
 * Class CategoryItem
 *
 * Связь категории с товаром
 *
 * @package app\models\reference
 * @property string category_id
 * @property string item_id
 * @property bool is_top
 * @property int status_id
 *
 * @property Category category
 * @property Item item
 */
class CategoryItem extends Cross
{
    /**
     * @var array
     */
    public static $treeCache = [];

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Связь категрии с товаром';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Связи категрии с товаром';
    }
    
    /**
     * @inheritdoc
     */
    public static function isBigData() {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['is_top'], 'boolean'],
                [['is_top'], 'default', 'value' => true],
            ],
            ValidationRules::ruleEnum('status_id', Status::className()),
            ValidationRules::ruleRequired('category_id','item_id'),
            [],
            []
        );
    }

    /**
     * @param $itemId
     * @param $categoryId 
     * @param $isTop
     * @param $statusId
     * @return bool
     */
    public static function createItemCategoryTree($itemId, $categoryId, $isTop = true, $statusId = Status::STATUS_ACTIVE) {
        if (isset(static::$treeCache[$itemId.'_'.$categoryId])) {
            return false;
        }
        $categoryItem = new CategoryItem();
        $categoryItem->item_id      = $itemId;
        $categoryItem->category_id  = $categoryId;
        $categoryItem->is_top       = $isTop;
        $categoryItem->status_id    = $statusId;
        $categoryItem->save();
        
        static::$treeCache[$itemId.'_'.$categoryId] = true;

        $parents = CategoryCategory::find()
            ->alias('cc')
            ->innerJoin([
                'c' => Category::tableName()
            ], "cc.parent_id = c.id")
            ->where([
                'cc.child_id'   => $categoryId
            ])
            ->select([
                'parent_id'     => 'cc.parent_id',
                'is_top'        => 'c.is_top',
                'status_id'     => 'c.status_id'
            ])
            ->asArray()
            ->all();

        foreach ($parents as $parent) {
            static::createItemCategoryTree($itemId, $parent['parent_id'], $parent['is_top'], $parent['status_id']);
        }
        return true;
    }

    public static function relations() {
        return [
            'category',
            'item',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory() {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem() {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }
}