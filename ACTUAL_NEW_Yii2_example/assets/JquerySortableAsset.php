<?php
namespace app\assets;

use yii\web\AssetBundle;

class JquerySortableAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery-sortable/source/js';

    public $css = [

    ];

    public $js = [
        'jquery-sortable-min.js',
    ];

    public $depends = [
        'app\assets\AppAsset',
    ];

}
