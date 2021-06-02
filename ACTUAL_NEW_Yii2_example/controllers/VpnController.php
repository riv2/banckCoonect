<?php
namespace app\controllers;

use app\components\crud\controllers\ActiveController;

class VpnController extends ActiveController
{
    public $modelClass          = 'app\models\reference\Vpn';
    public $searchModelClass    = 'app\models\reference\Vpn';

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $result = parent::actions();
        $result['update']['viewAction'] = 'index';
        return $result;
    }
}