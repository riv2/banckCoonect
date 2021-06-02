<?php
namespace app\models\reference;

use app\components\base\Entity;
use app\components\base\type\Reference;
use app\components\ValidationRules;
use app\models\enum\FileFormat;
use app\models\enum\TaskStatus;
use app\models\register\FileExchange;
use app\models\register\FileProcessing;
use app\models\register\Task;
use yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * настройки импорта файлы
 *
 * Class FileProcessingSettings
 * @package app\models\reference
 *
 * @property string settings_json
 * @property string class
 *
 * @property array settings
 *
 */

class FileProcessingSettings extends Reference
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Настройки обработки файлов';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Настройки обработки файлов';
    }


    public function rules()
    {
        return array_merge(parent::rules(),
            [
                [['class'], 'string'],
                [['settings_json','settings'], 'safe'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'class'                     => 'Класс',
            'settings_json'             => 'Настройки',
        ]);
    }


    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[

            'name',
            'class',
            'upload' => [
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a('<i class="fa fa-upload"></i> Загрузить',['/file/upload', 'id' => $model->id],['class' => 'btn btn-xs btn-primary']);
                }
            ]
        ]);
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
    public function beforeValidate()
    {
        if (!$this->name && $this->scenario != self::SCENARIO_SEARCH) {
            $this->name = 'Настройки';
        }
        return parent::beforeValidate();
    }


    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
        ]);
    }


}