<?php
namespace app\models\cross;

use \app\components\base\type\Cross;
use app\components\ValidationRules;
use app\models\reference\Category;

/**
 * Class CategoryCategory
 *
 * Дерево категорий
 *
 * @package app\models\cross
 * @property string parent_id
 * @property string child_id
 *
 * @property Category parent
 * @property Category child
 */
class CategoryCategory extends Cross
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Связь категрии с категорией';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Связи категрии с категорией';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired('parent_id','child_id'),
            [],
            []
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
                'parent_id'            => 'Родительская',
                'child_id'             => 'Дочерняя',
                'parent'               => 'Родительская',
                'child'                => 'Дочерняя',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(), [
            'parent',
            'child',
        ]);
    }


    /**
     * @return Category
     */
    public function getChild()
    {
        return $this->hasOne(Category::className(), ['id' => 'child_id']);
    }

    /**
     * @return Category
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }
}