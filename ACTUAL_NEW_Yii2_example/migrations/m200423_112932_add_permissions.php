<?php

use app\components\Migration;
use yii\rbac\Permission;


class m200423_112932_add_permissions extends Migration
{
    private $_permissionsToAdd = [
        'app\models\pool\ReportKpi.read' => '',
        'app\models\pool\ReportMatching.read' => '',
        'app\models\pool\PriceCalculated.read' => '',
        'app\models\pool\LogProjectExecution.read' => '',
        'app\models\pool\LogPriceCalculation.read' => '',
        'app\models\pool\PriceParsed.read' => '',
        'app\models\pool\PriceRefined.read' => '',
        'app\models\pool\ParsingError.read' => '',
        'app\models\pool\ParsingBuffer.read' => '',

        'app\models\reference\ProjectTheme.read' => '',
        'app\models\reference\ParsingProject.read' => '',
        'app\models\reference\Masks.read' => '',
        'app\models\reference\Schedule.read' => '',
        'app\models\reference\BrandFilter.read' => '',
        'app\models\reference\FileProcessingSettings.read' => '',
        'app\models\reference\JournalSettings.read' => '',

        'app\models\register\Parsing.read' => '',
        'app\models\register\FileProcessing.read' => '',
        'app\models\register\HoradricCube.read' => '',

        'app\controllers\SiteController.index' => '',
    ];

    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        foreach ($this->_permissionsToAdd as $name => $description) {
            $permission = new Permission([
                'name' => $name,
                'description' => $description
            ]);
            $auth->add($permission);
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        foreach ($this->_permissionsToAdd as $name => $description) {
            $permission = $auth->getPermission($name);
            if ($permission) {
                $auth->remove($permission);
            }
        }
    }

}
