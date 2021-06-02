<?php
namespace api\controllers;

use yii\web\Controller;

/**
 * Базовый контроллер.
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}
