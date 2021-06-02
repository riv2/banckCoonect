<?php
namespace app\models\register;

use app\components\DataProvider;
use app\components\base\BaseModel;
use app\components\base\Entity;
use app\components\base\type\Register;
use app\components\DateTime;
use app\components\processing\ExcelOpt220;
use app\components\ValidationRules;
use app\models\enum\ErrorType;
use app\models\enum\FileFormat;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\reference\FileProcessingSettings;
use ForceUTF8\Encoding;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Settings;
use PHPExcel_Style_Fill;
use yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * Обработка файлов
 *
 * Class FileProcessing
 * @package app\models\reference
 *
 * @property int        file_processing_settings_id
 * @property string     file_path
 * @property string     original_file_name
 * @property string     encoding
 * @property string     settings_json
 * @property int        file_size
 * @property int        total
 * @property int        progress
 * @property int        errors
 * @property int        task_id
 * @property string     error_message
 *
 * @property array     settings
 * @property FileProcessingSettings     fileProcessingSettings
 * @property Task task
 */

class FileProcessing extends Register
{
    /**
     * Для создани поля загрузки файла на форме
     * @var UploadedFile
     */
    public $file;

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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'file_processing_settings_id'   => 'Тип документа',
            'file_path'                     => 'Временный файл',
            'original_file_name'            => 'Файл',
            'encoding'                      => 'Кодировка',
            'settings_json'                 => 'Настройки',
            'file_size'                     => 'Размер',
            'file'                          => 'Файл',
            'total'                         => 'Всего',
            'progress'                      => 'Обработано',
            'errors'                        => 'Ошибок',
            'task_id'                       => 'Задача',
            'error_message'                 => 'Ошибка',


            'settings'                  => 'Настройки',
            'fileProcessingSettings'    => 'Тип документа',
            'task'                      => 'Задача',
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired('file_processing_settings_id'),
            ValidationRules::ruleUuid('file_processing_settings_id'),
            ValidationRules::ruleUuid('task_id'),
            [
                [['original_file_name','file_path','encoding','error_message'], 'string'],
                [['file'], 'file'],
                [['settings_json'], 'safe'],
                [['total','progress','file_size','errors'], 'number', 'integerOnly' => true],
            ],
            [],
            []
        );
    }

    public function taskProcess(Task $task = null) {

        $task->task_status_id   = TaskStatus::STATUS_RUNNING;
        $task->started_at       = new DateTime();
        $task->save();

        $class = $this->fileProcessingSettings->class;
        /** @var ExcelOpt220 $processor */
        $processor = new $class($this->settings);
        $processor->fileProcessing = $this;

        try {
            $processor->process();

            $task->task_status_id   = TaskStatus::STATUS_FINISHED;
            $task->finished_at      = new DateTime();
            $task->status_id        = Status::STATUS_DISABLED;
            $task->save();

            @unlink($this->file_path);
        } catch (\Exception $e) {
            $this->error_message = $e->getMessage();
            $this->save();

            Error::logError($e, ErrorType::TYPE_TASK, Entity::Task, $task->id);
            $task->task_status_id   = TaskStatus::STATUS_FAILED;
            $task->save();

            @unlink($this->file_path);
        }

    }

    public function cancelProcess() {
    }


    public function uploadFile() {
        if (!$this->file) {
            $this->file = UploadedFile::getInstance($this,'file');
        }
        $extension = $this->file->extension;
        $path = FileExchange::generateTempImportFileName($extension);
        $this->file->saveAs($path, true);
        $this->file_path = $path;
        $this->file_size = filesize($path);
        $this->original_file_name = $this->file->baseName;
    }

    public function createTask() {
        $task2 = new Task;

        $task2->name                 = 'Обработка '.$this->fileProcessingSettings->name.' '.$this->original_file_name;
        $task2->requester_id         = $this->id;
        $task2->requester_entity_id  = Entity::FileProcessing;
        $task2->task_function        = 'process';
        $task2->task_type_id         = TaskType::TYPE_FILE_PROCESSING;
        $task2->enqueue();

        $this->task_id = $task2->id;
        $this->save();
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'fileProcessingSettings' ,
            'original_file_name',
            'progress'=> [
                'format' => 'raw',
                'label' => 'Обработано',
                'value' => function($model) {
                    if ($model->progress || !$model->task) {
                        return $model->progress;
                    }
                    return $model->task->progress;
                }
            ],
            'total'=> [
                'format' => 'raw',
                'label' => 'Всего',
                'value' => function($model) {
                    if ($model->total || !$model->task) {
                        return $model->total;
                    }
                    return $model->task->total;
                }
            ],
            'status' => [
                'format' => 'raw',
                'label' => 'Статус',
                'value' => function($model) {
                    if (!$model->task) {
                        return null;
                    }
                    return TaskStatus::getNameById($model->task->task_status_id);
                }
            ],
            'started_at' => [
                'format' => 'raw',
                'label' => 'Начато',
                'value' => function($model) {
                    if (!$model->task) {
                        return null;
                    }
                    return $model->task->started_at;
                }
            ],
            'finished_at' => [
                'format' => 'raw',
                'label' => 'Завершено',
                'value' => function($model) {
                    if (!$model->task) {
                        return null;
                    }
                    return $model->task->finished_at;
                }
            ],
            'error_message'
        ]);
    }

    /**
     * Путь загрузки файлов
     * @return string
     */
    public static function importPath() {
        return Yii::getAlias('@runtime').'/upload';
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->id = self::getDb()->createCommand("SELECT uuid_generate_v4();")->queryScalar();
            }
            return true;
        }
        return false;
    }


    /**
     * @return array|mixed
     */
    public function getSettings() {
        if (!$this->settings_json)
            return [];
        return Json::decode($this->settings_json);
    }

    /**
     * @param $array
     */
    public function setSettings($array) {
        $this->settings_json     = Json::encode($array);
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'fileProcessingSettings',
            'task',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFileProcessingSettings() {
        return $this->hasOne(FileProcessingSettings::className(), ['id' => 'file_processing_settings_id'] );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask() {
        return $this->hasOne(Task::className(), ['id' => 'task_id'] );
    }


    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->original_file_name;
    }

}