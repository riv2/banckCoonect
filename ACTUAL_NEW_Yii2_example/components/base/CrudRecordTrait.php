<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 14.09.16
 * Time: 05:07
 */

namespace app\components\base;

use app\models\enum\Status;
use app\widgets\FormBuilder;
use netis\crud\db\ActiveQuery;
use yii\bootstrap\ActiveField;
use yii\db\ActiveRelationTrait;

trait CrudRecordTrait
{
    public $labelAttribute = 'name';

    public static function crudCreateEnabled() {
        return true;
    }
    public static function crudDeleteEnabled(){
        return true;
    }

    /**
     * Какие колонки выводить в индексе
     * @return array
     */
    public function crudIndexColumns() {
        return [];
    }

    public function crudIndexSearchRelations() {
        return [];
    }

    public function crudSearch($params = []) {
        if ($this->hasAttribute('status_id') && !isset($params['status_id'])) {
            $params['status_id'] = Status::STATUS_ACTIVE;
        }
        /** @var ActiveQuery $query */
        $query = $this->search($params);
        $fields = [];
        if (isset($params['fields'])) {
            $fields = explode(',', $params['fields']);
        }
        foreach ($this->crudIndexSearchRelations() as $relationName) {
            if (count($fields) == 0 || in_array($relationName, $fields)) {
                /** @var ActiveRelationTrait $relation */
                $relation = $this->getRelation($relationName);
                if ($relation) {
                    if ($relation->multiple) {
                        $query->distinct();
                    }
                    $query->joinWith($relationName);
                }
            }
        }
        return $query;
    }

    /**
     * @param \yii\db\ActiveRecord $model
     * @param string $relation
     * @param \yii\db\ActiveQuery $activeRelation
     * @param bool $multiple true for multiple values inputs, usually used for search forms
     * @return ActiveField
     */
    public static function crudAsRelationFilter($model, $relation, $activeRelation, $multiple) {
        return FormBuilder::getRelationWidgetOptions($model, $relation, $activeRelation, $multiple);
    }
    
}