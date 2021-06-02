<?php
namespace app\widgets\FileExchangeWidget;

use app\components\base\BaseModel;
use app\components\base\Entity;
use app\models\enum\FileFormat;
use app\models\register\FileExchange;
use app\models\reference\FileExchangeSettings;
use app\widgets\FormBuilder;
use kartik\form\ActiveField;
use kartik\form\ActiveForm;
use yii;
use yii\bootstrap\Widget;
use yii\web\UploadedFile;

class FileExchangeWidget extends Widget
{
    /** @var  BaseModel */
    public $model = null;
    
    /** @var int  */
    public $entity_id = null;

    /** @var FileExchangeSettings  */
    public $fileExchangeSettings = null;

    /** @var array  */
    public $values = [];

    /** @var array  */
    public $exclude = [];

    /** @var bool  */
    public $is_export = false;


    public function run()
    {
        $this->model->search($this->values);

        $search = $this->model->getAppliedSearchFilters();

        if (!$this->entity_id) {
            $this->entity_id = Entity::getIdByClassName($this->model->className());

        }
        if (Yii::$app->user && !Yii::$app->user->isGuest) {
            $this->fileExchangeSettings = FileExchangeSettings::getUserSettings($this->entity_id, $this->is_export);
        }
        if (!$this->fileExchangeSettings) {
            $this->fileExchangeSettings = new FileExchangeSettings;
        }

        if ($this->fileExchangeSettings->isNewRecord) {
            $this->fileExchangeSettings->is_export = $this->is_export;
            $this->fileExchangeSettings->entity_id = $this->entity_id;
            $this->fileExchangeSettings->loadDefaultValues();
            if (!$this->is_export) {
                $this->fileExchangeSettings->encoding           = 'Windows-1251';
                $this->fileExchangeSettings->file_format_id     = FileFormat::TYPE_CSV;
                $this->fileExchangeSettings->data_source        = 'file';
                $this->fileExchangeSettings->presetColumns      =  array_merge($this->model->fileImportPresetColumns(), $search);
            } else {
                $this->fileExchangeSettings->encoding           = 'UTF-8';
                $this->fileExchangeSettings->file_format_id     = FileFormat::TYPE_XLS;
                $this->fileExchangeSettings->skip_first_row     = true;
            }
            $this->fileExchangeSettings->excludeColumns       = $this->model->excludeFieldsFileImportColumns();
        }

        $this->fileExchangeSettings->excludeColumns = array_merge($this->fileExchangeSettings->excludeColumns, $this->exclude);


        if ($this->is_export) {
            $this->fileExchangeSettings->presetColumns = array_keys($search);
            $columns = $this->model->fileExportFields(array_keys($search));
        } else {
            $columns = $this->model->fileImportFields(array_keys($search));
        }

        foreach ($this->fileExchangeSettings->excludeColumns as $columnId) {
            if (in_array($columnId, $this->fileExchangeSettings->presetColumns)) {
                $this->fileExchangeSettings->presetColumns = array_diff($this->fileExchangeSettings->presetColumns, [$columnId]);
            }
        }
        
        echo $this->render($this->is_export?'export':'import', [
            'model'                 => $this->model,
            'widgetId'              => $this->id,
            'fileExchangeSettings'  => $this->fileExchangeSettings,
            'columns'               => $columns,
            'search'                => $search,
            'columnsOrder'          => $this->fileExchangeSettings->columnsOrder,
            'presetColumns'         => $this->fileExchangeSettings->presetColumns,
            'excludeColumns'        => $this->fileExchangeSettings->excludeColumns,
            'columnsValues'         => $this->fileExchangeSettings->columnsValues,
        ]);
    }

    public static function processExportRequest($modelClass, $request) {

        /** @var BaseModel $searchModel */
        $searchModel              = new $modelClass();
        $searchModel->scenario    = $searchModel::SCENARIO_SEARCH;

        if (isset($request['import_settings_id']) && $request['import_settings_id']) {
            $fileExchangeSettings = FileExchangeSettings::findOne($request['import_settings_id']);
        } else {
            $fileExchangeSettings = new FileExchangeSettings;
        }

        $fileExchangeSettings->load($request);

        $searchModel->load($request);


        $fileExchangeSettings->columnsValues    = [];
        $columnValues                           = Yii::$app->request->getQueryParams();

        foreach ($fileExchangeSettings->presetColumns as $column) {
            $columnValues[$column] = $searchModel->$column;
        }
        
        $fileExchangeSettings->columnsValues = $columnValues;

        $fileExchangeSettings->save();

        $fileExchangeSettings->createFileTask();

        FileExchange::runNext();

        return null;
    }

    public static function processImportRequest($modelClass, $request) {

        /** @var BaseModel $model */
        $model              = new $modelClass();
        $model->scenario    = $model::SCENARIO_SEARCH;

        if (isset($request['import_settings_id']) && $request['import_settings_id']) {
            $fileExchangeSettings = FileExchangeSettings::findOne($request['import_settings_id']);
        } else {
            $fileExchangeSettings = new FileExchangeSettings;
        }

        $fileExchangeSettings->load($request);

        if ($fileExchangeSettings->data_source == 'content') {
            $fileExchangeSettings->encoding = 'UTF-8';
        }

        $model->load($request);

        $fileExchangeSettings->columnsValues  = [];
        $columnValues                       = [];

        foreach ($fileExchangeSettings->presetColumns as $column) {
            $columnValues[$column] = $model->$column;
        }

        $fileExchangeSettings->columnsValues = $columnValues;

//        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
//            $response           = clone Yii::$app->response;
//            $response->format   = Response::FORMAT_JSON;
//            /** @var BaseModel[] $models */
//            $models             = [$model, $fileExchangeSettings];
//            $response->content  = json_encode(call_user_func_array('\yii\widgets\ActiveForm::validate', $models));
//            return $response;
//        }

        if ($fileExchangeSettings->data_source == 'file') {
            $fileExchangeSettings->file = UploadedFile::getInstance($fileExchangeSettings, 'file');
        }

        $fileExchangeSettings->save();

        $fileExchangeSettings->createFileImportTask();

        FileExchange::runNext('import');

        return null;
    }

    /**
     * @param ActiveForm $form
     * @param $columnName
     * @param $column
     * @param bool $disabled
     * @param bool $readonly
     * @param BaseModel $model
     * @param mixed $value
     * @return string
     */
    public static function columnPresetTemplate($form, $columnName, $column, $disabled = false, $readonly = false, $model = null, $value = null) {
        /** @var ActiveField $column */
        //$forceId = $widgetId . '-' . $columnName . '-no-preset';
        if ($disabled) {
            $disabled = 'hidden-disabled';
        } else {
            $disabled = '';
        }
        $r = '<div class="col-sm-4 import-widget-preset-column '.$disabled.'" data-column="' . $columnName . '">';


        $column->template = '{label}';
        if (!$readonly) {
            $column->template .= '<a href="#" class="btn btn-default btn-xs import-widget-unpreset" data-column="' . $columnName . '"  title="Использовать эту колонку в импорте"/>
        <span class="glyphicon glyphicon-arrow-down"></span></a>';
            $column->template .= '<a href="#" class="btn btn-default btn-xs import-widget-exclude" data-column="' . $columnName . '"  title="Исключить колонку"/>
        <span class="glyphicon glyphicon-arrow-up"></span></a>';
        }
        $column->template .= '{input}';
        if (strpos($columnName, '_at') !== false) $column = $column->textInput();

        $r .= FormBuilder::renderField($form, $column);

        $r .= '</div>';
        return $r;
    }

}