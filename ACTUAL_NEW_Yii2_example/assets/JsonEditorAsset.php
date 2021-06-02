<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class JsonEditorAsset
 * @package common\assets
 */
class JsonEditorAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/jsoneditor';

    public $js = [
        'jsoneditor.js',
    ];

    public $css = [
        'jsoneditor.css',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
