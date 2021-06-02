<?php
namespace app\models\register;

use app\components\DataProvider;
use app\components\base\BaseModel;
use app\components\base\Entity;
use app\components\base\type\Register;
use app\components\ValidationRules;
use app\models\enum\ErrorType;
use app\models\enum\FileFormat;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\reference\FileExchangeSettings;
use ForceUTF8\Encoding;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Settings;
use PHPExcel_Style_Fill;
use yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;

/**
 * Импорт файлов
 *
 * Class FileExchange
 * @package app\models\reference
 *
 * @property string     name
 * @property int        entity_id
 * @property string     file_path
 * @property string     original_file_name
 * @property string     encoding
 * @property int        file_format_id
 * @property string     settings
 * @property int        rows_imported
 * @property int        rows_failed
 * @property int        task_status_id
 * @property int        file_size
 * @property boolean    had_errors
 * @property bool       is_export
 *
 * @property array exchangeSettings
 * @property Entity entity
 * @property FileFormat fileFormat
 */

class FileExchange extends Register
{
    public $transaction;
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Работа с файлами';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Работа с файлами';
    }

    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired('entity_id','file_path','file_format_id','settings'),
            [
                [['name', 'original_file_name','file_path', 'settings','encoding'], 'string'],
                [['had_errors'], 'boolean'],
                [['is_export'], 'boolean'],
                [['rows_imported','rows_failed','file_size'], 'number', 'integerOnly' => true],
            ],
            ValidationRules::ruleDefault('had_errors', false),
            ValidationRules::ruleDefault('is_export', false),
            ValidationRules::ruleDefault('status_id', Status::STATUS_DISABLED),
            ValidationRules::ruleEnum('file_format_id', FileFormat::className()),
            ValidationRules::ruleEnum('entity_id', Entity::className()),
            ValidationRules::ruleEnum('task_status_id', TaskStatus::className())
        );
    }

    /**
     * Попытаться запустить задачу, если не ограничен запуск по её типу
     * @param $type
     */
    public static function runNext($type = 'export') {
        shell_exec('php '.Yii::getAlias('@app').'/yii task/file-exchange-next '.$type.' > /dev/null 2>/dev/null &');
    }
    
    /**
     * Попытаться запустить задачу, если не ограничен запуск по её типу
     */
    public function tryToRun() {
        $type = $this->is_export ? 'export' : 'import';
        shell_exec('php '.Yii::getAlias('@app').'/yii task/file-exchange-run ' . $type .' '. $this->id.' > /dev/null 2>/dev/null &');
    }

    /**
     * Запустить процесс
     */
    public function run() {
        if ($this->is_export) {
            $this->startExport();
        } else {
            $this->startImport();
        }
    }

    /**
     * Убить задачу
     */
    public function cancel() {
        $type = $this->is_export ? 'export' : 'import';
        $result = [];
        exec('ps aux | grep -v grep | grep "task/file-exchange-run ' . $type .' '.$this->id.'"', $result);
        $count = count($result);
        if ($count) {
            foreach ($result as $row) {
                $row        = preg_replace('/\s+/', ' ', $row);
                $process    = explode(' ', $row);
                shell_exec('kill -9 '.$process[1]);
            }
        }
        $this->status_id        = Status::STATUS_DISABLED;
        $this->task_status_id   = TaskStatus::STATUS_CANCELED;
        $this->save();
        return true;
    }

    public function getExportLabelsRow($columns) {
        $row = [];
        foreach ($columns as $columnId => $field) {
            $row[] = isset($field['label'])? $field['label'] : $field['attribute'];
        }
        return $row;
    }

    public function getExportRow($model, $columns, $index) {
        $row = [];
        foreach ($columns as $columnId => $column) {
            $colValue   = "";
            if (!is_numeric($columnId) && !is_array($column)) {
                $columnId = $column;
            }
            $field = $columnId;
            if (isset($column['attribute'])) {
                $field = $column['attribute'];
            }
            if (isset($column['value'])) {
                $value = $column['value'];
                if (is_callable($value)) {
                    $colValue = $value($model, $index, $index, $column);
                } else {
                    $colValue = $value;
                }
            } else {
                if (is_object($model) && isset($model->$field)) {
                    $colValue = $model->$field;
                } else if (is_array($model) && isset($model[$field])) {
                    $colValue = $model[$field];
                }
            }
            if (is_array($colValue)) {
                foreach ($colValue as $cav) {
                    $row[] = $cav;
                }
            } else {
                $row[] = $colValue;
            }
        }
        return $row;
    }

    public function startExport() {
        ob_start();

        $this->task_status_id = TaskStatus::STATUS_RUNNING;
        $this->save();
        
        $fileExchangeSettings = new FileExchangeSettings;
        $fileExchangeSettings->setAttributes($this->exchangeSettings);

        $modelClass = Entity::getClassNameById($this->entity_id);  /** @var BaseModel $searchModel */
        $searchModel              = new $modelClass();
        $searchModel->scenario    = $searchModel::SCENARIO_SEARCH;
        $searchModel->setAttributes($fileExchangeSettings->columnsValues);
        
        if (!$fileExchangeSettings->data_source) {
            $dataProvider = new DataProvider([
                'query'         => $searchModel->crudSearch($fileExchangeSettings->columnsValues),
                'sort'          => $searchModel->getSort(),
                'pagination'    => [
                    'pageSizeLimit'     => [-1, 0x7FFFFFFF],
                    'defaultPageSize'   => 10000,
                    'pageSize'          => 10000,
                ],
            ]);
            $fields                = $fileExchangeSettings->columnsOrder;
            $columns = [];
            foreach ($fields as $columnId => $column) {
                $columns[] = ['attribute' => $column, 'label' => $searchModel->getAttributeLabel($column)];
            }
        } else {
            $method   = $fileExchangeSettings->data_source;
            $report   = $searchModel->$method();
            $dataProvider = new ArrayDataProvider([
                'allModels' => $report['items'],
                'sort' => $report['sort'],
                'pagination' => [
                    'pageSize' => 100000,
                ],
            ]);
            $columns = $report['columns'];
        }

        $errors     = 0;
        $exported   = 0;
        $page       = 0;
        $file       = null;

        try {
            if ($this->file_format_id == FileFormat::TYPE_XLS) {
                $objPHPExcel = new PHPExcel();
                PHPExcel_Settings::setLocale('ru');
                $sheetIndex  = 0;
                $objPHPExcel->setActiveSheetIndex($sheetIndex);
                $sheet = $objPHPExcel->getActiveSheet();
                $sheet->setTitle($this->created_at->format("d-m-Y_H-i-s"));

                $rowNum = 1;
                if ($fileExchangeSettings->skip_first_row) {
                    $labels = $this->getExportLabelsRow($columns);
                    foreach ($labels as $colNum => $label) {
                        if ($label) {
                            $sheet->setCellValueByColumnAndRow($colNum, $rowNum, $label);
                        } else {
                            $sheet->setCellValueByColumnAndRow($colNum, $rowNum, '');
                        }
                        $sheet->getColumnDimensionByColumn($colNum)->setAutoSize(true);
                        $sheet->getCellByColumnAndRow($colNum, $rowNum)->getStyle()->getFont()->setBold(true);
                    }
                    $rowNum++;
                }

                $reg_exUrl = '/(http|https|ftp|ftps)\:\/\/([^\"\s]+)/';

                $models = $dataProvider->getModels();
                while (!empty($models)) {
                    foreach ($models as $index => $model) {
                        $row = $this->getExportRow($model, $columns, $index);
                        foreach ($row as $colNum => $value) {
                            if ($colNum <= 250) { // Если колонок больше 250 эксель отказывается дальше записывать
                                if ($rowNum == 1) {
                                    $sheet->getColumnDimensionByColumn($colNum)->setAutoSize(true);
                                }
                                $url    = [];
                                $u      = $value;
                                if(preg_match($reg_exUrl, $u, $url)) {
                                    $value = strip_tags($value);
                                    $sheet->getCellByColumnAndRow($colNum, $rowNum)->setHyperlink(new \PHPExcel_Cell_Hyperlink($url[0], $value));
                                }
                                if(preg_match('/^\\*\\*\\*/', $value)) {
                                    $value = str_replace('***', '', $value);
                                    $sheet->getCellByColumnAndRow($colNum, $rowNum)
                                        ->getStyle()
                                        ->getFill()
                                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                        ->getStartColor()
                                        ->setRGB('FFDDDD');
                                }
                                $sheet->setCellValueByColumnAndRow($colNum, $rowNum, $value);
                            }
                        }
                        $exported++;
                        $rowNum++;
                    }
                    $page++;
                    $dataProvider->pagination->page = $page;
                    $dataProvider->refresh();
                    $this->rows_imported = $exported;
                    $this->rows_failed = $errors;
                    $this->had_errors = ($errors > 0);
                    $this->save();
                    /** @var BaseModel[] $models */
                    $models = $dataProvider->getModels();
                }
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                ob_end_clean();
                $objWriter->save($this->file_path);
            }
            else if ($this->file_format_id == FileFormat::TYPE_JSON) {
                $file = fopen($this->file_path, "w");

                if ($fileExchangeSettings->skip_first_row) {
                    $string = '"' . join('";"', $this->getExportLabelsRow($columns)) . "\"\n";
                    if ($this->encoding && $this->encoding != 'UTF-8') {
                        $string = mb_convert_encoding($string, $this->encoding, "UTF-8");
                    }
                    fputs($file, $string);
                }

                $models = $dataProvider->getModels();
                while (!empty($models)) {
                    foreach ($models as $index => $model) {
                        $row = $this->getExportRow($model, $columns, $index);
                        foreach ($row as $r => $col) {
                            $col        = preg_replace('/^\*\*\*/', '', $col);
                            $col        = preg_replace('/\\\\/', '', $col);
                            $row[$r]    = '"'.preg_replace('/"/', '\\\\"', $col).'"';
                        }
                        $string = join('";"', $row) . "\n";
                        if ($this->encoding && $this->encoding != 'UTF-8') {
                            $string = mb_convert_encoding($string, $this->encoding, "UTF-8");
                        }
                        fputs($file, $string);
                        $exported++;
                    }
                    $page++;
                    $dataProvider->pagination->page = $page;
                    $dataProvider->refresh();
                    $this->rows_imported = $exported;
                    $this->rows_failed = $errors;
                    $this->had_errors = ($errors > 0);
                    $this->save();
                    /** @var BaseModel[] $models */
                    $models = $dataProvider->getModels();
                }

                fclose($file);
            }else {

                $file = fopen($this->file_path, "w");

                if ($fileExchangeSettings->skip_first_row) {
                    $string = '"' . join('";"', $this->getExportLabelsRow($columns)) . "\"\n";
                    if ($this->encoding && $this->encoding != 'UTF-8') {
                        $string = mb_convert_encoding($string, $this->encoding, "UTF-8");
                    }
                    fputs($file, $string);
                }

                $models = $dataProvider->getModels();
                while (!empty($models)) {
                    foreach ($models as $index => $model) {
                        $row = $this->getExportRow($model, $columns, $index);
                        foreach ($row as $r => $col) {
                            $col        = preg_replace('/^\*\*\*/', '', $col);
                            $col        = preg_replace('/\\\\/', '', $col);
                            $row[$r]    = '"'.preg_replace('/"/', '\\\\"', $col).'"';
                        }
                        $string = join('";"', $row) . "\n";
                        if ($this->encoding && $this->encoding != 'UTF-8') {
                            $string = mb_convert_encoding($string, $this->encoding, "UTF-8");
                        }
                        fputs($file, $string);
                        $exported++;
                    }
                    $page++;
                    $dataProvider->pagination->page = $page;
                    $dataProvider->refresh();
                    $this->rows_imported = $exported;
                    $this->rows_failed = $errors;
                    $this->had_errors = ($errors > 0);
                    $this->save();
                    /** @var BaseModel[] $models */
                    $models = $dataProvider->getModels();
                }

                fclose($file);
            }
            $this->task_status_id = TaskStatus::STATUS_FINISHED;
            $this->file_size = filesize($this->file_path);
        } catch (\Exception $e) {
            $errors++;
            if ($file) {
                fclose($file);
            }
            echo $e->getMessage().PHP_EOL;
            Error::logError($e, ErrorType::TYPE_FILE_IMPORT, Entity::FileExchange, $this->id);
            @unlink($this->file_path);
            $this->task_status_id = TaskStatus::STATUS_FAILED;
        }

        $this->rows_imported    = $exported;
        $this->rows_failed      = $errors;
        $this->had_errors       = ($errors > 0);
        $this->save();

        FileExchange::runNext();
    }

    public function startImport() {
        $this->task_status_id = TaskStatus::STATUS_RUNNING;
        $this->save();

        $fileExchangeSettings = new FileExchangeSettings;
        $fileExchangeSettings->setAttributes($this->exchangeSettings);

        $template = [];

        foreach ($fileExchangeSettings->columnsValues as $attribute => $value) {
            if($attribute == 'id' && !$value) continue;
            $template[$attribute] = $value;
        }

        $modelClass = Entity::getClassNameById($this->entity_id);
        
        $errors     = 0;
        $imported   = 0;

        //$transaction = Yii::$app->db->beginTransaction();

        try {
            if ($this->file_format_id == FileFormat::TYPE_CSV) {
                $map = null;
                if (file_exists($this->file_path) && $handle = fopen($this->file_path, 'r')) {
                    while (($row = fgets($handle, 2048)) !== FALSE) {
                        if (!$fileExchangeSettings->encoding) {
                            $test = preg_replace('/[a-zA-Z0-9\."\'\[\]\<\>\!\{\},\(\)\s]/ui', '', $row);
                            $encoding = mb_detect_encoding($test, "Windows-1251,UTF-8,Windows-1252,ISO-8859-1");
                        } else {
                            $encoding = $fileExchangeSettings->encoding;
                        }

                        if ($encoding) {
                            $row = mb_convert_encoding($row, 'UTF-8', $encoding);
                        }
                        $firstRow = trim($row);
                        $row = str_getcsv($firstRow, ",");
                        if (count($row) === 1) {
                            $row = str_getcsv($firstRow, ";");
                        }
                        foreach ($row as $i => $col) {
                            $row[$i] = trim($col);
                        }

                        try {

                            if (!$map && $fileExchangeSettings->skip_first_row) {
                                $map = $row;
                                continue;
                            }
                            if ($fileExchangeSettings->auto_mapping && $map) {
                                while (count($row) < count($map)) {
                                    $row[] = '';
                                }
                                $attributes = array_merge($template, array_combine($map, $row));
                            } else {
                                while (count($row) < count($fileExchangeSettings->columnsOrder)) {
                                    $row[] = '';
                                }
                                $attributes = array_merge($template, array_combine($fileExchangeSettings->columnsOrder, $row));
                            }

                            /** @var BaseModel $newModel */
                            $newModel = new $modelClass;
                            $newModel->importOneFromFile($attributes);
                            $imported++;
                        } catch (\Exception $e) {
                            $errors++;
                            echo $e->getMessage() . PHP_EOL;
                            Error::logError($e, ErrorType::TYPE_FILE_IMPORT, Entity::FileExchange, $this->id);
                        }
                    }
                    @fclose($handle);
                }
            } else if ($this->file_format_id == FileFormat::TYPE_JSON) {
                $contents = Encoding::toUTF8(file_get_contents($this->file_path));
                $json = Json::decode($contents);
                $map = null;
                foreach ($json as $row) {
                    if (!$map && $fileExchangeSettings->auto_mapping) {
                        $map = array_keys($row);
                    }
                    if ($map && $fileExchangeSettings->auto_mapping) {
                        $attributes = array_merge($template, array_combine($map, $row));
                    } else {
                        $attributes = array_merge($template, array_combine($fileExchangeSettings->columnsOrder, $row));
                    }
                    /** @var BaseModel $newModel */
                    $newModel = new $modelClass;
                    try {
                        $newModel->importOneFromFile($attributes);
                        $imported++;
                    } catch (\Exception $e) {
                        $errors++;
                        Error::logError($e, ErrorType::TYPE_FILE_IMPORT, Entity::FileExchange, $this->id);
                    }
                }
            }
            //$transaction->commit();
        } catch (\Exception $e) {
            //$transaction->rollBack();
            echo $e->getMessage().PHP_EOL;
            Error::logError($e, ErrorType::TYPE_FILE_IMPORT, Entity::FileExchange, $this->id);
        }

        @unlink($this->file_path);
        $this->status_id = Status::STATUS_DISABLED;
        $this->task_status_id = TaskStatus::STATUS_FINISHED;
        $this->rows_imported = $imported;
        $this->rows_failed = $errors;
        $this->had_errors = ($errors > 0);
        $this->save();

        FileExchange::runNext('import');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'entity_id'                 => 'Сущность',
            'entity'                    => 'Сущность',
            'original_file_name'        => 'Файл',
            'file_path'                 => 'Временный файл',
            'file_format_id'            => 'ID Формат файла',
            'fileFormat'                => 'Формат файла',
            'settings'                  => 'Настройки',
            'rows_imported'             => 'Импортированно',
            'rows_failed'               => 'Ошибок',
            'had_errors'                => 'Были ошибки',
            'importErrors'              => 'Ошибки во время импорта',
            'encoding'                  => 'Кодировка',
            'task_status_id'            => 'Состояние задачи',
        ]);
    }

    /**
     * Путь загрузки файлов
     * @param $extension
     * @return string
     */
    public static function generateTempImportFileName($extension = 'tmp') {
        if (!$extension) {
            $extension = 'tmp';
        }
        return self::importPath().'/'.uniqid("tmp_").'.'.$extension;
    }

    /**
     * Путь загрузки файлов
     * @param $extension
     * @return string
     */
    public static function generateTempExportFileName($extension = 'tmp') {
        if (!$extension) {
            $extension = 'tmp';
        }
        return self::exportPath().'/'.uniqid("tmp_").'.'.$extension;
    }


    /**
     * Путь загрузки файлов
     * @return string
     */
    public static function importPath() {
        return Yii::getAlias('@runtime').'/upload';
    }


    /**
     * Путь загрузки файлов
     * @return string
     */
    public static function exportPath() {
        return Yii::getAlias('@runtime').'/download';
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->ws->publishToUser($this->created_user_id, 'fileExchangeUpdated', ['FileExchange' => $this->toArray()]);
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!$this->name && $this->scenario != self::SCENARIO_SEARCH) {
            $this->name = $this->is_export ? 'Экспорт' : 'Импорт';
            if ($this->entity_id) {
                $this->name .= ' «'.Entity::getNameById($this->entity_id).'»';
            }
            if (!$this->is_export) {
                if ($this->original_file_name) {
                    $this->name .= ' из ' . $this->original_file_name;
                }
            } else {
                if ($this->file_format_id) {
                    $this->name .= ' в ' . $this->fileFormat;
                }
            }
        }
        return parent::beforeValidate();
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if($insert && !$this->is_export) {
                $this->file_size = filesize($this->file_path);
            }
            return true;
        }
        return false;
    }


    /**
     * @return array|mixed
     */
    public function getExchangeSettings() {
        if (!$this->settings)
            return [];
        return Json::decode($this->settings);
    }

    /**
     * @param $array
     */
    public function setImportSettings($array) {
        $this->settings     = Json::encode($array);
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'fileFormat',
            'entity',
//            'importErrors',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFileFormat() {
        return $this->hasOne(FileFormat::className(), ['id' => 'file_format_id'] );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntity() {
        return $this->hasOne(Entity::className(), ['id' => 'entity_id'] );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImportErrors() {
        return $this->hasMany(Error::className(), ['entity_row_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->name;
    }

}