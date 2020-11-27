<?php

namespace frontend\assets;

use yii\validators\ValidationAsset;
use yii\web\AssetBundle;
use yii\widgets\ActiveFormAsset;

/**
 * Asset bundle для взаимодействия формы с API.
 */
class ApiFormAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@frontend/assets';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/api-form.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        ActiveFormAsset::class,
        ValidationAsset::class,
    ];
}
