<?php
namespace app\assets;

use yii\web\AssetBundle;

class ProjectIndexAsset extends AssetBundle
{
    public $sourcePath = '@app/views/project';

    public $css = [

    ];

    public $js = [
        'js/index.js',
    ];

    public $depends = [
        'app\assets\AppAsset',
    ];

}
