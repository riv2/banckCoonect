<?php

namespace app\models\reference;

use app\components\base\BaseModel;

/**
 * Модель роли RBAC
 *
 * @property string $name
 * @property string $description
 */
class Role extends BaseModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name', 'description'], 'string', 'max' => 255],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name'        => 'ID',
            'description' => 'Наименование',
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item}}';
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return parent::find()
            ->alias('t')
            ->andWhere([
                't.type' => \yii\rbac\Role::TYPE_ROLE,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Роль';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Роли';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->isNewRecord ? '(новый)' : $this->description;
    }
}
