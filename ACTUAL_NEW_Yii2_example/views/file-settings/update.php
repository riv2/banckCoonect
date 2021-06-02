<?php

use app\models\reference\Masks;
use app\validators\TimeSpanValidator;
use yii\bootstrap\Html;
use app\models\cross\ParsingProjectRegion;
use app\models\enum\Source;
use app\widgets\FormBuilder;
use kartik\form\ActiveForm;
use \app\widgets\ListWidget\ListWidget;
use yii\bootstrap\Modal;
use yii\helpers\Url;


/**
 * @var $controller netis\crud\crud\ActiveController
 */
/** @var $this \netis\crud\web\View */
/** @var \app\models\reference\FileProcessingSettings $model */
/** @var $form yii\widgets\ActiveForm */

$controller = $this->context;

if (!$model->isNewRecord) {
    $this->title = $model->__toString();
}

if ($controller instanceof \yii\base\Controller) {
    $this->params['breadcrumbs'] = $controller->getBreadcrumbs($controller->action, $model);
    $this->params['menu']        = $controller->getMenu($controller->action, $model);
}
/** @var \app\components\processing\ExcelOpt220 $settingsClass */
$settingsClass = $model->class;
$settingsBuilder = $settingsClass::settingsBuilder();
?>

<?= netis\crud\web\Alerts::widget() ?>

<?php
$form = ActiveForm::begin([
    'validateOnSubmit'      => true,
    'enableAjaxValidation'  => false,
    'enableClientValidation' => true,
    'method' => 'post',
    'options' => [
        'enctype' => 'multipart/form-data',
    ]
]);
?>


    <?php if ($model->isNewRecord): ?>
        <h1>Новый тип оработки</h1>
    <?php else: ?>
        <h1><span><?= Html::encode($model->name) ?></span></h1>
    <?php endif; ?>

    <div class="box">
        <div class="box-body">

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->input('text') ?>

                    <?php
                        foreach ($settingsBuilder as $key => $item) {
                            $name = "FileProcessingSettings[settings][{$key}]";
                            $val = isset($model->settings[$key]) ? $model->settings[$key] : $item['value'];
                            echo Html::beginTag('div', [
                                    'class' => 'form-group'
                            ]);
                            echo Html::tag('label', $item['label']);
                            if ($item['input'] == 'select2') {
                                echo FormBuilder::renderSelect2($this, $item['class'], $name, $val, false);
                            }
                            if (in_array($item['input'] ,['text','number'])) {
                                echo Html::input($item['input'], $name, $val, [
                                        'class' => 'form-control'
                                ]);
                            }
                            if ($item['input'] == 'seconds') {
                                echo Html::dropDownList( $name, $val, [
                                    8 * 3600 => '8 часов',
                                    8 * 3600 => '12 часов',
                                    24 * 3600 => '1 сутки',
                                    48 * 3600 => '2 суток',
                                    72 * 3600 => '3 суток',
                                ], [
                                    'class' => 'form-control'
                                ]);
                            }
                            echo Html::endTag('div');
                        }
                    ?>

                    <?=  Html::submitButton(
                        '<span class="glyphicon glyphicon-floppy-disk"></span> Сохранить',
                        ['class' => 'btn btn-success']
                    ) ?>
                </div>
            </div>
        </div>
    </div>


<?php
$form->end();
?>