<?php
/**
 * @var \app\components\base\BaseModel $model
 * @var string $widgetId
 * @var \app\widgets\ListWidget\ListWidget $widget;
 */

use app\widgets\FormBuilder;
use yii\bootstrap\Html;

\app\widgets\ListWidget\ListWidgetAsset::register($this);

$sortable = $widget->sortable ? 'true' : 'false';

$this->registerJs("$('#$widgetId').listWidget({'inputName':'{$widget->inputName}', 'attribute':'{$widget->attribute}','validateRegexp':'{$widget->validateRegexp}','sortable':$sortable});");


$attribute = $widget->attribute;

?>

<div class="panel panel-default"  id="<?=$widgetId?>">
    <div class="panel-heading"><strong><?=$widget->title?></strong></div>
        <div class="panel-body list-grid">
            <div class="list-grid-head list-widget-new-record">
                <div class="right-list-block">
                    <?php
                    if ($widget->addOn && is_callable($widget->addOn)) {
                        echo Html::beginTag('div',[
                            'class' => 'list-widget-addOn-template',
                            'style' => 'display:none;'
                        ]);
                        call_user_func($widget->addOn, null, $widget->inputName, '__index__', $widget->addOnParam);
                        echo Html::endTag('div');
                    }
                    ?>
                    <button class="btn btn-info list-widget-add-btn" type="button"><span class="glyphicon glyphicon-plus-sign"></span></button>
                </div>
                <div class="left-list-block">
                    <?php
                    if (in_array($widget->attribute, $model->relations())) {
                        echo FormBuilder::relation($this, $model, $widget->attribute, false, '');
                    } else {
                        echo Html::input('text', '', '', ['class' => 'form-control']);
                    }
                    ?>
                </div>
            </div>

            <ul class="list-grid-ul">
            <?php foreach ($widget->items as $i => $item) { ?>
                <li data-id="<?=base64_encode(in_array($attribute, $model->relations()) ? $item->$attribute->id : $item->$attribute)?>">
                    <div class="right-list-block">
                        <?php
                        if (in_array($widget->attribute, $model->relations())) {
                            echo Html::input('hidden', $widget->inputName.'['.$i.']['.$attribute.']', $item->$attribute->id);
                        } else {
                            echo Html::input('hidden', $widget->inputName.'['.$i.']['.$attribute.']', $item->$attribute);
                        }
                        if ($widget->addOn && is_callable($widget->addOn)) {
                            call_user_func($widget->addOn, $item, $widget->inputName, $i, $widget->addOnParam);
                        }
                        ?>
                        <button class="btn btn-danger btn-xs list-widget-delete-btn" type="button"><span class="glyphicon glyphicon-trash"></span></button>
                    </div>
                    <div class="left-list-block">
                        <?=$item->$attribute?>
                    </div>
                </li>
            <?php }?>
            </ul>
        </div>
</div>
