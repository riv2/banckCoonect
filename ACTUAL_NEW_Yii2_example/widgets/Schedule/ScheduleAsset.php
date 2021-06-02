<?php
namespace app\widgets\Schedule;

use yii\web\AssetBundle;

class ScheduleAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/Schedule';

    public $css = [

    ];

    public $js = [
        'js/schedule.js',
    ];

    public $depends = [
        'app\assets\AppAsset',
        'yii2fullcalendar\CoreAsset',
    ];
}