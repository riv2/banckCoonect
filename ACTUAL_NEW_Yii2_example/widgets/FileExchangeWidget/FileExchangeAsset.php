<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 17.06.16
 * Time: 10:05
 */

namespace app\widgets\FileExchangeWidget;

use yii\web\AssetBundle;

class FileExchangeAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/FileExchangeWidget';
    
    public $css = [];

    public $js = [
        'js/file-exchange-widget.js',
    ];

    public $depends = [
        'app\assets\AppAsset',
        'kartik\sortable\SortableAsset',
    ];
}