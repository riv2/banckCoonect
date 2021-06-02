<?php
namespace app\assets;

use yii\web\AssetBundle;

class ProjectCompetitorIndexAsset extends AssetBundle
{
    public $sourcePath = '@app/views/project-competitor';

    public $css = [

    ];

    public $js = [
        'js/index.js',
    ];

    public $depends = [
        'app\assets\AppAsset',
    ];

}
