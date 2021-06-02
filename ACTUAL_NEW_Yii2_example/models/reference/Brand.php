<?php
namespace app\models\reference;

use app\components\base\type\Reference;

/**
 * Class Brand
 * @package app\models\reference
 */
class Brand extends Reference
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Бренд';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Бренды';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'items'            => 'Товары',
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(), [
            'items'
        ]);
    }
    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            
            'name',
            'id',
        ]);
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        return $this->hasMany(Item::className(), ['brand_id' => 'id']);
    }
}