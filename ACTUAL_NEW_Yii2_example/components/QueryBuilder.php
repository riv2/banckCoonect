<?php

namespace app\components;

/**
 * Класс построитель запросов
 *
 * @package app\components
 */
class QueryBuilder extends \yii\db\pgsql\QueryBuilder {

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->typeMap[Schema::TYPE_UUIDPK] = "uuid DEFAULT uuid_generate_v4()";
        $this->typeMap[Schema::TYPE_UUID] = "uuid";
    }

}