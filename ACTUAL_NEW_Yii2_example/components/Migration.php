<?php

namespace app\components;

use app\components\base\Entity;
use yii;
use yii\helpers\Inflector;

abstract class Migration extends \yii\db\Migration
{


    /**
     * Добавление первичного ключа
     *
     * @param string       $table      имя таблицы
     * @param string|array $columns    колонка/колонки
     */
    public function addPK($table, $columns) {
        $name = 'pk_' . strtolower($this->db->schema->getRawTableName($table)) . '_' . strtolower(is_array($columns) ? implode('_', $columns) : $columns);
        $this->addPrimaryKey($name, $table, $columns);
    }

    public function uuid() {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_UUID, null);
    }

    public function uuidpk() {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_UUIDPK, null);
    }

    /**
     * @return yii\db\ColumnSchemaBuilder
     */
    public function serial() {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_SERIAL, null);
    }

    public function createVirtualEntity($table, $entityId, $entityType, $entityTitle = null, $parentEntityId = null) {
        $tableName              = substr($this->db->schema->getRawTableName($table), strlen($this->db->tablePrefix));
        $entityShortClassName   = Inflector::id2camel($tableName,'_');
        $action                 = Inflector::camel2id($tableName);
        $entityClass            = 'app\\models\\'.$entityType.'\\'.$entityShortClassName;

        $this->insertEnum('{{%entity}}', [
            'id'            => $entityId,
            'name'          => $entityTitle,
            'class_name'    => $entityClass,
            'entity_type'   => $entityType,
            'action'        => $action,
            'alias'         => $entityShortClassName,
            'parent_id'     => $parentEntityId
        ]);
    }

    public function createEntity($table, $entityId, $entityType, $entityTitle = null, $columns = [], $createPk = null, $insert = [], $addStatus = null, $parentEntityId = null, $options = null)
    {
        $typeColumns = [
            Entity::ENTITY_TYPE_ENUM => [
                'id'            => Schema::TYPE_INTEGER.' NOT NULL',
                'name'          => Schema::TYPE_STRING.' NOT NULL'
            ],
            Entity::ENTITY_TYPE_REFERENCE => [
                'id'                => Schema::TYPE_UUIDPK.' NOT NULL',
                'name'              => Schema::TYPE_STRING.' NOT NULL',
                'created_at'        => Schema::TYPE_DATETIME.' NOT NULL',
                'updated_at'        => Schema::TYPE_DATETIME.' NOT NULL',
                'created_user_id'   => Schema::TYPE_INTEGER,
                'updated_user_id'   => Schema::TYPE_INTEGER
            ],
            Entity::ENTITY_TYPE_DOCUMENT => [
                'id'                => Schema::TYPE_UUIDPK.' NOT NULL',
                'number'            => Schema::TYPE_SERIAL.' NOT NULL',
                'name'              => Schema::TYPE_STRING,
                'created_at'        => Schema::TYPE_DATETIME.' NOT NULL',
                'updated_at'        => Schema::TYPE_DATETIME.' NOT NULL',
                'created_user_id'   => Schema::TYPE_INTEGER,
                'updated_user_id'   => Schema::TYPE_INTEGER
            ],
            Entity::ENTITY_TYPE_REGISTER => [
                'id'                => Schema::TYPE_UUIDPK.' NOT NULL',
                'created_at'        => Schema::TYPE_DATETIME.' NOT NULL',
                'updated_at'        => Schema::TYPE_DATETIME.' NOT NULL',
                'created_user_id'   => Schema::TYPE_INTEGER,
                'updated_user_id'   => Schema::TYPE_INTEGER
            ],
            Entity::ENTITY_TYPE_CROSS => [
                'id'                => Schema::TYPE_UUIDPK,
            ],
            Entity::ENTITY_TYPE_POOL => [
                'id'                => Schema::TYPE_UUIDPK.' NOT NULL',
                'created_at'        => Schema::TYPE_DATETIME.' NOT NULL',
            ],
        ];

        $tableName = substr($this->db->schema->getRawTableName($table), strlen($this->db->tablePrefix));
        $entityShortClassName = Inflector::id2camel($tableName,'_');
        $columns = array_merge($typeColumns[$entityType], $columns);

        if ($addStatus === null) {
            $addStatus = true;
            if ($entityType == Entity::ENTITY_TYPE_CROSS ||
                $entityType == Entity::ENTITY_TYPE_POOL) {
                $addStatus = false;
            }
        }

        if ($createPk === null) {
            $createPk = false;
            if ($entityType == Entity::ENTITY_TYPE_ENUM) {
                $createPk = true;
            }
        }

        if (!$entityTitle) {
            $entityTitle = Inflector::camel2words($entityShortClassName);
        }

        if ($addStatus) {
            $columns['status_id'] = Schema::TYPE_INTEGER." NOT NULL DEFAULT '0'";
        }

        $this->createTable($table, $columns, $options);

        if ($createPk) {
            $this->addPrimaryKey('pk_'.$tableName.'_id', $table, 'id');
        }

        if ($addStatus) {
            $this->addFK($table, 'status_id', '{{%status}}', 'id');
        }

        if (isset($columns['created_user_id'])) {
            $this->addFK($table, 'created_user_id', '{{%user}}', 'id');
        }
        if (isset($columns['updated_user_id'])) {
            $this->addFK($table, 'updated_user_id', '{{%user}}', 'id');
        }

        $this->createVirtualEntity($table, $entityId, $entityType, $entityTitle, $parentEntityId);

        if (count($insert) > 0) {
            foreach ($insert as $item) {
                $this->insert($table, $item);
            }
         }

        return $table;
    }

    public function multiInsert($table, $insert) {
        if (count($insert) > 0) {
            foreach ($insert as $item) {
                $this->insert($table, $item);
            }
        }
    }

    public function multiInsertColumn($table, $column, $insert) {
        if (count($insert) > 0) {
            foreach ($insert as $item) {
                $this->insert($table, [$column => $item]);
            }
        }
    }

    public function multiDelete($table, $column = 'id', $delete) {
        if (count($delete) > 0) {
            foreach ($delete as $item) {
                $this->delete($table, [$column => $item]);
            }
        }
    }
    public function dropVirtualEntity($table)
    {
        $tableName = substr($this->db->schema->getRawTableName($table), strlen($this->db->tablePrefix));
        $entityShortClassName = Inflector::id2camel($tableName, '_');
        $this->deleteEnum('{{%entity}}', [
            'alias' => $entityShortClassName,
        ]);
    }

    public function dropEntity($table)
    {
        $this->dropVirtualEntity($table);
        $this->dropTable($table);
    }

    /**
     * Возвращает опции для таблиц по умолчанию
     * @return null|string
     */
    protected function getDefaultTableOptions() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        return $tableOptions;
    }

    /**
     * Обновление кеша схемы таблицы
     *
     * @param string $table название таблицы
     */
    protected function refreshTableSchema($table) {
        $this->db->getTableSchema($table, true);
    }

    /**
     * Добавление внешнего ключа
     *
     * @param string       $table      имя таблицы
     * @param string|array $columns    колонка/колонки
     * @param string       $refTable   имя таблицы на которую ссылается ключ
     * @param string|array $refColumns колонка/колонки на которые ссылаются
     * @param string       $delete     реакция при удалении записи
     * @param string       $update     реакция при обновлении записи
     */
    public function addFK($table, $columns, $refTable, $refColumns, $delete = 'RESTRICT', $update = 'RESTRICT') {
        $name = 'fk_' . strtolower($this->db->schema->getRawTableName($table)) . '_' . strtolower(is_array($columns) ? implode('_', $columns) : $columns);
        $this->addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
    }

    /**
     * Удаление внешнего ключа
     *
     * @param string        $table   имя таблицы
     * @param string|array  $columns колонка/колонки
     * @param string        $ref_table колонка/колонки
     * @param string|array  $ref_column колонка/колонки
     */
    public function delFK($table, $columns, /** @noinspection PhpUnusedParameterInspection */ $ref_table = null, /** @noinspection PhpUnusedParameterInspection */ $ref_column = null) {
        $name = 'fk_' . strtolower($this->db->schema->getRawTableName($table)) . '_' . strtolower(is_array($columns) ? implode('_', $columns) : $columns);
        $this->dropForeignKey($name, $table);
    }

    /**
     * Добавление индекса
     * @param string       $table   имя таблицы
     * @param string|array $columns колонка/колонки
     * @param boolean      $unique  уникальность индекса
     */
    public function addIndex($table, $columns, $unique = false)
    {
        $name = ($unique ? 'u' : 'i') . 'x_' . strtolower($this->db->schema->getRawTableName($table)) . '_' . strtolower(is_array($columns) ? implode('_', $columns) : $columns);
        parent::createIndex($name, $table, $columns, $unique);
    }

    /**
     * Удаление индекса
     * @param string       $table   имя таблицы
     * @param string|array $columns колонка/колонки
     * @param boolean      $unique  уникальность индекса
     */
    public function delIndex($table, $columns, $unique = false)
    {
        $name = ($unique ? 'u' : 'i') . 'x_' . strtolower($this->db->schema->getRawTableName($table)) . '_' . strtolower(is_array($columns) ? implode('_', $columns) : $columns);
        parent::dropIndex($name, $table);
    }

    /**
     * Заносим новую строку для Enum и сбрасываем кэш
     * @param string       $table   имя таблицы
     * @param string|array $columns колонка/колонки
     */
    public function insertEnum($table, $columns)
    {
        parent::insert($table, $columns);
        Yii::$app->cache->delete('#' . $this->db->schema->getRawTableName($table) . '#');
    }

    /**
     * Обновляем строку Enum и сбрасываем кэш
     * @param string $table имя таблицы
     * @param array $columns новые значения колонок (name => value)
     * @param string|array $condition условия указания строк которые надо обновить
     * @param array $params массив параметров для условия (name => value)
     */
    public function updateEnum($table, $columns, $condition = '', $params = [])
    {
        parent::update($table, $columns, $condition, $params);
        Yii::$app->cache->delete('#' . $this->db->schema->getRawTableName($table) . '#');
    }

    /**
     * Удаляем строку Enum и сбрасываем кэш
     * @param string $table the table where the data will be deleted from.
     * @param array|string $condition the conditions that will be put in the WHERE part. Please
     * refer to [[Query::where()]] on how to specify conditions.
     * @param array $params the parameters to be bound to the query.
     */
    public function deleteEnum($table, $condition = '', $params = [])
    {
        parent::delete($table, $condition, $params);
        Yii::$app->cache->delete('#' . $this->db->schema->getRawTableName($table) . '#');
    }

    /**
     * @inheritdoc
     */
    public function createTable($table, $columns, $options = null)
    {
        parent::createTable($table, $columns, $options);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function addColumn($table, $column, $type)
    {
        parent::addColumn($table, $column, $type);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function dropColumn($table, $column)
    {
        parent::dropColumn($table, $column);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function alterColumn($table, $column, $type)
    {
        parent::alterColumn($table, $column, $type);
        $this->refreshTableSchema($table);
    }

    /**
     * Добавление новой системной настройки
     * @param string $table название таблицы
     * @param array $columns массив данных для вставки
     */
    public function insertSetting($table, $columns) {
        $this->insert($table, $columns);
        Yii::$app->cache->delete('#' . Yii::$app->db->schema->getRawTableName($table) . '#');
    }

    /**
     * Добавление новой системной настройки
     * @param string $table название таблицы
     * @param string|array $condition условия по которым надо удалить настройку
     * @param array $params параметры для условия
     */
    public function deleteSetting($table, $condition = '', $params = []) {
        $this->delete($table, $condition, $params);
        Yii::$app->cache->delete('#' . Yii::$app->db->schema->getRawTableName($table) . '#');
    }
}