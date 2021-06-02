<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\base\Entity;
use app\models\enum\FileFormat;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\pool\PriceRefined;
use app\models\reference\ConsoleTask;
use app\models\register\FileExchange;
use creocoder\flysystem\FtpFilesystem;
use yii\base\BaseObject;
use yii\helpers\Json;

class ProanalyticsLoadPricesTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $date = date('Ymd');

        /** @var FtpFilesystem $fs*/
        $fs = \Yii::$app->fs;

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
                'columns_order' => 'http404,price,item_id,competitor_item_sku,competitor_item_name,out_of_stock,url,delivery_days,competitor_item_seller',
                'columns_values' => Json::encode([
                    'source_id' => Source::SOURCE_WEBSITE,
                    'competitor_id' => '53fb2711-17cc-416a-840b-00ad8ee7dade',
                    'regions' => '2,6,4,7,8,9,10,11,12,13,14,15,16,20,21,23,33,35,36,37,38,39,41,42,43,44,45,46,47,48,49,50,51,53,54,55,56,191,193,194,195,235,239,240,968,970,971,972,10649,10838,11127,11147,11168,1,24,5,25,192,236,10664,10668,10830,10839,10951,11053,11067,172,66,65,18',
                    'competitor_shop_name' => 'Беру.ру',
                    'parsing_project_id' => '752eeb7a-0d5b-4ef5-b53c-bfa17a7008d4',
                ]),
                'preset_columns' => 'source_id,competitor_id,regions,competitor_shop_name',
                'exclude_columns' => ',,price_parsed_id,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,parsing_project_id,,,parsing_id,,,,,,,,,,,,id,,extracted_at,created_at,,,,,,,,,,,,,,,',
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

        // dns
        $filename = 'report_dns_' . $date . '.csv';
        $remoteFilepath = '/out/' . $filename;
        if ($fs->has($remoteFilepath)) {
            $content = $fs->read($remoteFilepath);
            if (!file_exists(sys_get_temp_dir() . '/web/')) {
                mkdir(sys_get_temp_dir() . '/web/');
            }
            file_put_contents(sys_get_temp_dir() . '/web/' . $filename, $content);

            $entityId = Entity::getIdByClassName(PriceRefined::className());
            $fileExchange                     = new FileExchange;
            $fileExchange->name               = 'Загрузка цен Технопоинта от Проаналитики';
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
                'columns_order' => 'http404,price,item_id,competitor_item_sku,competitor_item_name,out_of_stock,url,delivery_days,competitor_item_sku',                'columns_values' => Json::encode([
                    'source_id' => Source::SOURCE_WEBSITE,
                    'competitor_id' => '789edc7f-fab9-46f2-a9de-be02cb5afb77',
                    'competitor_shop_name' => 'technopoint.ru',
                    'parsing_project_id' => 'fe4cc41c-e466-49a4-a61b-41129800ce78',
                    'regions' => '1,4,23,38,2,5,6,8,9,10,191,192,193,54,44,43,35,53,235,47,11168,50,25,39,11,51,194,971,240,55,172,56,16,12,13,14,15,21,20,36,37,46,48,49,195,24,968,18,7,33,42,45,239,970,972,10838,10649,11127,236,10668,10830,10839,10951,11067,66,65,1',
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

        $filename = 'report_ozon_' . $date . '.csv';
        $remoteFilepath = '/out/' . $filename;
        if ($fs->has($remoteFilepath)) {
            $content = $fs->read($remoteFilepath);
            if (!file_exists(sys_get_temp_dir() . '/web/')) {
                mkdir(sys_get_temp_dir() . '/web/');
            }
            file_put_contents(sys_get_temp_dir() . '/web/' . $filename, $content);

            $entityId = Entity::getIdByClassName(PriceRefined::className());
            $fileExchange                     = new FileExchange;
            $fileExchange->name               = 'Загрузка цен OZON от Проаналитики';
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
                'columns_order' => 'http404,price,item_id,competitor_item_sku,competitor_item_name,out_of_stock,url,delivery_days,competitor_item_sku',                'columns_values' => Json::encode([
                    'source_id' => Source::SOURCE_WEBSITE,
                    'competitor_id' => '3ff5f169-7fe2-4988-9b91-fcf46b062d67',
                    'competitor_shop_name' => 'ozon.ru',
                    'parsing_project_id' => 'e22e045e-b3cc-4437-b074-9d3f80b2f649',
                    'regions' => '1,4,23,38,2,5,6,8,9,10,191,192,193,54,44,43,35,53,235,47,11168,50,25,39,11,51,194,971,240,55,172,56,16,12,13,14,15,21,20,36,37,46,48,49,195,24,968,18,7,33,42,45,239,970,972,10838,10649,11127,236,10668,10830,10839,10951,11067,66,65,1',
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
}