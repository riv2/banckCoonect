<?php

use yii\bootstrap\Html;
use kartik\form\ActiveForm;


/**
 * @var $this netis\crud\web\View
 * @var $model \app\models\reference\User
 * @var $controller netis\crud\crud\ActiveController
 * @var $competitors \app\models\reference\Competitor[]
 */

$controller = $this->context;

if (!$model->isNewRecord) {
    $this->title = 'Пользователь '.$model->__toString();
}

if ($controller instanceof \yii\base\Controller) {
    $this->params['breadcrumbs'] = $controller->getBreadcrumbs($controller->action, $model);
    $this->params['menu']        = $controller->getMenu($controller->action, $model);
}
?>

<?= netis\crud\web\Alerts::widget() ?>

<?php
$form = ActiveForm::begin([
    'validateOnSubmit'      => true,
    'enableAjaxValidation'  => true,
    'enableClientValidation' => true,
    'method' => 'post',
    'options' => [
        'enctype' => 'multipart/form-data',
    ]
]);
?>

<h1><?= Html::encode($model->username) ?></h1>

<div class="box">
        <table class="box-body table">
            <tbody>
                <tr>
                    <td>
                        ФИО
                    </td>
                    <td>
                        <?= Html::encode($model->firstname) ?> <?= Html::encode($model->lastname) ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: middle">
                        Доступ в Pricing
                    </td>
                    <td class="form-inline" style="vertical-align: middle">
                        <?= $form->field($model, 'is_active')->checkbox([],false)->label(false) ?>
                    </td>
                </tr>
                <tr class="roles" style="display: <?= $model->is_active ? 'table-row' : 'none'?>">
                    <td style="vertical-align: middle">
                        Роли
                    </td>
                    <td class="form-inline" style="width: 68%;">
                        <?php
                        echo \app\widgets\FormBuilder::renderSelect2(
                            $this,
                            \app\models\reference\Role::className(),
                            \yii\helpers\Html::getInputName($model, 'roles'),
                            $model->getRolesIds(),
                            true,
                            0
                        );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: middle">
                        Заблокирован
                    </td>
                    <td class="form-inline" style="vertical-align: middle">
                        <?= $form->field($model, 'is_disabled')->checkbox([],false)->label(false) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Почта
                    </td>
                    <td>
                        <?= Html::encode($model->email) ?>
                    </td>
                </tr>
            <tr>
                <td>

                </td>
                <td>

                    <?=  Html::submitButton(
                        '<span class="glyphicon glyphicon-floppy-disk"></span> Сохранить',
                        ['class' => 'btn btn-success', 'type' => 'submit']
                    ) ?>
                </td>
            </tr>
            </tbody>
        </table>
</div>


<?php
$isActiveInputId = Html::getInputId($model, 'is_active');
$this->registerJs(<<<JS
    $('#$isActiveInputId').on('click', function(ev) {
        $(this).closest('table').find('tr.roles').toggle(this.checked);
    });
JS
);
$form->end();
?>

