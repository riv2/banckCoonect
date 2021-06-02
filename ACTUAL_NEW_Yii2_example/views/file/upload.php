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
/** @var \app\models\reference\FileProcessingSettings $fileProcessing */
/** @var $form yii\widgets\ActiveForm */

$controller = $this->context;

if (!$fileProcessing->isNewRecord) {
    $this->title = $fileProcessing->__toString();
}

if ($controller instanceof \yii\base\Controller) {
    $this->params['breadcrumbs'] = $controller->getBreadcrumbs($controller->action, $fileProcessing);
    $this->params['menu']        = $controller->getMenu($controller->action, $fileProcessing);
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


    <div class="box">
        <div class="box-body">

            <div class="row">
                <div class="col-sm-6">
                    <h1>Загрузка длкумента для обработки</h1>
                    <div class="form-group">
                        <label>
                            Тип документа
                        </label>
                        <?= FormBuilder::relation($this, $fileProcessing, 'fileProcessingSettings') ?>
                    </div>
                    <?= $form->field($fileProcessing, 'file')->fileInput() ?>

                    <?=  Html::submitButton(
                        '<span class="fa fa-upload"></span> Загрузить',
                        ['class' => 'btn btn-success']
                    ) ?>
                </div>
            </div>
        </div>
    </div>


<?php
$form->end();
?>