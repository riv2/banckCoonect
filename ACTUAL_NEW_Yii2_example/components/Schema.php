<?php
/**
 * Created by IntelliJ IDEA.
 * User: aydin
 * Date: 06.02.2015
 * Time: 11:37
 */

namespace app\components;

/**
 * Схема для СУБД PostgreSQL
 *
 * @package app\components
 */
class Schema extends \yii\db\pgsql\Schema {

    /** Первичный ключ типа UUID */
    const TYPE_UUIDPK = 'uuidpk';
    /** Поле типа UUID */
    const TYPE_UUID = 'uuid';
    /** Поле типа SERIAL */
    const TYPE_SERIAL = 'serial';
    /** Поле типа SERIALPK */
    const TYPE_SERIAL_PK = 'serial PRIMARY KEY';
    /** Поле типа SERIALPK */
    const TYPE_ENUM_DATA_FORMAT = 'data_format';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->typeMap['uuid'] = self::TYPE_UUID;
        $this->typeMap['serial'] = self::TYPE_SERIAL;
        $this->typeMap['data_format'] = self::TYPE_ENUM_DATA_FORMAT;
        parent::init();
    }

    /**
     * @inheritdoc
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        return new QueryBuilder($this->db);
    }
}