<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 15.06.16
 * Time: 13:16
 */

namespace app\components\crud\actions;


use app\components\crud\controllers\ActiveController;
use app\components\base\BaseModel;
use netis\crud\db\ActiveQuery;
use netis\crud\db\ActiveRecord;
use netis\crud\db\ActiveSearchInterface;
use netis\crud\db\LabelsBehavior;
use netis\crud\web\Response;
use yii;
use yii\base\Model;
use yii\db\ActiveRecordInterface;
use yii\web\NotFoundHttpException;

class Action extends \netis\crud\crud\Action
{
    /**
     * @var ActiveQuery cached query
     */
    private $query;
    
    /**
     * @param ActiveRecord $model
     * @param string $field relation name
     * @param ActiveQuery $relation
     * @return array grid column definition
     */
    protected static function getRelationColumn($model, $field, $relation)
    {
        $labels = $model->attributeLabels();
        return [
            'attribute' => $field,
            'format'    => ['crudLink', ['data-pjax' => '0']],
            'visible'   => true,
            'label'     => isset($labels[$field]) ? $labels[$field] : $model->getRelationLabel($relation, $field),
        ];
    }

    /**
     * Returns default fields list by combining model attributes with hasOne relations or just hasMany relations.
     * @param ActiveRecord $model
     * @param bool $extra if false, returns attributes and hasOne relations, if true, returns only hasMany relations
     * @return array default list of fields
     */
    public static function getDefaultFields($model, $extra = false)
    {
        $fields = $extra ? [] : $model->attributes();

        foreach ($model->relations() as $relation) {
            $activeRelation = $model->getRelation($relation);

            if ((!$extra && $activeRelation->multiple) || ($extra && !$activeRelation->multiple)) {
                continue;
            }

            if (count($activeRelation->link) == 1) {
                foreach ($activeRelation->link as $left => $right) {
                    $index = array_search($right, $fields);
                    if ($index) {
                        $fields[$index] = $relation;
                    } else {
                        $fields[] = $relation;
                    }
                }
            } else {
                $fields[] = $relation;
            }

        }
        return $fields;
    }


    public static function getRelationGridColumns($model, $fields, $relationName, $relation)
    {
        return self::getGridColumns($model, $fields);
    }

    /**
     * Returns all primary and foreign key column names for specified model.
     * @param ActiveRecord $model
     * @param bool $includePrimary
     * @return array names of columns from primary and foreign keys
     */
    public static function getModelKeys($model, $includePrimary = true)
    {
        $keys = array_map(function ($foreignKey) {
            array_shift($foreignKey);
            return array_keys($foreignKey);
        }, $model->getTableSchema()->foreignKeys);
        if ($includePrimary) {
            $keys[] = ['id'];
        }
        if (count($keys) > 0) {
            return call_user_func_array('array_merge', $keys);
        }
        else {
            return [];
        }
    }

    /**
     * Retrieves grid columns configuration using the modelClass.
     * @param Model $model
     * @param array $fields
     * @return array grid columns
     */
    public static function getGridColumns($model, $fields)
    {
        if (!$model instanceof ActiveRecord) {
            return $model->attributes();
        }

        /** @var ActiveRecord $model */
        list($behaviorAttributes, $blameableAttributes) = self::getModelBehaviorAttributes($model);
        /** @var LabelsBehavior $labelsBehavior */
        $labelsBehavior = $model->getBehavior('labels');
        $versionAttribute = $model->optimisticLock();
        $formats = $model->attributeFormats();
        $keys    = self::getModelKeys($model, false);

        $columns = [];

        foreach ($fields as $key => $field) {
            // for arrays and callables, don't generate the column, use the one provided
            if (is_array($field)) {
                $columns[$key] = $field;
                continue;
            } elseif (!is_string($field) && is_callable($field)) {
                $columns[$key] = call_user_func($field, $model);
                continue;
            }

            // if the field is from a relation (eg. client.firstname) treat it as an attribute
            $format = isset($formats[$field]) ? $formats[$field] : $model->getAttributeFormat($field);

            if ($format !== null) {
                if ((in_array($field, $keys)
                        && ($labelsBehavior === null || !in_array($field, $labelsBehavior->attributes)))
                    || in_array($field, $behaviorAttributes)
                    || $field === $versionAttribute
                ) {
                    continue;
                }
                $columns[] = static::getAttributeColumn($model, $field, $format);
                continue;
            }

            $relation = $model->getRelation($field);
            foreach ($relation->link as $left => $right) {
                if (in_array($right, $blameableAttributes)) {
                    continue 2;
                }
            }

            if (!Yii::$app->user->can($relation->modelClass . '.read')) {
                continue;
            }
            $columns[] = static::getRelationColumn($model, $field, $relation);
        }

        $columnsAssoc = [];

        foreach ($columns as $i => $column) {
            $index = isset($column['attribute']) ? $column['attribute'] : $i;
            $columnsAssoc[$index] = $column;
        }

        return $columnsAssoc;
    }

    /**
     * @return ActiveSearchInterface
     */
    public function getSearchModel()
    {
        /** @var ActiveRecord $model */
        if ($this->controller instanceof ActiveController) {
            $model = $this->controller->getSearchModel();
        } else {
            $model = new $this->modelClass();
        }
        return $model;
    }

    /**
     * Calls ActiveForm::validate() on the model if current request is ajax and not pjax.
     * @param \app\components\base\BaseModel|array $model
     * @return Response returns boolean false if current request is not ajax or is pjax
     */
    protected function validateAjax($model)
    {
        if (!Yii::$app->request->isAjax || Yii::$app->request->isPjax) {
            return false;
        }
        $response = clone Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        if (!is_array($model)) {
            $model = [$model];
        }
        $response->content = json_encode(call_user_func_array('\yii\widgets\ActiveForm::validate', $model));
        return $response;
    }

    /**
     * Returns the data model based on the primary key given.
     * If the data model is not found, a 404 HTTP exception will be raised.
     * @param string $id the ID of the model to be loaded. If the model has a composite primary key,
     * the ID must be a string of the primary key values separated by commas.
     * The order of the primary key values should follow that returned by the `primaryKey()` method
     * of the model.
     * @return BaseModel the model found
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id)
    {
        if ($this->findModel !== null) {
            return call_user_func($this->findModel, $id, $this);
        }

        /* @var $modelClass ActiveRecordInterface */
        $modelClass = $this->modelClass;
        $model = null;
        $model = $modelClass::findOne(['id' => $id]);

        if ($model === null) {
            throw new NotFoundHttpException("Object not found: $id");
        }
        return $model;
    }
}