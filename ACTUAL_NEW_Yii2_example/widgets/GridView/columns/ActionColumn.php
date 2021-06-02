<?php
namespace app\widgets\GridView\columns;

use app\components\base\BaseModel;
use app\components\base\Entity;
use yii;
use yii\bootstrap\Html;

class ActionColumn extends \yii\grid\ActionColumn
{

    public function renderFilterCellContent()
    {
        $note   = 'CSV';
        $gridId = $this->grid->getId();
        $modelName = $this->grid->filterModel->formName();
        $search = Yii::$app->request->get($modelName);
        $key    = null;
        $class  = 'grid_view-csv_filter-empty';
        $entityId = Entity::getIdByClassName($this->grid->filterModel->className());

        if (isset($search['csv_filter'])) {
            $key        = $search['csv_filter'];
            $fileName   = BaseModel::getCsvFilterName($key);
            $count      = BaseModel::getCsvFilterCount($key);
            if ($count) {
                $note   = $fileName . ' (' . $count . ')';
                $class  = 'grid_view-csv_filter-applied';
            } else {
                $key = null;
            }
        }

        return <<<EOF
        <div class="grid_view-csv_filter $class" id="$gridId-csv_filter" data-grid_id="$gridId" data-entity_id="$entityId">
            <a class="btn btn-danger btn-xs grid_view-remove-csv_filter"><i class="fa fa-times"></i></a>
            <div class="grid_view-csv_filter-note"><i class="glyphicon glyphicon-filter"></i> <span class="grid_view-csv_filter-note-text">$note</span></div>
            <input type="hidden" name="{$modelName}[csv_filter]" class="grid_view-csv_filter-input" data-grid_id="$gridId" id="$gridId-csv_filter-input" value="$key"  />
        </div>
EOF;
    }
    public function renderHeaderCellContent()
    {
        return Html::tag('div', Html::resetButton('Сброс',[
            'class' => 'btn btn-default'
        ]), ['class'=>'text-right']);
    }
}