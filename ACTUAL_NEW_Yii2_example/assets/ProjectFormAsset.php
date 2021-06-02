<?php
namespace app\assets;

use yii\web\AssetBundle;

class ProjectFormAsset extends AssetBundle
{
    public $sourcePath = '@app/views/project';

    public $css = [

    ];

    public $js = [
        'js/form.js',
    ];

    public $depends = [
        'app\assets\AppAsset',
    ];

}
