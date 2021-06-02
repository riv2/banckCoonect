<?php

use yii\bootstrap\Html;
use app\widgets\FileExchangeWidget\FileExchangeWidget;
/**
 * @var $this \netis\crud\web\View
 * @var $model \app\components\base\BaseModel
 * @var $controller \app\components\crud\controllers\ActiveController
 * @var $showTitle boolean If set to false <h1> title won't be rendered.
 * @var $exclude array
 * @var $values array
 */

$controller = $this->context;

if ($model instanceof \netis\crud\db\ActiveRecord) {
    if ($this->title === null) {
        $this->title = $model->getCrudLabel('import');
    }
    if ($controller instanceof \yii\base\Controller) {
        $this->params['breadcrumbs'] = $controller->getBreadcrumbs($controller->action, $model);
        $this->params['menu'] = $controller->getMenu($controller->action, $model);
    }
}

echo netis\crud\web\Alerts::widget();

if (!isset($showTitle) || $showTitle) {
    echo '<h1><span>' . Html::encode($this->title) . '</span></h1>';
}

echo FileExchangeWidget::widget([
    'model'         => $model,
    'values'        => $values,
    'exclude'       => $exclude,
]);