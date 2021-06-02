<?php

use yii\bootstrap\Html;
/**
 * @var $this netis\crud\web\View
 * @var $model \app\models\reference\Project
 * @var $controller netis\crud\crud\ActiveController
 * @var $competitors \app\models\reference\Competitor[]
 */

$controller = $this->context;

$this->title = 'Создать новый проект';
if (!$model->isNewRecord) {
    $this->title = $model->__toString();
}

if ($controller instanceof \yii\base\Controller) {
    $this->params['breadcrumbs'] = $controller->getBreadcrumbs($controller->action, $model);
    $this->params['menu']        = $controller->getMenu($controller->action, $model);
}
?>

<h1><span><?= Html::encode($this->title) ?></span></h1>

<?= netis\crud\web\Alerts::widget() ?>

<?= $this->render('_form', [
    'model'         => $model,
    'competitors'   => $competitors,
], $this->context) ?>