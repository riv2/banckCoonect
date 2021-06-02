<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_002_create_table_prc_entity extends Migration
{
    public function up()
    {
        $this->createTable('{{%entity}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'alias' => $this->string()->notNull(),
            'class_name' => $this->string()->notNull(),
            'action' => $this->string()->notNull(),
            'entity_type' => $this->string()->notNull(),
            'parent_id' => $this->integer(),
            'is_logging' => $this->boolean()->notNull()->defaultValue('0'),
            'is_enabled' => $this->boolean()->notNull()->defaultValue('1'),
        ], null);
    }

    public function down()
    {
        $this->dropTable('{{%entity}}');
    }
}
