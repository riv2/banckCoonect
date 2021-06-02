<?php
/**
 * @var \app\components\base\BaseModel $searchModel
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var array  $columns
 * @var array  $gridOptions
 * @var string $gridId
 */

use app\widgets\GridView;
use yii\widgets\Pjax;

//
Pjax::begin(['id' => 'indexPjax']);

echo netis\crud\web\Alerts::widget();

$gridId = isset($gridId) ? $gridId : uniqid();
if (!isset($gridOptions) || !is_array($gridOptions)) {
    $gridOptions = [];
}
echo GridView::widget(array_merge([
    'id'             => $gridId,
    'filterModel'    => $searchModel,
    'dataProvider'   => $dataProvider,
    'columns'        => $columns,
], $gridOptions));
//
Pjax::end();

$script = <<<JavaScript
var enterPressed = false;
$(document)
    .off('click', '#$gridId [type="reset"]')
    .on('click','#$gridId [type="reset"]',function(){
        $('#{$gridId}-filters input, #{$gridId}-filters select').each(function(){
            var el = this;
            var field_type = el.type.toLowerCase();
            switch(field_type) {
                case "text": 
                case "password": 
                case "textarea":
                case "hidden":	
                case "number":	
                    el.value = ""; 
                    break;
                case "radio":
                case "checkbox":
                    if (el.checked) {
                        el.checked = false; 
                    }
                    break;
                case "select-one":
                case "select-multi":
                            el.selectedIndex = -1;
                    break;
                default: 
                    break;
            }
        });
        $('#{$gridId}').yiiGridView('applyFilter');
        return false;
    })
    .off('click', '#$gridId .delete-found')
    .on('click','#$gridId .delete-found',function(){
        var btnDelete = $(this);
        if (confirm("Вы правда хотите удалить ВСЕ найденные записи?")){
            if (confirm("И не будете потом спрашивать куда они делись?")){
                var deleteUrl = btnDelete.attr('href')+'&delete=1';
                $.ajax({
                    'url'       : deleteUrl,
                    'type'      : 'get',
                    'dataType'  : 'json',
                    'success'   : function(json){
                        if (json.success) {
                           alert("Удалено: "+json.count);
                            $('#{$gridId}').yiiGridView('applyFilter');
                        }
                    }
                });
            }
        }
    })
    .off('change.yiiGridView keydown.yiiGridView', '#{$gridId}-filters input, #{$gridId}-filters select')
    .on('change.yiiGridView keydown.yiiGridView', '#{$gridId}-filters input, #{$gridId}-filters select', function (event) {
        if (event.type === 'keydown') {
            if (event.keyCode !== 13) {
                return; // only react to enter key
            } else {
                enterPressed = true;
            }
        } else {
            // prevent processing for both keydown and change events
            if (enterPressed) {
                enterPressed = false;
                return;
            }
        }

        $('#{$gridId}').yiiGridView('applyFilter');
        pricing.toggleLoadingState($('#{$gridId} tbody'));
        
        return false;
    })
    .off('change', '#{$gridId}-filters input.select2, #{$gridId}-filters select.select2')
    .on('change', '#{$gridId}-filters input.select2, #{$gridId}-filters select.select2', function (event) {
 
        $('#{$gridId}').yiiGridView('applyFilter');
        pricing.toggleLoadingState($('#{$gridId} tbody'));
        return false;
    });
JavaScript;
$this->registerJs($script);