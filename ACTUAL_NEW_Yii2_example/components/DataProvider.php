<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 13.09.16
 * Time: 11:23
 */

namespace app\components;

use app\components\base\BaseModel;
use yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\QueryInterface;

class DataProvider extends ActiveDataProvider
{
    public $countBy = '*';
    public $noKeys = false;

    public $resetCountCache = false;
    /**
     * @inheritdoc
     */
    protected function prepareTotalCount()
    {
        if (!$this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }

        /** @var ActiveQuery $query */
        $query = clone $this->query;
        $query->limit(-1)->offset(-1)->orderBy([]);

        $needCaching = false;

        if ($query instanceof ActiveQuery && $query->modelClass && method_exists($query->modelClass, 'noCount')) {
            //$needCaching = forward_static_call([$query->modelClass, 'dataProviderCountNeedCaching']);
            $needCaching = forward_static_call([$query->modelClass, 'noCount']);
        }

        if ($needCaching) {
            return 1000000;
            //$tableName = forward_static_call([$query->modelClass, 'tableName']);
           // return Yii::$app->getDb()->createCommand("SELECT 100 * count(*) AS estimate FROM $tableName TABLESAMPLE SYSTEM (1);")->queryScalar();
            $query2 = clone $query;
            $hash = md5($query2->createCommand()->rawSql);
            $key = "crud::count#" . $hash;
            if (!$this->resetCountCache && Yii::$app->cache->exists($key)) {
                return Yii::$app->cache->get($key);
            }
            $count = (int) $query->count($this->countBy, $this->db);
            Yii::$app->cache->set($key, $count, 60);
        } else {
            $count = (int) $query->count($this->countBy, $this->db);
        }

        return $count;
    }

    public function resetTotalCountCache() {
        $this->resetCountCache = true;
        return $this->prepareTotalCount();
    }

    /**
     * @inheritdoc
     */
    protected function prepareKeys($models)
    {
        $keys = [];
        if ($this->noKeys) {
            return array_keys($models);
        }
        if ($this->key !== null) {
            foreach ($models as $model) {
                if (is_string($this->key)) {
                    $keys[] = $model[$this->key];
                } else {
                    $keys[] = call_user_func($this->key, $model);
                }
            }

            return $keys;
        } elseif ($this->query instanceof ActiveQueryInterface) {
            /* @var $class \yii\db\ActiveRecord */
            $class = $this->query->modelClass;
            $pks = $class::primaryKey();
            if (count($pks) === 1) {
                $pk = $pks[0];
                foreach ($models as $model) {
                    $keys[] = $model[$pk];
                }
            } else {
                foreach ($models as $model) {
                    $kk = [];
                    foreach ($pks as $pk) {
                        $kk[$pk] = $model[$pk];
                    }
                    $keys[] = $kk;
                }
            }

            return $keys;
        } else {
            return array_keys($models);
        }
    }
}