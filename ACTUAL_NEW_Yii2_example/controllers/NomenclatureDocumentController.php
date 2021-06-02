<?php
namespace app\controllers;

use app\components\crud\controllers\ActiveController;

class NomenclatureDocumentController extends ActiveController
{
    public $modelClass          = 'app\models\reference\NomenclatureDocument';
    public $searchModelClass    = 'app\models\reference\NomenclatureDocument';

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $result = parent::actions();
        $result['update']['viewAction'] = 'update';
        return $result;
    }
}
