<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\widgets\GridView\assets;

use yii\web\AssetBundle;

class GridViewAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/GridView/assets';
    public $js = [
        'js/grid-view.js',
    ];
    public $css = [
        'css/grid-view.css',
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
