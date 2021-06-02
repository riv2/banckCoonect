<?php
namespace app\commands;

use app\components\base\Entity;
use app\models\enum\FileFormat;
use app\models\enum\Region;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\pool\LogKpi;
use app\models\pool\PriceRefined;
use app\models\reference\CompetitorItem;
use app\models\reference\ParsingProject;
use app\models\register\FileExchange;
use creocoder\flysystem\FtpFilesystem;
use yii;
use yii\console\Controller;
use yii\helpers\Json;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Settings;

class ProanalyticsController extends Controller
{
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

    /**
     * @param null $date
     * @throws yii\db\IntegrityException
     */
    public function actionLoadPrices($date = null)
    {
        if (!$date) {
            $date = date('Ymd');
        }
        /** @var FtpFilesystem $fs*/
        $fs = Yii::$app->fs;

        // beru
        $filename = 'report_' . $date . '.csv';
        $remoteFilepath = '/out/' . $filename;
        if ($fs->has($remoteFilepath)) {
            $content = $fs->read($remoteFilepath);
            if (!file_exists(sys_get_temp_dir() . '/web/')) {
                mkdir(sys_get_temp_dir() . '/web/');
            }
            file_put_contents(sys_get_temp_dir() . '/web/' . $filename, $content);

            $entityId = Entity::getIdByClassName(PriceRefined::className());
            $fileExchange                     = new FileExchange;
            $fileExchange->name               = 'Загрузка цен БеруРу от Проаналитики';
            $fileExchange->entity_id          = $entityId;
            $fileExchange->is_export          = false;
            $fileExchange->encoding           = 'UTF-8';
            $fileExchange->file_path          = sys_get_temp_dir() . '/web/' . $filename;
            $fileExchange->file_format_id     = FileFormat::TYPE_CSV;
            $fileExchange->task_status_id     = TaskStatus::STATUS_QUEUED;
            $fileExchange->created_user_id = 6666;
            $fileExchange->updated_user_id = 6666;
            $fileExchange->setImportSettings([
                'entity_id' => $entityId,
                'skip_first_row' => true,
                'auto_mapping' => false,
                'columns_order' => 'http404,price,item_id,competitor_item_sku,competitor_item_name,out_of_stock,url,delivery_days',
                'columns_values' => Json::encode([
                    'source_id' => Source::SOURCE_WEBSITE,
                    'competitor_id' => '53fb2711-17cc-416a-840b-00ad8ee7dade',
                    'regions' => '2,6,4,7,8,9,10,11,12,13,14,15,16,20,21,23,33,35,36,37,38,39,41,42,43,44,45,46,47,48,49,50,51,53,54,55,56,191,193,194,195,235,239,240,968,970,971,972,10649,10838,11127,11147,11168,1,24,5,25,192,236,10664,10668,10830,10839,10951,11053,11067,172,66,65,18',
                    'competitor_shop_name' => 'Беру.ру',
                    'parsing_project_id' => '752eeb7a-0d5b-4ef5-b53c-bfa17a7008d4',
                ]),
                'preset_columns' => 'source_id,competitor_id,regions,competitor_shop_name',
                'exclude_columns' => ',,price_parsed_id,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,parsing_project_id,,competitor_item_seller,parsing_id,,,,,,,,,,,,id,,extracted_at,created_at,,,,,,,,,,,,,,,',
                'file_format_id' => FileFormat::TYPE_CSV,
                'status_id' => Status::STATUS_ACTIVE,
                'is_export' => false,
                'encoding' => 'UTF-8',
                'data_source' => 'file',
            ]);
            $fileExchange->save();
        }

        // leroy
        $filename = 'report_leroy_' . $date . '.csv';
        $remoteFilepath = '/out/' . $filename;
        if ($fs->has($remoteFilepath)) {
            $content = $fs->read($remoteFilepath);
            if (!file_exists(sys_get_temp_dir() . '/web/')) {
                mkdir(sys_get_temp_dir() . '/web/');
            }
            file_put_contents(sys_get_temp_dir() . '/web/' . $filename, $content);

            $entityId = Entity::getIdByClassName(PriceRefined::className());
            $fileExchange                     = new FileExchange;
            $fileExchange->name               = 'Загрузка цен Леруа от Проаналитики';
            $fileExchange->entity_id          = $entityId;
            $fileExchange->is_export          = false;
            $fileExchange->encoding           = 'UTF-8';
            $fileExchange->file_path          = sys_get_temp_dir() . '/web/' . $filename;
            $fileExchange->file_format_id     = FileFormat::TYPE_CSV;
            $fileExchange->task_status_id     = TaskStatus::STATUS_QUEUED;
            $fileExchange->created_user_id = 6666;
            $fileExchange->updated_user_id = 6666;
            $fileExchange->setImportSettings([
                'entity_id' => $entityId,
                'skip_first_row' => true,
                'auto_mapping' => false,
                'columns_order' => 'http404,price,item_id,competitor_item_sku,competitor_item_name,out_of_stock,url,delivery_days,regions',
                'columns_values' => Json::encode([
                    'source_id' => Source::SOURCE_WEBSITE,
                    'competitor_id' => 'd9287ded-7f05-4a43-9a77-5e05605d4a23',
                    'competitor_shop_name' => 'leroymerlin.ru',
                    'parsing_project_id' => 'be6f3059-b89e-444c-b00c-83a6ba17fb1e',
                ]),
                'preset_columns' => 'source_id,competitor_id,regions,competitor_shop_name',
                'exclude_columns' => ',,price_parsed_id,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,parsing_project_id,,competitor_item_seller,parsing_id,,,,,,,,,,,,id,,extracted_at,created_at,,,,,,,,,,,,,,,',
                'file_format_id' => FileFormat::TYPE_CSV,
                'status_id' => Status::STATUS_ACTIVE,
                'is_export' => false,
                'encoding' => 'UTF-8',
                'data_source' => 'file',
            ]);
            $fileExchange->save();
        }

        // citilink
        $filename = 'report_citilink_' . $date . '.csv';
        $remoteFilepath = '/out/' . $filename;
        if ($fs->has($remoteFilepath)) {
            $content = $fs->read($remoteFilepath);
            if (!file_exists(sys_get_temp_dir() . '/web/')) {
                mkdir(sys_get_temp_dir() . '/web/');
            }
            file_put_contents(sys_get_temp_dir() . '/web/' . $filename, $content);

            $entityId = Entity::getIdByClassName(PriceRefined::className());
            $fileExchange                     = new FileExchange;
            $fileExchange->name               = 'Загрузка цен Ситилинк от Проаналитики';
            $fileExchange->entity_id          = $entityId;
            $fileExchange->is_export          = false;
            $fileExchange->encoding           = 'UTF-8';
            $fileExchange->file_path          = sys_get_temp_dir() . '/web/' . $filename;
            $fileExchange->file_format_id     = FileFormat::TYPE_CSV;
            $fileExchange->task_status_id     = TaskStatus::STATUS_QUEUED;
            $fileExchange->created_user_id = 6666;
            $fileExchange->updated_user_id = 6666;
            $fileExchange->setImportSettings([
                'entity_id' => $entityId,
                'skip_first_row' => true,
                'auto_mapping' => false,
                'columns_order' => 'http404,price,item_id,competitor_item_sku,competitor_item_name,out_of_stock,url,delivery_days,regions',
                'columns_values' => Json::encode([
                    'source_id' => Source::SOURCE_WEBSITE,
                    'competitor_id' => '5dda2a3b-a633-45e5-bccf-7e8dc960a3df',
                    'competitor_shop_name' => 'citilink.ru',
                    'parsing_project_id' => 'cfb2a2bd-a846-4b13-9b13-9e91fb710dd4',
                ]),
                'preset_columns' => 'source_id,competitor_id,regions,competitor_shop_name',
                'exclude_columns' => ',,price_parsed_id,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,parsing_project_id,,competitor_item_seller,parsing_id,,,,,,,,,,,,id,,extracted_at,created_at,,,,,,,,,,,,,,,',
                'file_format_id' => FileFormat::TYPE_CSV,
                'status_id' => Status::STATUS_ACTIVE,
                'is_export' => false,
                'encoding' => 'UTF-8',
                'data_source' => 'file',
            ]);
            $fileExchange->save();
        }
    }

    /**
     * Выгрузка последнего списка урлов от Беру к проаналитике
     */
    public function actionUploadUrls()
    {
        $beruRuCompetitor = \app\models\reference\Competitor::findOne('53fb2711-17cc-416a-840b-00ad8ee7dade');
        $kpiData = (new yii\db\Query())
            ->select(new \yii\db\Expression('json_agg(CONCAT(url, \'|\', item_id))'))
            ->from(LogKpi::tableName())
            ->andWhere([
                'project_id' => '7ed41eb9-6ec3-44ed-83a3-e82eb707def9',
                'competitor_id' => $beruRuCompetitor->primaryKey,
            ])
            ->groupBy('project_execution_id')
            ->limit(1)
            ->scalar();
        $kpiData = json_decode($kpiData);
        $excel = new PHPExcel();
        PHPExcel_Settings::setLocale('ru');
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        foreach($kpiData as $index => $dataString) {
            [$urls, $item_id] = explode('|', $dataString);
            $itemCellValue = $item_id;
            $urlCellValue = explode('?', str_replace(['{', '}'], '', explode(',', $urls)[0]))[0];
            {
                $matches = [];
                preg_match('/\/[0-9]+$/', $urlCellValue, $matches);
                if (count($matches) > 0) {
                    $url = CompetitorItem::find()->select('url')->andWhere('url LIKE \'%' . $matches[0] . '\'')->limit(1)->scalar();
                    if ($url) {
                        $urlCellValue = $url;
                    }
                }
            }
            var_dump($urlCellValue);
            $sheet->setCellValueByColumnAndRow(0, $index, $itemCellValue);
            $sheet->setCellValueByColumnAndRow(1, $index, $urlCellValue);
            $sheet->setCellValueByColumnAndRow(2, $index, $beruRuCompetitor->name);
        }
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save(sys_get_temp_dir() . '/web/beru_ru_urls.xlsx');
        Yii::$app->fs->put(
            '/in/beru_ru_urls.xlsx',
            file_get_contents(sys_get_temp_dir() . '/web/beru_ru_urls.xlsx')
        );

        $leroyParsingProject = ParsingProject::findOne(['name' => 'leroymerlin-msk_card']);
        $regions = Region::find()
            ->select(['id', 'name'])
            ->andWhere([
                'id' => [
                    38, 10951, 193, 6, 4, 2, 39,
                    35, 43, 54, 172, 55, 47, 56,
                    44, 46, 8, 1,
                ]
            ])
            ->asArray()
            ->all();

        $excel = new PHPExcel();
        PHPExcel_Settings::setLocale('ru');
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $rowIndex = 0;
        foreach ($regions as $region) {
            $urls = $leroyParsingProject->execute(['regions' => [$region['id']]], true);
            foreach ($urls as $url) {
                var_dump($url['url']);

                $sheet->setCellValueByColumnAndRow(0, $rowIndex, $url['attributes']['item_id']);
                $sheet->setCellValueByColumnAndRow(1, $rowIndex, $url['url']);
                $sheet->setCellValueByColumnAndRow(2, $rowIndex, $region['name']);
                $rowIndex++;
            }
        }
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save(sys_get_temp_dir() . '/web/leroy_urls.xlsx');
        Yii::$app->fs->put(
            '/in/leroy_urls.xlsx',
            file_get_contents(sys_get_temp_dir() . '/web/leroy_urls.xlsx')
        );

        $citilinkParsingProject = ParsingProject::findOne(['id' => 'cfb2a2bd-a846-4b13-9b13-9e91fb710dd4']);

        $excel = new PHPExcel();
        PHPExcel_Settings::setLocale('ru');
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $rowIndex = 0;
        foreach ($citilinkParsingProject->regions as $region) {
            $urls = $citilinkParsingProject->execute(['regions' => [$region->id]], true);
            foreach ($urls as $url) {
                var_dump($url['url']);

                $sheet->setCellValueByColumnAndRow(0, $rowIndex, $url['attributes']['item_id']);
                $sheet->setCellValueByColumnAndRow(1, $rowIndex, $url['url']);
                $sheet->setCellValueByColumnAndRow(2, $rowIndex, $region->name);
                $rowIndex++;
            }
        }
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save(sys_get_temp_dir() . '/web/citilink_urls.xlsx');
        Yii::$app->fs->put(
            '/in/citilink_urls.xlsx',
            file_get_contents(sys_get_temp_dir() . '/web/citilink_urls.xlsx')
        );

        Yii::$app->mailer
            ->compose()
            ->setHtmlBody('Добрый день! Только что мы выгрузили урлы номенклатур, просьба загрузить их с вашей стороны.')
            ->setFrom('dev.email.ct@gmail.com')
            ->setTo([
                'lukoyanovas@cloud-team.ru',
                'kristina.volosnikova@proanalytics.ru',
                'yury.sergeev@proanalytics.ru',
            ])
            ->setSubject('ВсеИнструменты.ру: уведомление о выгрузке урлов номенклатур')
            ->send();
    }
}