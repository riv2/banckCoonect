<?php

/**
 * @var $this           netis\crud\web\View
 * @var $dataProvider   yii\data\ActiveDataProvider
 * @var $columns        array
 * @var $searchModel    \app\components\base\BaseModel
 * @var $controller     \app\components\crud\controllers\ActiveController
 * @var $buttons        array
 */

use app\models\reference\Robot;
use app\widgets\FormBuilder;
FormBuilder::registerSelect($this);

echo FormBuilder::registerRelations($this);

if (!isset($gridOptions) || !is_array($gridOptions)) {
    $gridOptions = [];
}

echo $this->render('_grid', [
    'gridId'        => 'indexGrid',
    'gridOptions'   => [
        'buttons' => $buttons,
    ],
    'columns'       => $columns,
    'dataProvider'  => $dataProvider,
    'searchModel'   => $searchModel,
], $this->context);

?>

<?php

$this->registerJs(<<<JS
    $(function() {
        function getRow(el) {
            var tr = $(el).parents('tr').first();
            return {tr:tr, id: tr.find('[data-horadric-item]').attr('data-horadric-item')};
        }
        $(document).on('click','.matching-rollback', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var row = getRow(this);
            $.ajax({
                'url': '/horadric-cube/rollback?id='+row.id,
                'type': 'post',
                'dataType':'json',
                'success': function(json) {
                        if($('[data-horadric-item]').length === 0){
                            $('#indexGrid').yiiGridView('applyFilter');
                        }
                }
            });
                    row.tr.remove();
            return false;
        });
        
        $(document).on('click','.matching-ok', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var row = getRow(this);
            $.ajax({
                'url': '/horadric-cube/ok?id='+row.id,
                'type': 'post',
                'dataType':'json',
                'success': function(json) {
                        if($('[data-horadric-item]').length === 0){
                            $('#indexGrid').yiiGridView('applyFilter');
                        }
                }
            });
                    row.tr.remove();
            return false;
        });
        $(document).on('click','.matching-wrong', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var row = getRow(this);
            $.ajax({
                'url': '/horadric-cube/wrong?id='+row.id,
                'type': 'post',
                'dataType':'json',
                'success': function(json) {
                        if($('[data-horadric-item]').length === 0){
                            $('#indexGrid').yiiGridView('applyFilter');
                        }
                }
            });
                    row.tr.remove();
            return false;
        });
    });
JS
);
//<div style="position: absolute; left: 20px; bottom: 0; padding: 20px 0 0 20px; background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAMCAYAAABWdVznAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAGElEQVR42mNkYGCoZyABMDGQCEY1jBQNAD+jAJe/Rx5TAAAAAElFTkSuQmCC');">
//    <?=$this->render('@app/views/robot/_robots',['robots' => Robot::find()->all()]);?>
<!--</div>-->
