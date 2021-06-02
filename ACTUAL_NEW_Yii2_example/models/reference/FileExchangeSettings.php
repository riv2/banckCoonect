<?php
namespace app\models\reference;

use app\components\base\Entity;
use app\components\base\type\Reference;
use app\components\ValidationRules;
use app\models\enum\FileFormat;
use app\models\enum\TaskStatus;
use app\models\register\FileExchange;
use yii;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * Пользовательские настройки импорта сущностей через файлы
 *
 * Class FileExchangeSettings
 * @package app\models\reference
 *
 * @property int entity_id
 * @property bool skip_first_row
 * @property bool auto_mapping
 * @property string columns_order
 * @property string columns_values
 * @property string preset_columns
 * @property string exclude_columns
 * @property string data_source
 * @property string encoding
 * @property int file_format_id
 * @property bool is_export
 *
 * @property array columnsValues
 * @property array presetColumns
 * @property array excludeColumns
 * @property array columnsOrder
 *
 * @property int status_id  - Status::STATUS_DISABLED - в очереди, Status::STATUS_ACTIVE - выполняется, Status::STATUS_REMOVED - выполнен и удален
 *
 * @property Entity entity
 * @property FileFormat fileFormat
 */

class FileExchangeSettings extends Reference
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Настройки импорта';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Настройки импорта';
    }

    /**
     * @var array
     */
    private $_columnsValues = null;

    /**
     * Для создани поля загрузки файла на форме
     * @var UploadedFile
     */
    public $file;

    /**
     * Для создани поля контента на форме
     * @var string
     */
    public $content;

    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired('entity_id'),
            [
                [['data_source', 'columns_order', 'columns_values','preset_columns','exclude_columns','content','encoding'], 'string'],
                [['skip_first_row', 'auto_mapping', 'is_export'], 'boolean'],
                [['file'], 'file', 'skipOnEmpty' => false,
                    'when' => function($model) {
                        return ($model->data_source == 'file' && !$model->is_export);
                    },
                    'whenClient' => "function (attribute, value) {
                        return $('#fileimportsettings-data_source').find('[value=\"file\"]').prop('checked');
                    }"
                ],
                [['content'], 'required',
                    'when' => function($model) {
                        return ($model->data_source == 'content' && !$model->is_export);
                    },
                    'whenClient' => "function (attribute, value) {
                        return $('#fileimportsettings-data_source').find('[value=\"content\"]').prop('checked');
                    }",
                    'except' => self::SCENARIO_SEARCH
                ],
            ],
            ValidationRules::ruleDefault('skip_first_row',false),
            ValidationRules::ruleDefault('is_export', false),
            ValidationRules::ruleDefault('file_format_id', 1),
            ValidationRules::ruleDefault('columns_values','{}'),
            ValidationRules::ruleEnum('file_format_id', FileFormat::className()),
            ValidationRules::ruleEnum('entity_id', Entity::className())
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'entity_id'         => 'Сущность',
            'skip_first_row'    => 'Пропустить первую строку',
            'auto_mapping'      => 'Сопоставить по названиям колонок',
            'columns_order'     => 'Порядок колонок',
            'columns_values'    => 'Значения колонок',
            'columns_names'     => 'Названия колонок',
            'preset_columns'    => 'Предустановленные значения',
            'exclude_columns'   => 'Исключенные колонки значения',
            'file'              => 'Файл',
            'content'           => 'Текст',
            'data_source'       => 'Источник данных',
            'file_format_id'    => 'Формат данных',
            'encoding'          => 'Кодировка',
        ]);
    }

    /**
     * @param $entityId
     * @param int $isExport
     * @param null $dataSource
     * @return FileExchangeSettings
     */
    public static function getUserSettings ($entityId, $isExport = 0, $dataSource = null) {
        $where = [
            'entity_id'         => $entityId,
            'created_user_id'   => Yii::$app->user->identity->getId(),
            'is_export'         => $isExport,
        ];
        if ($dataSource !== null) {
            $where['data_source'] = $dataSource;
        }
        $fes = static::findOne($where);
        if (!$fes) {
            $fes = new static;
            $fes->entity_id     = $entityId;
            $fes->is_export     = $isExport;
            if ($dataSource !== null) {
                $fes->data_source = $dataSource;
            }
        }
        return $fes;
    }

    /**
     * Импорт файла
     * @return null|string
     */
    public function createFileImportTask()
    {
        return $this->createFileTask();
    }

    /**
     * Обработка файла
     * @return null|string
     */
    public function createFileTask()
    {
        if ($this->validate()) {

            $extensions = explode(',', $this->fileFormat->extensions);
            $extension = isset($extensions[0]) ? $extensions[0] : 'tmp';

            if (!$this->is_export) {
                if ($this->file) {
                    $extension = $this->file->extension ?: $extension;
                }
            }

            if ($this->is_export) {
                $path = FileExchange::generateTempExportFileName($extension);
            } else {
                $path = FileExchange::generateTempImportFileName($extension);
            }

            if (!$this->is_export) {
                if ($this->file) {
                    $this->file->saveAs($path);
                } else {
                    file_put_contents($path, $this->content);
                }
            }

            $fileExchange = new FileExchange;
            if ($this->is_export && $this->data_source) {
                $fileExchange->name = $this->name;
            }
            $fileExchange->entity_id          = $this->entity_id;
            $fileExchange->is_export          = $this->is_export;
            $fileExchange->encoding           = $this->encoding;
            $fileExchange->file_path          = $path;
            if ($this->is_export) {
                $fileExchange->original_file_name = $this->name . date(' @ d.m.Y H:i:s') . '.' . $extension;
            } else {
                $fileExchange->original_file_name = $this->file ? $this->file->baseName.'.'.$this->file->extension : null;
            }
            $fileExchange->importSettings     = $this->toArray();
            $fileExchange->file_format_id     = $this->file_format_id;
            $fileExchange->created_user_id    = $this->created_user_id;
            $fileExchange->updated_user_id    = $this->updated_user_id;
            $fileExchange->task_status_id     = TaskStatus::STATUS_QUEUED;
            $fileExchange->save();

            return $fileExchange;
        } else {
            return null;
        }
    }
    

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!$this->name && $this->scenario != self::SCENARIO_SEARCH) {
            $this->name = 'Настройки';
            if ($this->entity_id) {
                $this->name = Entity::getNameById($this->entity_id);
            }
        }
        return parent::beforeValidate();
    }

    /**
     * @param $column
     * @return mixed|null
     */
    public function columnValue($column) {
        if (!$this->_columnsValues) {
            $this->_columnsValues = $this->columnsValues;
        }
        if (isset($this->_columnsValues[$column])) {
            return $this->_columnsValues[$column];
        }
        return null;
    }

    /**
     * @return array|mixed
     */
    public function getColumnsValues() {
        if (!$this->columns_values)
            return [];
        return Json::decode($this->columns_values);
    }

    /**
     * @param $array
     */
    public function setColumnsValues($array) {
        $this->_columnsValues    = $array;
        $this->columns_values   = Json::encode($array);
    }

    /**
     * @return array
     */
    public function getPresetColumns() {
        if (!$this->preset_columns)
            return [];;
        return explode(',',$this->preset_columns);
    }

    /**
     * @param $array
     */
    public function setPresetColumns($array) {
        $this->preset_columns   = join(',', $array);
    }

    /**
     * @return array
     */
    public function getExcludeColumns() {
        if (!$this->exclude_columns)
            return [];
        return explode(',',$this->exclude_columns);
    }

    /**
     * @param $array
     */
    public function setExcludeColumns($array) {
        $this->exclude_columns   = join(',', $array);
    }

    /**
     * @return array
     */
    public function getColumnsOrder() {
        if (!$this->columns_order)
            return [];
        return explode(',',$this->columns_order);
    }

    /**
     * @param $array
     */
    public function setColumnsOrder($array) {
        $this->columns_order   = join(',', $array);
    }


    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'fileFormat',
            'entity'
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
}