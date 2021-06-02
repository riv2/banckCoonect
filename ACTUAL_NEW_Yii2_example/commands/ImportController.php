<?php
namespace app\commands;

use app\components\exchange\Exchange;
use yii\console\Controller;

class ImportController extends Controller
{
    public function actionLdapUsers() {
        if ($this->processIsRun('import/ldap-users') > 2) {
            return;
        }
        Exchange::runImport([
            'Users'             => [],
        ]);
    }

    public function actionPformerTypes() {
        if ($this->processIsRun('import/pformer-types') > 2) {
            return;
        }
        Exchange::runImport([
            'PriceFormerTypes'  => [],
        ]);
    }

    public function actionPhubBrands() {
        if ($this->processIsRun('import/phub-brands') > 2) {
            return;
        }
        Exchange::runImport([
            'Brands'            => [
                'queue'         => true,
                'importQueue'   => 1000
            ],
        ]);
    }


    public function actionPhubCategories() {
        if ($this->processIsRun('import/phub-categories') > 2) {
            return;
        }
        Exchange::runImport([
            'Categories'        => [
                'queue'         => true,
                'importQueue'   => 1000
            ],
        ]);
    }

    public function actionPhubEnqueueItems() {
        if ($this->processIsRun('import/phub-enqueue-items') > 2) {
            return;
        }
        Exchange::runImport([
            'Items'        => []
        ]);
    }

    public function actionPhubItems() {
        if ($this->processIsRun('import/phub-items') > 2) {
            return;
        }
        for ($i = 0; $i < 5; $i ++) {
            Exchange::runImport([
                'Items' => [
                    'autoEnqueue' => false,
                    'importQueue' => 1000
                ]
            ]);
        }
    }

    public function actionImportItem($id) {
        Exchange::runImport([
            'Items' => ['importIds' => [$id], 'forced' => true]
        ]);
    }

    /**
     * Проверяем, что процесс запущен
     * @param string $processName Имя процесса
     * @return bool
     * @return boolean
     */
    public function processIsRun($processName)
    {
        $result = [];
        exec("ps aux | grep -v grep | grep \"$processName\"", $result);
        $count = count($result);
        return ceil($count);
    }

}