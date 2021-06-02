<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\forms\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use frontend\assets\ApiFormAsset;
use uran1980\yii\assets\jQueryEssential\JqueryCookieAsset;

ApiFormAsset::register($this);
JqueryCookieAsset::register($this);

$formId = 'login-form';
$apiUrl = \yii\helpers\ArrayHelper::getValue(Yii::$app->params,'apiUrl'). '/login';
$frontendUrl = \yii\helpers\ArrayHelper::getValue(Yii::$app->params,'frontendUrl');
$js = <<<JS
let form = $('#{$formId}');
form.apiForm({
    apiUrl: '{$apiUrl}',
    requestType: 'post',
    fieldIdPrefix: 'loginform-'
});
form.on('apiForm.success', function (event, data) {
    $.cookie('access-token', data.accessToken, {
        path: '/'
    });
    $.cookie('full-name', data.first_name + ' ' + data.last_name, {
        path: '/'
    });
    window.location.href = '{$frontendUrl}';
});
JS;

$this->registerJs($js);

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to login:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => $formId]); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <div style="color:#999;margin:1em 0">
                    If you forgot your password you can <?= Html::a('reset it', ['site/request-password-reset']) ?>.
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
