<?php

namespace app\controllers;

use app\components\crud\controllers\ActiveController;
use app\models\enum\Region;
use yii;

class ErrorController extends ActiveController
{
    public $modelClass          = "app\\models\\register\\Error";
    public $searchModelClass    = "app\\models\\register\\Error";
    

}
