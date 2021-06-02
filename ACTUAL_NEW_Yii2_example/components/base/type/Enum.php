<?php

namespace app\components\base\type;
use app\components\base\BaseModel;
use app\components\ValidationRules;
use app\models\enum\Status;
use yii;
use yii\db\IntegrityException;
use yii\helpers\ArrayHelper;

/**
 * @property int id
 * @property string name
 * @property string status_id
 *
 * @property Status status
 */
class Enum extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function isBigData() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function noCount() {
        return false;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            [
                [['id'], 'number', 'integerOnly' => true, 'except' => self::SCENARIO_SEARCH],
                [['id'], 'safe', 'on' => self::SCENARIO_SEARCH],
                [['name'], 'string'],
            ],
            ValidationRules::ruleRequired('name'),
            ValidationRules::ruleDefault('status_id', Status::STATUS_ACTIVE),
            ValidationRules::ruleEnum('status_id', Status::className())
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'id'            => 'ID',
                'name'          => 'Название',
                'status_id'     => 'ID Состояния',
                'status'        => 'Состояние',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function fileImportPresetColumns()
    {
        return [
        ];
    }

    /**
     * Не импортировать колонки
     * @return array
     */
    public function excludeFieldsFileImportColumns() {
        return [
            'status_id',
            'status',
        ];
    }

    /**
     * Загрузка сущностей из кэша
     * @return array
     */
    protected static function loadFromCache()
    {
        $tableName = Yii::$app->db->schema->getRawTableName(static::tableName());
        if (!($cache = Yii::$app->cache->get('#' . $tableName . '#'))) {
            $enums = static::find()->all();
            $cache['idByName']  = [];
            $cache['nameById']  = [];
            $cache['byId']      = [];
            /** @var Enum $enum */
            foreach ($enums as $enum) {
                $cache['idByName'][$enum->name] = $enum->id;
                $cache['nameById'][$enum->id]   = $enum->name;
                $cache['byId'][$enum->id]       = $enum->toArray();
            }
            Yii::$app->cache->set('#' . $tableName . '#', $cache, 0);
        }
        return $cache;
    }

    /**
     * Возвращает идентификатор строки по ее имени
     *
     * @param string $name
     * @param bool $isThrowException выбрасывать ошибку
     *
     * @return string
     * @throws IntegrityException
     *
     */
    public static function getIdByName($name, $isThrowException = true)
    {
        $cache = static::loadFromCache();
        if (isset($cache['idByName'][$name])) {
            return $cache['idByName'][$name];
        }
        if ($isThrowException) {
            throw new IntegrityException('Строки с именем "' . $name . '" не существует');
        } else {
            return false;
        }
    }

    /**
     * Возвращает название строки по ее идентификатору
     *
     * @param integer $id
     *
     * @param bool $isThrowException выбрасывать ошибку
     * @return string
     * @throws IntegrityException
     */
    public static function getNameById($id, $isThrowException = true) {
        if ($id === null) {
            return '(не указано)';
        }
        $cache = static::loadFromCache();
        if (isset($cache['nameById'][$id])) {
            return $cache['nameById'][$id];
        }
        if ($isThrowException) {
            throw new IntegrityException('Строки с идентификатором "' . $id . '" не существует');
        } else {
            return false;
        }
    }

    /**
     * Возвращает ID - Имя
     *
     * @param bool $isThrowException выбрасывать ошибку
     * @return array
     * @throws IntegrityException
     */
    public static function getEnumList($isThrowException = true)
    {
        $cache = static::loadFromCache();
        if (isset($cache['nameById'])) {
            return $cache['nameById'];
        }
        if ($isThrowException) {
            throw new IntegrityException('Список отсутствует');
        } else {
            return false;
        }
    }

    /**
     * Возвращает ID - запись
     *
     * @param bool $isThrowException выбрасывать ошибку
     * @return array
     * @throws IntegrityException
     */
    public static function getEnumArray($isThrowException = true)
    {
        $cache = static::loadFromCache();
        if (isset($cache['byId'])) {
            return $cache['byId'];
        }
        if ($isThrowException) {
            throw new IntegrityException('Список отсутствует');
        } else {
            return false;
        }
    }

    /**
     * Сброс кэша
     */
    public static function resetCache()
    {
        Yii::$app->cache->delete('#' . Yii::$app->db->schema->getRawTableName(static::tableName()) . '#');
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return [
            'status'
        ];
    }
    
    /**
     * @return Status
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }
}