<?php
namespace app\controllers;

use app\components\crud\controllers\ActiveController;

class ReportMatchingController extends ActiveController
{
    public $modelClass          = "app\\models\\pool\\ReportMatching";
    public $searchModelClass    = "app\\models\\pool\\ReportMatching";

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'update'        => null,
            'view'          => null,
        ]);
    }
}