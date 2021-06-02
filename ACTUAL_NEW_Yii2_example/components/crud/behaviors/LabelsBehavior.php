<?php
/**
 * @link http://netis.pl/
 * @copyright Copyright (c) 2015 Netis Sp. z o. o.
 */

namespace app\components\crud\behaviors;
use netis\crud\db\ActiveRecord;


/**
 * LabelsBehavior allows to configure how a model is cast to string and its other labels.
 * @package netis\crud\db
 */
class LabelsBehavior extends \netis\crud\db\LabelsBehavior
{
    /**
     * @var string class name of the owner model
     */
    private $modelClass;
    /**
     * @var array cached relation labels
     */
    private $cachedRelationLabels = [];
    /**
     * @var array cached localized labels
     */
    private static $cachedLocalLabels;

    /**
     * Fetches translated label from the relationLabels property or relation model.
     * @param \yii\db\ActiveQuery $activeRelation
     * @param string $relation
     * @return string
     */
    public function getRelationLabel($activeRelation, $relation)
    {
        $modelClass = $activeRelation->modelClass;
        if (isset($this->cachedRelationLabels[$modelClass][$relation])) {
            return $this->cachedRelationLabels[$modelClass][$relation];
        }
        if (isset($this->relationLabels[$relation])) {
            $label = $this->relationLabels[$relation];
        } else {
            /** @var ActiveRecord $relationModel */
            $relationModel = new $modelClass;
            /** @var ActiveRecord $model */
            $model = $this->owner;
            $label = $model->getAttributeLabel($relation) ? : $relationModel->getCrudLabel($activeRelation->multiple ? 'relation' : 'default');
        }
        return $this->cachedRelationLabels[$modelClass][$relation] = $label;
    }

    private function getLocalLabels()
    {
        if (self::$cachedLocalLabels !== null && isset(self::$cachedLocalLabels[$this->modelClass])) {
            return self::$cachedLocalLabels[$this->modelClass];
        }
        return self::$cachedLocalLabels[$this->modelClass] = call_user_func($this->localLabels, $this->owner);
    }

}
