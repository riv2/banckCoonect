<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\forms\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use uran1980\yii\assets\jQueryEssential\JqueryCookieAsset;

JqueryCookieAsset::register($this);

$formId = 'form-signup';
$apiUrl = \yii\helpers\ArrayHelper::getValue(Yii::$app->params,'apiUrl');
$frontendUrl = \yii\helpers\ArrayHelper::getValue(Yii::$app->params,'frontendUrl');
$js = <<<JS
let form = $('#{$formId}');
form.apiForm({
    apiUrl: '{$apiUrl}/register',
    requestType: 'post',
    fieldIdPrefix: 'signupform-'
});
form.on('apiForm.success', function (event, userData) {
    $.ajax({
        url: '{$apiUrl}/login',
        type: 'post',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify(userData),
        success: function (data) {
            $.cookie('access-token', data.accessToken, {
                path: '/'
            });
            $.cookie('email', data.email, {
                path: '/'
            });
            window.location.href = '{$frontendUrl}';
        },
    });
});
JS;

$this->registerJs($js);

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to signup:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'firstName')->textInput() ?>

                <?= $form->field($model, 'lastName')->textInput() ?>

                <?= $form->field($model, 'middleName')->textInput() ?>

                <?= $form->field($model, 'phone') ?>

                <?= $form->field($model, 'password')->passwordInput() ?>


                <div class="form-group">
                    <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
