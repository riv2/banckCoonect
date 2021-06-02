<?php

use app\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\widgets\Pjax;

/**
 * @var ActiveDataProvider $dataProvider
 * @var string $projectExecutionId
*/
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Расчитанные цены</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <?php
                Pjax::begin();
                echo GridView::widget([
                    'id'             => 'project-price-calcualted',
                    'dataProvider'   => $dataProvider,
                    // this actually renders some widgets and must be called after Pjax::begin()
                    'columns'        => [
                        'item_id',
                        'item',
                        'itemBrand',
                        'price',
                        'itemPriceSupply',
                        'itemPriceRecommendedRetail',
                        'itemPriceDefault',
                        'projectItemRRPR:boolean',
                        'projectItemMinMargin',
                        'created_at:datetime',
                    ]
                ]);
                Pjax::end();
                ?>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>
