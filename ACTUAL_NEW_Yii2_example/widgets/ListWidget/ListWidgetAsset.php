<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 17.06.16
 * Time: 10:05
 */

namespace app\widgets\ListWidget;

use yii\web\AssetBundle;

class ListWidgetAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/ListWidget';
    
    public $css = [];

    public $js = [
        'js/list.js',
    ];

    public $depends = [
        'app\assets\AppAsset',
        'app\assets\JquerySortableAsset',
    ];
}