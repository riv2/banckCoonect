<?php
namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/views/site';
    public $css = [
        'css/animate.css',
        'css/site.css',
    ];
    public $js = [
        'js/bootstrap-notify.min.js',
        'js/common.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'dmstr\web\AdminLteAsset',
        'app\modules\ws\assets\Asset',
    ];
}
