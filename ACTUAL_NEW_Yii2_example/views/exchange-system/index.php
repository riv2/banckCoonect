<?php
/**
 * @var \app\models\reference\ExchangeSystem[] $systems
 */
use kartik\form\ActiveForm;
use yii\bootstrap\Html;

?>


<?php foreach ($systems as $system) { ?>
    <?php
        $form = ActiveForm::begin([]);
        echo Html::hiddenInput('id', $system->id);
        $params = $system->getParams();
        $modelName = (new ReflectionClass($system->className()))->getShortName();
        $modelClass = $system->class_name;
        /** @var \app\components\exchange\Exchange $model */
        $model = new $modelClass();
    ?>
    <div class="box box-primary">
        <div class="box-header">
            <?=Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Сохранить' ,[
                'class' => 'btn btn-success pull-right'
            ]);?>
            <h1><?=$system?></h1>
        </div>
        <div class="box-body">
        <?php
        if (Yii::$app->session->getFlash("ExchangeSystem::Saved-".$system->id)) {
            echo Html::beginTag('div', ['class' => 'alert alert-success']);
            echo Yii::$app->session->getFlash("ExchangeSystem::Saved-".$system->id, true);
            echo Html::endTag('div');
        }
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                echo Html::beginTag('div', ['class' => 'panel panel-default']);
                echo Html::beginTag('div', ['class' => 'panel-heading']);
                echo Html::label($model->getLabel($key));
                echo Html::endTag('div');
                echo Html::beginTag('div', ['class' => 'panel-body']);
                foreach ($value as $k => $v) {
                    echo Html::beginTag('div', ['class' => 'form-group']);
                    echo Html::label($k, "$key-$k-$system");
                    echo Html::input('text', $modelName . "[params][$key][$k]", $v, [
                        'class' => 'form-control',
                        'id'    => "$key-$k-$system",
                    ]);
                    echo Html::endTag('div');
                }
                echo Html::endTag('div');
                echo Html::endTag('div');
            } else {
                echo Html::beginTag('div', ['class' => 'form-group']);
                if (strpos($key, 'Enabled') !== false) {
                    echo Html::label(Html::checkbox($modelName . "[params][$key]", $value, [
                        'id'    => "$key-$system",
                    ]). ' ' . $model->getLabel($key), "$key-$system");

                } else if (strpos($key, 'last') === 0) {
                    echo Html::label($model->getLabel($key), "$key-$system");
                    echo Html::beginTag('div'), $value, Html::endTag('div');
                    echo Html::hiddenInput($modelName . "[params][$key]", $value);
                } else {
                    echo Html::label($model->getLabel($key), "$key-$system");
                    echo Html::input('text', $modelName . "[params][$key]", $value, [
                        'class' => 'form-control',
                        'id'    => "$key-$system",
                    ]);
                }
                echo Html::endTag('div');
            }
        }
        if ($system->updated_at) {
            echo Html::beginTag('div');
            echo "Обновлено {$system->updated_at}";
            echo Html::endTag('div');
        }
        if ($system->updatedUser) {
            echo Html::beginTag('div');
            echo "Последние изменения внёс {$system->updatedUser}";
            echo Html::endTag('div');
        }
        ?>
        </div>
    </div>
    <?php
    ActiveForm::end();
    ?>
<?php } ?>
