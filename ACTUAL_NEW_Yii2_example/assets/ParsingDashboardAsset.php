<?php
namespace app\assets;

use yii\web\AssetBundle;

class ParsingDashboardAsset extends AssetBundle
{
    public $sourcePath = '@app/views/site/js';

    public $css = [
    ];

    public $js = [
        'parsing.dashboard.js',
    ];

    public $depends = [
        'app\assets\AppAsset',
    ];

}
