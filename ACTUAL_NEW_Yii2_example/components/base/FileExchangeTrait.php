<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 14.09.16
 * Time: 05:53
 */

namespace app\components\base;

use app\components\Schema;
use app\validators\UuidValidator;
use app\widgets\FormBuilder;
use yii\base\InvalidValueException;
use yii\db\ColumnSchema;
use yii\db\IntegrityException;
use yii\helpers\Json;

/**
 * Trait FileExchangeTrait
 * @package app\components\base
 */
trait FileExchangeTrait
{
    public static $importSearchCache = [];

    public static function fileImportEnabled() {
        return true;
    }

    public static function fileExportEnabled() {
        return true;
    }
    
    public function excludeFieldsUpdate() {
        return ['id'];
    }

    public function excludeFieldsCreate() {
        return ['id'];
    }

    public function fileImportFields($extraFields = []) {
        return $this->fileExchangeFields(false, $extraFields);
    }

    public function fileExportFields($extraFields = []) {
        return $this->fileExchangeFields(true, $extraFields);
    }


    /**
     * Не импортировать колонки
     * @return array
     */
    public function excludeFieldsFileImportColumns() {
        return [];
    }

    protected function fileExchangeFields($is_export, $extraFields = []) {
        /** @var BaseModel $this */
        $modelColumns = $this->attributes();

        foreach ($this->relations() as $relation) {
            $activeRelation = $this->getRelation($relation);
            if (($activeRelation->multiple)) {
                continue;
            }
            $modelColumns[] = $relation;
        }

        foreach ($extraFields as $field) {
            if(!in_array($field, $modelColumns) && $field != 'csv_filter') {
                $modelColumns[] = $field;
            }
        }


        $columns = FormBuilder::getFormFields($this, $modelColumns, false, [], $is_export);

        return $columns;
    }

    /**
     * Для нового объекта ищет в БД запись которая конфликтует с ним по уникальному индексу
     * и подменяет аттрибуты объекта на данные из БД.
     * Если использовать этот метод перед сохранением, позволяет сделать что-то наподобие INSERT ON DUPLICATE UPDATE
     * @return BaseModel
     */
    public function replaceByUniqueIndex() {
        /** @var BaseModel $this */
        $primaryKeys = ['id'];

        $where  = [];
        $pk     = true;

        foreach ($primaryKeys as $primaryKey) {
            if (!$this->$primaryKey) {
                $pk = false;
                break;
            } else {
                $where[$primaryKey] = $this->$primaryKey;
            }
        }

        if ($pk) {
            $toReplace = static::find()->andWhere($where)->one();
            if ($toReplace) {
                return $toReplace;
            }
        }

        $rules      = $this->rules();
        $fields     = null;

        foreach ($rules as $rule) {
            if (count($rule) >= 2) {
                $validator  = $rule[1];
                if ($validator == 'unique') {
                    if (isset($rule['targetAttribute'])) {
                        $fields = $rule['targetAttribute'];
                    } else {
                        $fields = $rule[0];
                    }
                    break;
                }
            }
        }

        if ($fields) {
            $where = [];
            if (!is_array($fields)) {
                $fields = [$fields];
            }
            foreach ($fields as $field) {
                $where[$field] = $this->$field;
            }
            /** @var BaseModel $toReplace */
            $toReplace = static::find()->andWhere($where)->one();
            if ($toReplace) {
                return $toReplace;
            }
        }

        return null;
    }

    /**
     * Импорт из файла с учетом поиска связанных сущностей по имени
     * @param $attributes
     * @return $this
     * @throws IntegrityException
     * @throws \yii\base\InvalidConfigException
     */
    public function importOneFromFile($attributes) {

        /** @var FileExchangeTrait|BaseModel $this */
        $formatMap = [
            Schema::TYPE_PK                 => 'number',
            Schema::TYPE_BIGPK              => 'number',
            Schema::TYPE_STRING             => 'text',
            Schema::TYPE_TEXT               => 'text',
            Schema::TYPE_SMALLINT           => 'number',
            Schema::TYPE_INTEGER            => 'number',
            Schema::TYPE_BIGINT             => 'number',
            Schema::TYPE_FLOAT              => 'number',
            Schema::TYPE_DOUBLE             => 'number',
            Schema::TYPE_DECIMAL            => 'number',
            Schema::TYPE_DATETIME           => 'datetime',
            Schema::TYPE_TIMESTAMP          => 'datetime',
            Schema::TYPE_TIME               => 'datetime',
            Schema::TYPE_DATE               => 'datetime',
            Schema::TYPE_BINARY             => 'text',
            Schema::TYPE_BOOLEAN            => 'boolean',
            Schema::TYPE_MONEY              => 'number',
            Schema::TYPE_UUID               => 'uuid',
            Schema::TYPE_UUIDPK             => 'uuid',
            Schema::TYPE_SERIAL             => 'number',
            Schema::TYPE_SERIAL_PK          => 'number',
        ];
        $this->loadDefaultValues();
        /** @var ColumnSchema[] $columns */
        $columns = static::getTableSchema()->columns;
        $relations = $this->relations();
        foreach ($relations as $relation) {
            $activeRelation = $this->getRelation($relation);
            foreach ($activeRelation->link as $left => $right) {
                //$columns[$attribute]->type === Schema::TYPE_INTEGER
                if (isset($columns[$right]) && isset($attributes[$right]) && $attributes[$right]) {
                    if (($formatMap[$columns[$right]->type] == 'number' && !is_numeric($attributes[$right])) ||
                        ($formatMap[$columns[$right]->type] == 'uuid' && !empty($attributes[$right]) && !UuidValidator::test($attributes[$right]))) {
                        $term       = trim($attributes[$right]);
                        $cacheKey   = base64_encode($term);
                        if (isset(static::$importSearchCache[$cacheKey])) {
                            $attributes[$right] = static::$importSearchCache[$cacheKey];
                        } else {
                            /** @var BaseModel $modelClass */
                            $modelClass = $activeRelation->modelClass;
                            if (in_array('name', $modelClass::getTableSchema()->columnNames)) {
                                $id = $modelClass::find()->andFilterWhere(['name' => trim($attributes[$right])])->select(['id' => 'id'])->scalar();
                                if (!$id) {
                                    throw new IntegrityException("Не найдена запись для $right [Искомая запись '$attributes[$right]']");
                                } else {
                                    $attributes[$right] = $id;
                                    static::$importSearchCache[$cacheKey] = $id;
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($attributes as $attribute => $value) {
            if (!isset($formatMap[$columns[$attribute]->type])) {
                continue;
            }
            switch ($formatMap[$columns[$attribute]->type]) {
                case "boolean":
                    $value = ($value && trim($value) && intval($value,10) > 0 && trim($value) != '' && strtolower(trim($value)) != 'false');
                    break;
                case "number":
                    $value = str_ireplace(",", "." , $value);
                    break;
                case "datetime":
                    $value = str_ireplace(",", "." , $value);
                    break;
            }
            $attributes[$attribute] = $value;
        }

        $this->setAttributes($attributes);
        $toReplace = $this->replaceByUniqueIndex();
        if ($toReplace) {
            $this->setAttributes($toReplace->getAttributes());
            $this->oldAttributes = $toReplace->oldAttributes;
            $this->setAttributes($attributes);
        }

        if (!$this->validate()) {
            throw new InvalidValueException(Json::encode($this->errors));
        }
        $this->save();
        return $this;
    }
}