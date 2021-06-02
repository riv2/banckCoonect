<?php

/**
 * @var $this           netis\crud\web\View
 * @var $dataProvider   yii\data\ActiveDataProvider
 * @var $columns        array
 * @var $searchModel    \app\components\base\BaseModel
 * @var $controller     \app\components\crud\controllers\ActiveController
 * @var $buttons        array
 */

use app\widgets\FormBuilder;

FormBuilder::registerSelect($this);

echo FormBuilder::registerRelations($this);

if (!isset($gridOptions) || !is_array($gridOptions)) {
    $gridOptions = [];
}

echo $this->render('_grid', [
    'gridId'        => 'indexGrid',
    'gridOptions'   => [
        'buttons' => $buttons,
    ],
    'columns'       => $columns,
    'dataProvider'  => $dataProvider,
    'searchModel'   => $searchModel,
], $this->context);