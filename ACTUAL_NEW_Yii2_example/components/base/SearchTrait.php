<?php
namespace app\components\base;

use app\components\crud\Formatter;
use app\components\DateTime;
use app\components\Schema;
use app\models\enum\Status;
use app\validators\UuidValidator;
use netis\crud\db\ActiveQuery;
use netis\crud\web\EnumCollection;
use yii;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\Json;

trait SearchTrait
{
    public $csv_filter;

    public $searchRelationsValues = [];
    protected static $csvFilterRedisKeyPrefix = 'search-csv-filter#';

    public function search($searchParams = [])
    {
        /** @var ActiveQuery $query */
        $query = static::find()->alias('t');

        $this->scenario     = static::SCENARIO_SEARCH;

        $searchParams       = static::clearSearchQuery($searchParams);

        if (isset($searchParams[$this->formName()])) {
            if ((!isset($searchParams[$this->formName()]['status_id']) || !$searchParams[$this->formName()]['status_id']) && $this->hasAttribute('status_id')) {
                if (isset(Yii::$app->request->isAjax) && (!Yii::$app->request->isAjax || Yii::$app->request->isPjax)) {
                    $searchParams[$this->formName()]['status_id'] = Status::STATUS_ACTIVE;
                }
            }
        } else {
            if ($this->hasAttribute('status_id')) {
                $query->andWhere(['t.status_id' => Status::STATUS_ACTIVE]);
            }
        }

        $this->load($searchParams);

        if (isset($searchParams['search'])) {
            $this->quickSearchTokenSetup($searchParams['search'], $query);
        }

        $this->validate();

        $this->addAttributesSearchConditions($query);

        return $query;
    }

    public function getAppliedSearchFilters() {
        $filters = [];
        $attributes         = $this->attributes();
        $validAttributes    = array_diff($attributes, array_keys($this->getErrors()));
        $attributeValues    = $this->getAttributes($validAttributes);

        foreach ($validAttributes as $attribute) {
            $value = $attributeValues[$attribute];
            if (!empty($value)) {
                $filters[$attribute] = $value;
            }
        }
        foreach ($this->searchRelationsValues as $attribute => $value) {
            if (!empty($value)) {
                $filters[$attribute] = $value;
            }
        }
        if (!empty($this->csv_filter)) {
            $filters['csv_filter'] = $this->csv_filter;
        }

        return $filters;
    }
    
    public static function setCsvFilterData($key, $name = 'File', $records) {
        $redisKey = static::$csvFilterRedisKeyPrefix . $key;
        $expire = 3600;
        Yii::$app->redis->executeCommand('SET', [$redisKey, Json::encode($records)]);
        Yii::$app->redis->executeCommand('SET', [$redisKey.'.name', $name]);
        Yii::$app->redis->executeCommand('SET', [$redisKey.'.count', count($records)]);
        Yii::$app->redis->executeCommand('EXPIRE', [$redisKey, $expire]);
        Yii::$app->redis->executeCommand('EXPIRE', [$redisKey.'.name', $expire]);
        Yii::$app->redis->executeCommand('EXPIRE', [$redisKey.'.count', $expire]);
    }
    public static function getCsvFilterData($key) {
        $redisKey = static::$csvFilterRedisKeyPrefix . $key;
        $records = Yii::$app->redis->executeCommand('GET', [$redisKey]);
        if (!$records) {
            return null;
        }
        return Json::decode($records, true);
    }
    public static function getCsvFilterName($key) {
        $redisKey = static::$csvFilterRedisKeyPrefix . $key;
        return Yii::$app->redis->executeCommand('GET', [$redisKey.'.name']);
    }
    public static function getCsvFilterCount($key) {
        $redisKey = static::$csvFilterRedisKeyPrefix . $key;
        return Yii::$app->redis->executeCommand('GET', [$redisKey.'.count']);
    }

    public function quickSearchTokenSetup($token, $query) {
        if (is_array($token)) {
            $this->setAttributes($token, false);
            return $query;
        }
        if (UuidValidator::test($token)) {
            $columns = static::getTableSchema()->columns;
            if (isset($columns['id']) && ($columns['id']->type == Schema::TYPE_UUID ||$columns['id']->type == Schema::TYPE_UUIDPK)) {
                $this->setAttribute('id', $token);
                return $query;
            }
        }
        if ($this->hasAttribute('number') && is_numeric($token)) {
            $this->setAttribute('number', $token);
        } else if ($this->hasAttribute('name')) {
            $this->setAttribute('name', $token);
        }
        return $query;
    }

    /**
     * Use a distinct compare value for each column. Primary and foreign keys support multiple values.
     * @param \yii\db\ActiveQuery $query
     * @return \yii\db\ActiveQuery
     */
    protected function addAttributesSearchConditions(\yii\db\ActiveQuery $query)
    {
        $tablePrefix    = $this->getDb()->getSchema()->quoteSimpleTableName('t');
        $conditions     = ['and'];
        $formats        = $this->attributeFormats();
        $attributes     = $this->attributes();
        $relations      = $this->relations();
        $validAttributes = array_diff($attributes, array_keys($this->getErrors()));
        $attributeValues = $this->getAttributes($validAttributes);
        $formatter = Yii::$app->formatter;

        /** @var EnumCollection $enums */
        $enums = $formatter instanceof Formatter ? $formatter->getEnums() : null;

        foreach ($validAttributes as $attribute) {
            $value = $attributeValues[$attribute];
            if ($value === null || $value === "" || !isset($formats[$attribute]) || ($enums !== null && !is_array($formats[$attribute]) && $enums->has($formats[$attribute]))) {
                continue;
            }
            if (in_array($attribute, $relations)) {
                // only hasMany relations should be ever marked as valid attributes
                $conditions[] =  $this->getRelationCondition($this->getRelation($attribute), $value);
            } else {
                $conditions[] =  $this->getAttributeCondition($attribute, $value, $formats, $tablePrefix, $this->getDb());
            }
        }
        $relationConditions = [];
        foreach ($this->searchRelationsValues as $attribute => $value) {
            if (!empty($value)) {
                /** @var \yii\db\ActiveQuery $relation */
                list($relationName, $attribute, $relation) = $this->getRelatedByAttributeName($attribute);
                if ($relationName) {
                    if (!isset($relationConditions[$relationName])) {
                        $relationConditions[$relationName] = ['conditions' => [], 'relation' => $relation];
                    }
                    $relationConditions[$relationName]['conditions'][$attribute] = $value;
                }
            }
        }
        if (count($relationConditions) > 0) {
            foreach ($relationConditions as $relationCondition) {
                $conditions[] =  $this->getRelationCondition($relationCondition['relation'], null, $relationCondition['conditions']);
            }
        }

        if ($this->csv_filter) {
            $records = static::getCsvFilterData($this->csv_filter);

            if (!$records) {
                throw new yii\base\Exception("Файл-фильтр был удалён по истечению срока годности. Перезагрузите файл-фильтр.");
            } else {
                if (count(array_keys($records[0])) == 1) {
                    $recordValue = [];
                    $recordKey = null;
                    foreach ($records as $record) {
                        foreach ($record as $key => $value) {
                            $recordKey = $key;
                            $recordValue[] = $value;
                        }
                    }
                    $conditions[] = $this->getAttributeCondition($recordKey, $recordValue, $formats, $tablePrefix, $this->getDb());
                } else {
                    $orC = ['or'];
                    foreach ($records as $record) {
                        $andC = ['and'];
                        foreach ($record as $recordKey => $recordValue) {
                            if (in_array($recordKey, $attributes)) {
                                $andC[] = $this->getAttributeCondition($recordKey, $recordValue, $formats, $tablePrefix, $this->getDb());
                            }
                        }
                        if ($andC !== ['and']) {
                            $orC[] = $andC;
                        }
                    }
                    if ($orC !== ['or']) {
                        $conditions[] = $orC;
                    }
                }
            }

        }
        

        //don't clear attributes to allow rendering filled search form
        //$this->setAttributes(array_fill_keys($attributes, null));

        if ($conditions !== ['and']) {
            $query->andFilterWhere($conditions);
        }
        return $query;
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $formats
     * @param string $tablePrefix
     * @param Connection $db
     * @return array in format supported by Query::where()
     */
    protected function getAttributeCondition($attribute, $value, $formats, $tablePrefix, $db)
    {
        $likeOp = $db->driverName === 'pgsql' ? 'ILIKE' : 'LIKE';

        $columnName = $tablePrefix . '.' . $db->getSchema()->quoteSimpleColumnName($attribute);
        switch ($formats[$attribute]) {
            case 'datetime':
            case 'date':
                $value = preg_replace("/\+/", ' ', $value);
                $checkd = preg_replace("/[^\d\-\s]/", '', $value);
                if (preg_match("/^\d\d\d\d\-?\d\d\-?\d\d(\s\d\d\d\d\d\d)?\s\-\s\d\d\d\d\-?\d\d\-?\d\d(\s\d\d\d\d\d\d)?$/", $checkd)) {
                    $dates = explode(' - ', $value);
                    $checkd = explode(' - ', $checkd);
                    $dateFormat = $formats[$attribute] === 'datetime' ? DateTime::DB_DATETIME_FORMAT : DateTime::DB_DATE_FORMAT;
                    if (!preg_match("/^\d\d\d\d\-?\d\d\-?\d\d\s\d\d\d\d\d\d?$/", $checkd[0])) {
                        $dates[0] = date($dateFormat, strtotime('midnight', strtotime($dates[0])));
                    } else {
                        $dates[0] = date($dateFormat, strtotime($dates[0]));
                    }
                    if (!preg_match("/^\d\d\d\d\-?\d\d\-?\d\d\s\d\d\d\d\d\d?$/", $checkd[1])) {
                        $dates[1] = date($dateFormat, strtotime('tomorrow', strtotime($dates[1])) - 1);
                    } else {
                        $dates[1] = date($dateFormat, strtotime($dates[1]));
                    }
                    return ['between', $columnName, $dates[0], $dates[1]];
               }
            case 'uuid':
                if ($value) {
                    if (!is_array($value) && strpos($value,',') > -1) {
                        return [$columnName => explode(',', $value)];
                    }
                    if ($value{0} === '!' || $value{0} === '-') {
                        $value = substr($value, (strlen($value) > 1 && $value{1} === '=') ? 2 : 1);
                        if (trim($value) === '') {
                            return new \yii\db\Expression("$columnName IS NOT NULL");
                        }
                        return ['!=', $columnName, $value];
                    }
                    if ($value === 'null') {
                        return "$columnName IS NULL";
                    }
                }
                return [$columnName => $value];
            case 'string':
            case 'text':
            case 'email':
            case 'url':
                if ($value{0} === '!' || $value{0} === '-') {
                    $value = substr($value, (strlen($value) > 1 && $value{1}==="=") ? 2 : 1);
                    if (trim($value) === '') {
                        return new \yii\db\Expression("$columnName IS NOT NULL AND $columnName != ''");
                    }
                    return ['not', [$likeOp, $columnName, $value]];
                }
                if (is_array($value)) {
                    $result = ['or'];
                    foreach ($value as $token) {
                        $result[] = [$likeOp, $columnName, $token];
                    }
                    return $result;
                }
                return [$likeOp, $columnName, $value];
            case 'json':
            case 'jsonb':
                if (!is_array($value)) {
                    $value = explode(',', $value);
                }
                $value = implode("','", $value);
                $value = "'$value'";
                return new yii\db\Expression($columnName." @> ANY (ARRAY [$value]::jsonb[])");

            default:

                if (!is_array($value) && strpos($value,',') > -1) {
                    $value = explode(',', $value);
                }

                if (!is_string($value) || strlen($value) < 2 || ($value{0} !== '>' && $value{0} !== '<' && $value{0} !== '!'&& $value{0} !== '@')) {
                    return [$columnName => $value];
                }

                $op = substr($value, 0, (strlen($value) > 1 && $value{1} !== '=') ? 1 : 2);
                $value  = substr($value, strlen($op));
                if (trim($value) === '') {
                    if ($op == "!=") {
                        return new \yii\db\Expression("$columnName IS NOT NULL");
                    }
                    if ($op == "@=") {
                        return new \yii\db\Expression("$columnName IS NULL");
                    }
                    return [];
                }
                return [$op, $columnName, $value];
                break;
        }
    }

    /**
     * Only hasMany relations should be ever marked as valid attributes.
     * @param \yii\db\ActiveQuery $relation
     * @param array $value
     * @param array $attributes
     * @return array an IN condition with a subquery
     */
    protected function getRelationCondition($relation, $value, $attributes = null)
    {
        $tablePrefix    = $this->getDb()->getSchema()->quoteSimpleTableName('t');
        /** @var BaseModel $relationClass */
        $relationClass = $relation->modelClass;
        if (is_array($attributes)) {
            $where = ['and'];
            /** @var BaseModel $secondarySearchModel */
            $secondarySearchModel = new $relationClass();
            $secondarySearchModel->loadDefaultValues();
            foreach ($attributes as $attribute => $val){
                $where[] = $this->getAttributeCondition($attribute, $val, $secondarySearchModel->attributeFormats(), $tablePrefix, $this->getDb());
            }
        } else {
            $where = ['IN', array_map(function ($key) {
                return 't.' . $key;
            }, $relationClass::primaryKey()), $value];
        }
        if ($relation->via !== null) {
            /* @var $viaRelation \yii\db\ActiveQuery */
            $viaRelation = is_array($relation->via) ? $relation->via[1] : $relation->via;
            /** @var \yii\db\ActiveRecord $viaClass */
            $viaClass = $viaRelation->modelClass;
            $subquery = (new Query)
                ->select(array_map(function ($key) {
                    return 'j.' . $key;
                }, array_keys($viaRelation->link)))
                ->from(['t' => $relationClass::tableName()])
                ->innerJoin(['j' => $viaClass::tableName()], implode(' AND ', array_map(function ($leftKey, $rightKey) {
                    return 't.' . $leftKey .' = j.' . $rightKey;
                }, array_keys($relation->link), array_values($relation->link))))
                ->where($where);
            $linkKeys = array_values($viaRelation->link);
        } else {
            $subquery = (new Query)
                ->select(array_keys($relation->link))
                ->from(['t' => $relationClass::tableName()])
                ->where($where);
            $linkKeys = array_values($relation->link);
        }
        return ['IN', array_map(function ($key) {
            return 't.' . $key;
        }, $linkKeys), $subquery];
    }

    public static function clearSearchQuery($params) {
        if (isset($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                if (empty($value) && $value != '0') {
                    unset($params[$key]);
                } else if (!empty($value)) {
                    if (!is_array($value)) {
                        if (strpos($value, ',') !== FALSE) {
                            $params[$key] = explode(',', $value);
                        }
                    } else {
                        $params[$key] = static::clearSearchQuery($value);
                    }
                }
            }
        }
        return $params;
    }

}
