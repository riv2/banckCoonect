<?php

namespace app\modules\ws\assets;

use yii\web\AssetBundle;

/**
 * Class Asset
 *
 * @package app\modules\ws\assets
 */
class Asset extends AssetBundle
{
    public $sourcePath = '@app/modules/ws/assets';
    public $js = [
        'js/socket.io.js',
        'js/ws.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
