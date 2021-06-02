<?php

namespace app\models\enum;
use app\components\base\type\Enum;
use app\components\processing\BufferPricesTask;
use app\components\processing\CalcLogExecTask;
use app\components\processing\CalcLogTask;
use app\components\processing\ClearOldDataTask;
use app\components\processing\ClearRabbitTask;
use app\components\processing\CompetitorItemErrorsTask;
use app\components\processing\FileExchangeNextTask;
use app\components\processing\FreeProxiesTask;
use app\components\processing\FreeRegionsTask;
use app\components\processing\FreeVpnsTask;
use app\components\processing\ItemDeduplicateTask;
use app\components\processing\LdapUsersImportTask;
use app\components\processing\ParsingProjectExecutionTask;
use app\components\processing\PformerTypesImportTask;
use app\components\processing\PhubBrandsImportTask;
use app\components\processing\PhubCategoriesImportTask;
use app\components\processing\PhubEnqueueItemsImportTask;
use app\components\processing\PhubItemsImportTask;
use app\components\processing\ProanalyticsLoadPricesTask;
use app\components\processing\ProanalyticsUploadUrlsTask;
use app\components\processing\ProcessParsedPricesTask;
use app\components\processing\ProcessTasksTask;
use app\components\processing\ProjectCalculateTask;
use app\components\processing\RefinePricesTask;
use app\components\processing\RelaunchErrorsTask;
use app\components\processing\ReportKpiTask;
use app\components\processing\ReportMatchingKpiTask;
use app\components\processing\SmsNotificationTask;
use app\components\processing\UpdateActiveParsingsDataTask;
use app\components\processing\UpdateCompetitorItemPrices;
use app\components\processing\UpdateItemsPricesTask;
use app\components\processing\UpdateRanksTask;
use app\components\processing\ViSearchedTask;

/**
 * Тип задачи, запускаемой cron'ом
 */
class ConsoleTaskType extends Enum
{
    const TASK_PARSING_PROJECT_EXECUTION = 1;
    const TASK_LDAP_USERS_IMPORT = 2;
    const TASK_PFORMER_TYPES_IMPORT = 3;
    const TASK_PHUB_BRANDS_IMPORT = 4;
    const TASK_PHUB_CATEGORIES_IMPORT = 5;
    const TASK_PHUB_ENQUEUE_ITEMS_IMPORT = 6;
    const TASK_PHUB_ITEMS_IMPORT = 7;
    const TASK_UPDATE_ITEMS_PRICES = 8;
    const TASK_UPDATE_RANKS = 9;
    const TASK_UPDATE_COMPETITOR_ITEM_PRICES = 10;
    const TASK_COMPETITOR_ITEM_ERRORS = 11;
    const TASK_REFINE_PRICES = 12;
    const TASK_ITEM_DEDUPLICATE = 13;
    const TASK_RELAUNCH_ERRORS = 14;
    const TASK_CLEAR_OLD_DATA = 15;
    const TASK_CLEAR_RABBIT = 16;
    const TASK_BUFFER_PRICES = 17;
    const TASK_CALC_LOG = 18;
    const TASK_CALC_LOG_EXEC = 19;
    const TASK_VI_SEARCHED = 20; // устарело
    const TASK_PROCESS_PARSED_PRICES = 21;
    const TASK_FILE_EXCHANGE_NEXT = 22;
    const TASK_PROJECT_CALCULATE = 23;
    const TASK_REPORT_KPI = 24;
    const TASK_REPORT_MATCHING_KPI = 25;
    const TASK_FREE_REGIONS = 26;
    const TASK_FREE_VPNS = 27;
    const TASK_UPDATE_ACTIVE_PARSINGS_DATA = 28;
    const TASK_PROANALYTICS_LOAD_PRICES = 29;
    const TASK_PROANALYTICS_UPLOAD_URLS = 30;
    const TASK_SMS_NOTIFICATION = 31;
    const TASK_FREE_PROXIES = 32;
    const TASK_PROCESS_TASKS = 33;

    /**
     * Получение имени класса обработчика задачи в зависимости от типа задачи
     * @param integer $taskTypeId тип задачи
     * @return string (false если класс не указан)
     */
    static public function getTaskProcessorClassByTypeId($taskTypeId)
    {
        switch ($taskTypeId) {
            case self::TASK_PARSING_PROJECT_EXECUTION:
                return ParsingProjectExecutionTask::className();
            case self::TASK_LDAP_USERS_IMPORT:
                return LdapUsersImportTask::className();
            case self::TASK_PFORMER_TYPES_IMPORT:
                return PformerTypesImportTask::className();
            case self::TASK_PHUB_BRANDS_IMPORT:
                return PhubBrandsImportTask::className();
            case self::TASK_PHUB_CATEGORIES_IMPORT:
                return PhubCategoriesImportTask::className();
            case self::TASK_PHUB_ENQUEUE_ITEMS_IMPORT:
                return PhubEnqueueItemsImportTask::className();
            case self::TASK_PHUB_ITEMS_IMPORT:
                return PhubItemsImportTask::className();
            case self::TASK_UPDATE_ITEMS_PRICES:
                return UpdateItemsPricesTask::className();
            case self::TASK_UPDATE_RANKS:
                return UpdateRanksTask::className();
            case self::TASK_UPDATE_COMPETITOR_ITEM_PRICES:
                return UpdateCompetitorItemPrices::className();
            case self::TASK_COMPETITOR_ITEM_ERRORS:
                return CompetitorItemErrorsTask::className();
            case self::TASK_REFINE_PRICES:
                return RefinePricesTask::className();
            case self::TASK_ITEM_DEDUPLICATE:
                return ItemDeduplicateTask::className();
            case self::TASK_RELAUNCH_ERRORS:
                return RelaunchErrorsTask::className();
            case self::TASK_CLEAR_OLD_DATA:
                return ClearOldDataTask::className();
            case self::TASK_CLEAR_RABBIT:
                return ClearRabbitTask::className();
            case self::TASK_BUFFER_PRICES:
                return BufferPricesTask::className();
            case self::TASK_CALC_LOG:
                return CalcLogTask::className();
            case self::TASK_CALC_LOG_EXEC:
                return CalcLogExecTask::className();
            case self::TASK_VI_SEARCHED:
                return ViSearchedTask::className();
            case self::TASK_PROCESS_PARSED_PRICES:
                return ProcessParsedPricesTask::className();
            case self::TASK_FILE_EXCHANGE_NEXT:
                return FileExchangeNextTask::className();
            case self::TASK_PROJECT_CALCULATE:
                return ProjectCalculateTask::className();
            case self::TASK_REPORT_KPI:
                return ReportKpiTask::className();
            case self::TASK_REPORT_MATCHING_KPI:
                return ReportMatchingKpiTask::className();
            case self::TASK_FREE_REGIONS:
                return FreeRegionsTask::className();
            case self::TASK_FREE_VPNS:
                return FreeVpnsTask::className();
            case self::TASK_UPDATE_ACTIVE_PARSINGS_DATA:
                return UpdateActiveParsingsDataTask::className();
            case self::TASK_PROANALYTICS_LOAD_PRICES:
                return ProanalyticsLoadPricesTask::className();
            case self::TASK_PROANALYTICS_UPLOAD_URLS:
                return ProanalyticsUploadUrlsTask::className();
            case self::TASK_SMS_NOTIFICATION:
                return SmsNotificationTask::className();
            case self::TASK_FREE_PROXIES:
                return FreeProxiesTask::className();
            case self::TASK_PROCESS_TASKS:
                return ProcessTasksTask::className();
        }
        return false;
    }
}
