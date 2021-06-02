<?php
namespace app\components\base;

use app\components\DateTime;
use app\components\Schema;
use app\models\enum\Status;
use app\models\reference\Schedule;
use netis\crud\db\ActiveQuery;
use ReflectionClass;
use \netis\crud\db\ActiveRecord;
use yii;

class BaseModel extends ActiveRecord {

    use SearchTrait;
    use CrudRecordTrait;
    use FileExchangeTrait;

    const SCENARIO_PRE_IMPORT   = 'pre_import';
    const SCENARIO_IMPORT       = 'import';
    const SCENARIO_EXPORT       = 'export';
    const SCENARIO_SEARCH       = 'search';
    const SCENARIO_UPDATE       = 'update';
    const SCENARIO_CREATE       = 'create';

    /**
     * Рабтать ли с данной таблицей как с большими данными (особые условия поиска, не выводить сразу все в дропдаун и т.д.)
     * @return bool
     */
    public static function isBigData() {
        return true;
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
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        foreach ([
                     self::SCENARIO_CREATE,
                     self::SCENARIO_UPDATE,
                     self::SCENARIO_SEARCH,
                     self::SCENARIO_IMPORT,
                     self::SCENARIO_EXPORT,
                     self::SCENARIO_PRE_IMPORT
                 ] as $scenario) {

            if (!isset($scenarios[$scenario])) {
                $scenarios[$scenario] = $scenarios[self::SCENARIO_DEFAULT];
            }
        }
        return $scenarios;
    }

    public function onUnsafeAttribute($name, $value)
    {
        if (!is_numeric($name) && property_exists($this, $name)) {
            if ($this->scenario == self::SCENARIO_SEARCH) {
                $this->$name = $value;
            } else {
                parent::onUnsafeAttribute($name, $value);
            }
        }
    }

    public function setAttributes($values, $safeOnly = true)
    {
        parent::setAttributes($values, ($this->scenario == self::SCENARIO_SEARCH) ? true : $safeOnly);
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        if ($this->scenario == self::SCENARIO_SEARCH) {
            return true;
        }
        return parent::validate($attributeNames, $clearErrors);
    }

    /**
     * Returns  fields list by combining model attributes with hasOne relations or just hasMany relations.
     * @param bool $extra if false, returns attributes and hasOne relations, if true, returns only hasMany relations
     * @return array default list of fields
     */
    public function getFields($extra = false)
    {

        $fields = $extra ? [] : $this->attributes();

        foreach ($this->relations() as $relation) {
            $activeRelation = $this->getRelation($relation);
            if ((!$extra && $activeRelation->multiple) || ($extra && !$activeRelation->multiple)) {
                continue;
            }
            $fields[] = $relation;
        }

        return $fields;
    }

    /**
     * @param \yii\web\Request|\yii\console\Request $request
     * @return bool
     */
    public function loadFromRequest($request) {
        $this->load($request->getQueryParams());
        return $this->load($request->getBodyParams());
    }

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return (new ReflectionClass(self::className()))->getShortName();
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return (new ReflectionClass(self::className()))->getShortName();
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return self::getSingularNominativeName().' №'.$this->id;
    }

    /**
     * Удалить
     * @throws \Exception
     */
    public function recycle() {
        if ($this->hasAttribute('status_id')) {
            $this->setAttribute('status_id', Status::STATUS_REMOVED);
            return $this->save();
        } else {
            return $this->delete();
        }
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            [
                [['csv_filter'], 'safe', 'on' => self::SCENARIO_SEARCH]
            ]);
    }
    
    /**
     * @inheritdoc
     */
    public function filteringRules()
    {
        return array_merge(parent::filteringRules());
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if ($this instanceof ScheduleInterface) {
                Schedule::deleteAll([
                    'requester_entity_id' => Entity::getIdByClassName($this->className()),
                    'requester_id' => $this->id
                ]);
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert && isset($changedAttributes['status_id']) && $this instanceof ScheduleInterface) {
            if ($this->hasAttribute('status_id')) {
                Schedule::updateAll([
                    'status_id' => $this->getAttribute('status_id')
                ], [
                    'requester_entity_id'   => Entity::getIdByClassName($this->className()),
                    'requester_id'          => $this->id
                ]);
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $parent = parent::behaviors();

        return array_merge($parent, [
            'labels' => [
                'class' => 'app\components\crud\behaviors\LabelsBehavior',
                'attributes' => [$this->labelAttribute],
                'crudLabels' => [
                    'default'  =>  static::getPluralNominativeName(),
                    'relation' =>  static::getSingularNominativeName(),
                    'index'    =>  static::getPluralNominativeName(),
                    'create'   => 'Создать '.static::getSingularNominativeName(),
                    'read'     =>  static::getSingularNominativeName(),
                    'update'   => 'Изменить '.static::getSingularNominativeName(),
                    'delete'   => 'Удалить '.static::getSingularNominativeName(),
                    'import'   => 'Импортировать '.static::getPluralNominativeName(),
                    'export'   => 'Экспортировать '.static::getPluralNominativeName(),
                ],
            ],
        ]);
    }


    public function getRelatedByAttributeName($name) {
        $parts = explode('.', $name);
        if (count($parts) > 1 && in_array($parts[0], $this->relations())) {
            $relation   = $parts[0];
            $attr       = $parts[1];
            return [$relation, $attr,  $this->getRelation($relation)];
        }
        return [null, null, null];
    }

    /**
     * Магическая функция для получения свойства объекта
     *
     * @param string $name наименование свойства
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->getDb()->getTableSchema($this->tableName())->columns)) {
            $columns = $this->getDb()->getTableSchema($this->tableName())->columns;

            if (isset($columns[$name])) {
                $column = $columns[$name];
                if ($column->type == Schema::TYPE_UUID ||
                    $column->type == Schema::TYPE_UUIDPK) {
                    $value = parent::__get($name);
                    return ($value == "") ? null : $value;
                }
                if (in_array($column->type, [Schema::TYPE_DATETIME, Schema::TYPE_TIMESTAMP, Schema::TYPE_DATE])) {
                    $value = parent::__get($name);
                    if (is_null($value)) {
                        return null;
                    } elseif(is_array($value) && isset($value['date'])) {
                        return new DateTime(str_ireplace('.000000', '', $value['date']), $value['withTime']);
                    } elseif (strtotime($value) !== false) {
                        return new DateTime($value, in_array($column->type, [Schema::TYPE_DATETIME, Schema::TYPE_TIMESTAMP]));
                    }
                }
            } else if ($name != 'scenario' && $this->scenario == self::SCENARIO_SEARCH) {
                if (isset($this->searchRelationsValues[$name])) {
                    return $this->searchRelationsValues[$name];
                }
                // Обращение к свойству рилейшена для поиска
                list($relationName, $attr) = $this->getRelatedByAttributeName($name);
                if ($relationName) {
                    if ($relatedObject = $this->$relationName) {
                        return $this->$relationName->$attr;
                    } else {
                        return null;
                    }
                }
            }
        }
        return parent::__get($name);
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if (isset($this->getDb()->getTableSchema($this->tableName())->columns)) {
            $columns = $this->getDb()->getTableSchema($this->tableName())->columns;
            if (isset($columns[$name])) {
                $column = $columns[$name];
                if ((in_array($column->type, [Schema::TYPE_DATETIME, Schema::TYPE_TIMESTAMP, Schema::TYPE_DATE])) && ($value instanceof DateTime)) {
                    $value = (string)$value;
                }
            } else if ($name != 'scenario' && $this->scenario == self::SCENARIO_SEARCH) {
                // Обращение к свойству рилейшена для поиска
                list($relationName, $attr) = $this->getRelatedByAttributeName($name);
                if ($relationName) {
                    $this->searchRelationsValues[$name] = $value;
                    return;
                }
            }
        }
        parent::__set($name, $value);
    }

    /**
     * @inheritdoc
     * @return ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ActiveQuery(get_called_class());
    }

    public function getDefaultOrderColumns() {
        return [];
    }

    /**
     * Creates a Sort object configuration using query default order.
     * @param array $overlayConfig
     * @return array
     */
    public function getSort($overlayConfig = [])
    {
        $sort = [
            'enableMultiSort'   => true,
            'attributes'        => [],
            'defaultOrder'      => $this->getDefaultOrderColumns(),
        ];

        $attributes = $this->attributes();

        foreach ($attributes as $attribute) {
            $sort['attributes'][$attribute] = [
                'asc'   => array_merge([$attribute => SORT_ASC]),
                'desc'  => array_merge([$attribute => SORT_DESC]),
            ];
        }
        
        foreach ($overlayConfig as $key => $value) {
            $sort[$key] = array_merge($sort[$key], $value);
        }

        return $sort;
    }

    
    public function attributeFormatMapping() {
        
    }
    
    /**
     * Returns the attribute formats. Possible formats include:
     * - text: string, text, email, url
     * - numbers: boolean, smallint, integer, bigint, float, decimal, money, currency, minorCurrency
     * - dates and time: datetime, timestamp, time, date, interval
     * - others: binary.
     *
     * Attribute formats are mainly used for display purpose. For example, given an attribute
     * `price` based on an integer column, we can declare a format `money`, which can be used
     * in grid column or detail attribute definitions.
     *
     * Default formats are detected by analyzing database columns.
     *
     * Note, in order to inherit formats defined in the parent class, a child class needs to
     * merge the parent formats with child formats using functions such as `array_merge()`.
     *
     * Note, when defining enum formats, remember to add an `in` validator to the rules.
     *
     * @return array attribute formats (name => format)
     */
    public function attributeFormats()
    {
        $columns = static::getTableSchema()->columns;
        $attributes = $this->attributes();
        $formatMap = [
            Schema::TYPE_PK                 => 'integer',
            Schema::TYPE_BIGPK              => 'integer',
            Schema::TYPE_STRING             => 'text',
            Schema::TYPE_TEXT               => 'paragraphs',
            Schema::TYPE_SMALLINT           => 'integer',
            Schema::TYPE_INTEGER            => 'integer',
            Schema::TYPE_BIGINT             => 'integer',
            Schema::TYPE_FLOAT              => 'integer',
            Schema::TYPE_DOUBLE             => 'integer',
            Schema::TYPE_DECIMAL            => 'decimal',
            Schema::TYPE_DATETIME           => 'datetime',
            Schema::TYPE_TIMESTAMP          => 'datetime',
            Schema::TYPE_TIME               => 'time',
            Schema::TYPE_DATE               => 'date',
            Schema::TYPE_BINARY             => 'text',
            Schema::TYPE_BOOLEAN            => 'boolean',
            Schema::TYPE_MONEY              => 'currency',
            Schema::TYPE_UUID               => 'uuid',
            Schema::TYPE_UUIDPK             => 'uuid',
            Schema::TYPE_ENUM_DATA_FORMAT   => 'enum',
            Schema::TYPE_JSON               => 'json',
            Schema::TYPE_JSONB              => 'jsonb',
        ];
        $nameMap = [
            'percent', 'email', 'url',
        ];
        $formats = [];
        foreach ($attributes as $attribute) {
            if (!isset($columns[$attribute])) {
                $formats[$attribute] = 'raw';
                continue;
            }
            $type = $columns[$attribute]->type;
            if ($columns[$attribute]->dbType === 'interval') {
                $formats[$attribute] = 'interval';
                continue;
            }
            foreach ($nameMap as $name) {
                if (!strcasecmp($attribute, $name)) {
                    $formats[$attribute] = $name;
                    continue;
                }
            }
            if (!strcasecmp($attribute, 'price') && $columns[$attribute]->type !== Schema::TYPE_STRING) {
                if ($columns[$attribute]->type === Schema::TYPE_INTEGER) {
                    $formats[$attribute] = 'minorCurrency';
                } else {
                    $formats[$attribute] = 'currency';
                }
                continue;
            }
            $formats[$attribute] = !isset($formatMap[$type]) ? 'text' : $formatMap[$type];
        }
        return $formats;
    }
}