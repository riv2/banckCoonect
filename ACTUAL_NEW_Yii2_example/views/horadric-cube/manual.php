<?php
use yii\widgets\LinkPager;
use yii\helpers\Html;
use app\widgets\FormBuilder;
/**
 * @var array $groups
 * @var array $competitors
 * @var array $categories
 * @var array $brands
 * @var \app\components\DataProvider $dataProvider
 */
$request = Yii::$app->request;

?>
<h1>Ручной разбор</h1>

<form class="form form-inline">
    <div class="row">
        <div class="col-xs-3" style="margin-bottom: 10px;">
            <?php
            echo \maddoger\widgets\Select2::widget([
                'name'          => 'competitors',
                'value'         => $request->get('competitors'),
                'clientOptions' => [
                    'width'             => '100%',
                    'allowClear'        => true,
                    'closeOnSelect'     => true,
                    'minimumInputLength' => 0,
                    'multiple'           => true,
                    'data'              => $competitors,
                ],
                'clientEvents' => null,
                'options' => [
                    'id'            =>  'competitors',
                    'class'         => 'select2',
                    'value'         => $request->get('competitors'),
                    'placeholder'   => 'Конкуренты',
                ],
            ]);
            ?>
        </div>
        <div class="col-xs-3" style="margin-bottom: 10px;">
            <?php
            echo \maddoger\widgets\Select2::widget([
                'name'          => 'brands',
                'value'         => $request->get('brands'),
                'clientOptions' => [
                    'width'             => '100%',
                    'allowClear'        => true,
                    'closeOnSelect'     => true,
                    'minimumInputLength' => 0,
                    'multiple'           => true,
                    'data'              => $brands,
                ],
                'clientEvents' => null,
                'options' => [
                    'id'            =>  'brands',
                    'class'         => 'select2',
                    'value'         => $request->get('brands'),
                    'placeholder'   => 'Бренды',
                ],
            ]);
            ?>
        </div>
        <div class="col-xs-3" style="margin-bottom: 10px;">
            <?php
            echo \maddoger\widgets\Select2::widget([
                'name'          => 'categories',
                'value'         => $request->get('categories'),
                'clientOptions' => [
                    'width'             => '100%',
                    'allowClear'        => true,
                    'closeOnSelect'     => true,
                    'minimumInputLength' => 0,
                    'multiple'           => true,
                    'data'              => $categories,
                ],
                'clientEvents' => null,
                'options' => [
                    'id'            => 'categories',
                    'class'         => 'select2',
                    'value'         => $request->get('categories'),
                    'placeholder'   => 'Категории',
                ],
            ]);
            ?>
        </div>

        <div class="col-xs-2 text-left">
            <?=Html::submitButton('Применить',['class' => 'btn btn-primary']);?>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-8">

            <?php
            echo LinkPager::widget([
                'pagination' => $dataProvider->pagination,
                'pageCssClass' => 'horadric-pagination',
                'prevPageCssClass' => 'prev',
                'nextPageCssClass' => 'next',
            ]);
            ?>

        </div>
        <div class="col-sm-4" style="line-height: 30px;">
            <?=$dataProvider->pagination->pageSize?>  на странице, <?=$dataProvider->pagination->pageCount?> страниц, <?=$dataProvider->pagination->totalCount?> групп
        </div>
    </div>

</form>



<div style="overflow: hidden; margin-top: 10px;" class="row">
<?php foreach ($groups as $group): ?>
<?php
    /**
     * @var \app\models\register\HoradricCube[] $matches
     * @var \app\models\reference\Competitor[] $competitor
     */
    $competitor = $group['competitor'];
    $matches    = $group['matches'];
 ?>
<div class="horadric-cube col-xs-12"  data-competitor_id="<?=$group['competitor_id']?>" data-item_id="<?=$group['item_id']?>">
    <table class="table">
        <thead>
            <tr>
                <th><?=Html::a('<i class="fa fa-close"></i> Нет', ['#'], [
                        'class' => 'btn btn-danger matching-wrong',
                    ]);?></th>
                <th>Процент</th>
                <th><nobr>Совпад.</nobr></th>
                <th width="100%">
                    <div class="horadric-comp-name"><?=$competitor ? $competitor->name:null?></div>
                    <?=$group['itemName']?></th>
                <th>Бренд</th>
                <th><nobr>Цена ВИ</nobr></th>
                <th><nobr>Цена кон.</nobr></th>
                <th><?=Html::a('<i class="fa fa-calendar"></i> Отложить', ['#'], [
                        'class' => 'btn btn-default matching-later',
                    ]);?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($matches as $match): ?>
            <tr data-horadric_id="<?=$match->id?>">
                <td><?=Html::a('<i class="fa fa-check-circle-o"></i> Да&nbsp;', ['#'], [
                        'class' => 'btn btn-default matching-ok',
                        //'style' => 'background-color: '.\app\models\register\HoradricCube::getColorString($match->percent,0.15).';',
                    ]);?></td>
                <td><?=$match->renderPercent()?></td>
                <td><?=$match->predict?></td>
                <td><?=$match->renderCompetitorItemName()?></td>
                <td><nobr><?=$match->brand ? $match->brand->name : null ?></nobr></td>
                <td><nobr><?=$match->vi_item_price ? number_format($match->vi_item_price,0,',', ' ') : null ?> ₽</nobr></td>
                <td><nobr><?=$match->competitor_item_price ?  number_format($match->competitor_item_price ,0,',', ' '): null ?> ₽</nobr></td>
                <td><?=$match->sales_rank?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>
    </div>


<?php
echo LinkPager::widget([
    'pagination' => $dataProvider->pagination,
    'pageCssClass' => 'feedback-page',
    'prevPageCssClass' => 'feedback-page prev',
    'nextPageCssClass' => 'feedback-page next',
]);
?>


<?php

$this->registerJs(<<<JS
    $(function() {

        $(document).on('click','.matching-ok', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var hc  = $(this).parents('.horadric-cube').first();
            var row = $(this).parents('tr').first();
            $.ajax({
                'url': '/horadric-cube/ok-all',
                'data': {
                    id:  row.attr('data-horadric_id'),
                },
                'type': 'get',
                'dataType':'json',
                'success': function(json) {
                    setTimeout(function() {
                        if($('.horadric-cube:visible').length === 0){
                            window.location.reload();
                        }
                    },300);
                }
            });
            hc.slideUp();
            return false;
        });
        
        $(document).on('click','.matching-wrong', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var hc = $(this).parents('.horadric-cube').first();
            $.ajax({
                'url': '/horadric-cube/wrong-all',
                'data': {
                    competitor_id:  hc.attr('data-competitor_id'),
                    item_id:        hc.attr('data-item_id')
                },
                'type': 'get',
                'dataType':'json',
                'success': function(json) {
                    setTimeout(function() {
                        if($('.horadric-cube:visible').length === 0){
                            window.location.reload();
                        }
                    },300);
                }
            });
            hc.slideUp();
            return false;
        });

        $(document).on('click','.matching-later', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var hc = $(this).parents('.horadric-cube').first();
            $.ajax({
                'url': '/horadric-cube/later-all',
                'data': {
                    competitor_id:  hc.attr('data-competitor_id'),
                    item_id:        hc.attr('data-item_id')
                },
                'type': 'get',
                'dataType':'json',
                'success': function(json) {
                    setTimeout(function() {
                        if($('.horadric-cube:visible').length === 0){
                            window.location.reload();
                        }
                    },300);
                }
            });
            hc.slideUp();
            return false;
        });
        
       
    });
JS
);