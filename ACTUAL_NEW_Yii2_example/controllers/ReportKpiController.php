<?php
namespace app\controllers;

use app\components\crud\controllers\ActiveController;

class ReportKpiController extends ActiveController
{
    public $modelClass          = "app\\models\\pool\\ReportKpi";
    public $searchModelClass    = "app\\models\\pool\\ReportKpi";

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