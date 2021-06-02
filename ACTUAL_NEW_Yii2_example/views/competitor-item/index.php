<?php

/**
 * @var $this           netis\crud\web\View
 * @var $dataProvider   yii\data\ActiveDataProvider
 * @var $columns        array
 * @var $searchModel    \app\components\base\BaseModel
 * @var $controller     \app\components\crud\controllers\ActiveController
 * @var $buttons        array
 */

use app\models\reference\Robot;
use app\widgets\FormBuilder;
FormBuilder::registerSelect($this);

echo FormBuilder::registerRelations($this);

if (!isset($gridOptions) || !is_array($gridOptions)) {
    $gridOptions = [];
}

?>
<p>
    <a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['/competitor-item/update-prices'])?>">Обновить названия и цены из спарсенных цен</a>
</p>

<?php
echo $this->render('_grid', [
    'gridId'        => 'indexGrid',
    'gridOptions'   => [
        'buttons' => $buttons,
    ],
    'columns'       => $columns,
    'dataProvider'  => $dataProvider,
    'searchModel'   => $searchModel,
], $this->context);
?>
