<?php
use app\models\reference\Project;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var ActiveForm $form
 * @var Project $model
 * @var array $groupedProjectItems
 */

?>
<div class="panel panel-primary">
    <div class="panel-heading">Ассортимент проекта
        <?=Html::a('Смотреть весь',
            $model->nomenclature_document_id
                ? ['/nomenclature-document/update', 'id' => $model->nomenclature_document_id]
                : ['/crud-project-item', 'ProjectItem[project_id]' => $model->id], [
            'class' => 'pull-right text-white',
            'style' => 'color: white;',
            'target' => '_blank',
        ]);?>
    </div>
    <div class="panel-body">
        <?php if (count($groupedProjectItems) > 0) { ?>
            <table class="table table-dark-b">
                <thead>
                <tr>
                    <th width="1">Бренд</th>
                    <th></th>
                    <th>Категория</th>
                    <th>Кол-во SKU</th>
                    <th><input type="checkbox" id="rrp-all-check" /> Рег. РРЦ</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($groupedProjectItems as $brandId => $brand) {
                    $catsKeys = array_keys($brand['categories']);
                    $firstCategoryId = reset($catsKeys);
                    ?>
                    <tr>
                        <td rowspan="<?=count($brand['categories'])?>" valign="middle" style="vertical-align: middle;">
                            <?=$brand['brand_name'];?>
                        </td>
                        <td rowspan="<?=count($brand['categories'])?>" valign="middle" align="left">
                            <?php
                                echo Html::a('<i class="fa fa-play-circle-o" ></i> Собрать цены', [
                                    '/parsing-project/execute',
                                    'id'            => null,
                                    'regions'       => $model->region_id,
                                    'projects'      => $model->id,
                                    'brands'        => $brandId,
                                ], [
                                    'class'     => "btn btn-info btn-sm",
                                ]);
                            ?>
                        </td>
                        <td>
                            <?=$brand['categories'][$firstCategoryId]['category_name']?>
                        </td>
                        <td>
                            <?=$brand['categories'][$firstCategoryId]['count']?>
                        </td>
                        <td>
<!--                            --><?//= Html::checkbox("", $brand['categories'][$firstCategoryId]['rrp_regulations'], [
//                                'data-name'         => "GroupedProjectItem[$brandId][$firstCategoryId][rrp_regulations]",
//                                'data-brand_id'     => $brandId,
//                                'data-category_id'  => $firstCategoryId,
//                                'data-state'        => $brand['categories'][$firstCategoryId]['rrp_regulations']?1:($brand['categories'][$firstCategoryId]['rrp_regulations']===null?-1:0),
//                                'class'             => 'project_item-rrp_regulations-checkbox'
//                            ])?>
                        </td>
                    </tr>
                    <?php
                    foreach ($brand['categories'] as $categoryId => $category) {
                        if ($categoryId == $firstCategoryId) continue;
                        ?>
                        <tr>
                            <td class="table-light-b">
                                <?=$category['category_name']?>
                            </td>
                            <td class="table-light-b">
                                <?=$category['count']?>
                            </td>
                            <td class="table-light-b">
<!--                                --><?//= Html::checkbox("", $category['rrp_regulations'], [
//                                    'data-name'         => "GroupedProjectItem[$brandId][$categoryId][rrp_regulations]",
//                                    'data-brand_id'     => $brandId,
//                                    'data-category_id'  => $categoryId,
//                                    'data-state'        => $category['rrp_regulations']?1:($category['rrp_regulations']===null?-1:0),
//                                    'class'             => 'project_item-rrp_regulations-checkbox'
//                                ])?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>