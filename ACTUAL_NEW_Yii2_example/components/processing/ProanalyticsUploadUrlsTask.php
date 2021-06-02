<?php

namespace app\components\processing;

use app\components\base\ConsoleTaskInterface;
use app\models\enum\Region;
use app\models\pool\LogKpi;
use app\models\reference\CompetitorItem;
use app\models\reference\ConsoleTask;
use app\models\reference\ParsingProject;
use yii\base\BaseObject;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Settings;
use yii\db\Query;
use yii\helpers\Json;

class ProanalyticsUploadUrlsTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $beruRuCompetitor = \app\models\reference\Competitor::findOne('53fb2711-17cc-416a-840b-00ad8ee7dade');
        $project = \app\models\reference\Project::findOne('7ed41eb9-6ec3-44ed-83a3-e82eb707def9');
        $kpiData = (new Query())
            ->select(new \yii\db\Expression('json_agg(CONCAT(url, \'|\', item_id))'))
            ->from(LogKpi::tableName())
            ->andWhere([
                'project_id' => '7ed41eb9-6ec3-44ed-83a3-e82eb707def9',
                'competitor_id' => $beruRuCompetitor->primaryKey,
                'project_execution_id' => $project->lastProjectExecution->id,
            ])
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
            $sheet->setCellValueByColumnAndRow(0, $index, $itemCellValue);
            $sheet->setCellValueByColumnAndRow(1, $index, $urlCellValue);
            $sheet->setCellValueByColumnAndRow(2, $index, $beruRuCompetitor->name);
        }
        var_dump('Беру ру - ' . count($kpiData));
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save(sys_get_temp_dir() . '/web/beru_ru_urls.xlsx');
        \Yii::$app->fs->put(
            '/in/beru_ru_urls.xlsx',
            file_get_contents(sys_get_temp_dir() . '/web/beru_ru_urls.xlsx')
        );

        $params = Json::decode($consoleTask->params);
        if (!isset($params['projectsToExport'])) {
            echo 'Нет проектов на экспорт';
            return;
        }

        $projectsToExport = $params['projectsToExport'];

        foreach ($projectsToExport as $name => $parsingProjectId) {
            $parsingProject = ParsingProject::findOne(['id' => $parsingProjectId]);

            $excel = new PHPExcel();
            PHPExcel_Settings::setLocale('ru');
            $excel->setActiveSheetIndex(0);
            $sheet = $excel->getActiveSheet();
            $rowIndex = 0;
//        foreach ($parsingProject->regions as $region) {
            $urls = $parsingProject->execute([], true);
            foreach ($urls as $url) {
                $sheet->setCellValueByColumnAndRow(0, $rowIndex, $url['attributes']['item_id']);
                $sheet->setCellValueByColumnAndRow(1, $rowIndex, $url['url']);
                $sheet->setCellValueByColumnAndRow(2, $rowIndex, 'Москва и область');
                $rowIndex++;
            }
//        }
            var_dump($name . ' - ' . $rowIndex);
            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $objWriter->save(sys_get_temp_dir() . '/web/' . $name . '_urls.xlsx');
            \Yii::$app->fs->put(
                '/in/' . $name . '_urls.xlsx',
                file_get_contents(sys_get_temp_dir() . '/web/' . $name . '_urls.xlsx')
            );
        }

        /*\Yii::$app->mailer
            ->compose()
            ->setHtmlBody('Добрый день! Только что мы выгрузили урлы номенклатур, просьба загрузить их с вашей стороны.')
            ->setFrom('dev.email.ct@gmail.com')
            ->setTo([
                'lukoyanovas@cloud-team.ru',
                'kristina.volosnikova@proanalytics.ru',
                'yury.sergeev@proanalytics.ru',
            ])
            ->setSubject('ВсеИнструменты.ру: уведомление о выгрузке урлов номенклатур')
            ->send();/***/
    }
}