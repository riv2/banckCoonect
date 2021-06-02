<?php
namespace app\widgets\GridView\columns;

use app\components\base\BaseModel;
use yii;

class DataColumn extends \yii\grid\DataColumn
{
    public function renderHeaderCellContent()
    {
        $content = parent::renderHeaderCellContent();
        if ($this->attribute) {
            $attribute = $this->attribute;
            if($this->grid->filterModel) {
                /** @var BaseModel $filterModel */
                $filterModel = $this->grid->filterModel;
                if (in_array($attribute, $filterModel->relations())) {
                    $relation   = $filterModel->getRelation($attribute);
                    foreach ($relation->link as $left => $right) {
                        $attribute  = $right;
                        break;
                    }
                }
            }
            $content .= '<div class="grid-view-header-attribute">'.$attribute.'</div>';
        } else {
            $content .= '<div class="grid-view-header-attribute">&nbsp;</div>';
        }
        return $content;
    }
}