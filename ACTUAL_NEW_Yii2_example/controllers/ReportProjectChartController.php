<?php
namespace app\controllers;

use app\components\base\BaseModel;
use app\components\BaseController;
use app\models\pool\ReportParsing;
use app\models\pool\ReportProjectChart;
use Yii;

/**
 * Контроллер отчета парсингов по конкурентам
 */
class ReportProjectChartController extends BaseController
{
    public $modelClass          = 'app\models\pool\ReportProjectChart';
    public $searchModelClass    = 'app\models\pool\ReportProjectChart';

    public function actionIndex()
    {
        /** @var $model ReportProjectChart */
        $model = new $this->modelClass();

        $report = $model->generateReport();

        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $report['dataProvider'],
        ]);
    }

    public function actionExport()
    {
        /** @var $model ReportProjectChart */
        $model = new $this->modelClass();

        $report = $model->generateReport();

        $excel = new \PHPExcel();
        \PHPExcel_Settings::setLocale('ru');
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();

        $rowIndex = 1;
        foreach ($report['dataProvider']->query->each() as $row) {
            if ($rowIndex === 1) {
                $columnIndex = 0;
                foreach ($row as $key => $value) {
                    $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $model->getAttributeLabel($key));
                    $columnIndex++;
                }
                $rowIndex++;
            }

            $columnIndex = 0;
            foreach ($row as $value) {
                $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $value);
                $columnIndex++;
            }
            $rowIndex++;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Description: File Transfer');
        Header('Content-Disposition: attachment; filename='
            . ($this->modelClass)::getSingularNominativeName()
            . ' '
            . $model->getProject()->select('name')->scalar()
            . ' '
            . ($this->modelClass)::getSeriesLabels($model->type)[$model->series_index]
            . ' от '
            . $model->date
            . '.xls'
        );
        header('Content-Transfer-Encoding: binary');
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('php://output');
    }
}