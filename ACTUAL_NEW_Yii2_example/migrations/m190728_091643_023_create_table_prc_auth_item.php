<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_023_create_table_prc_auth_item extends Migration
{
    public function up()
    {
        $this->createTable('{{%auth_item}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'type' => $this->integer()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(),
            'data' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], null);

        $this->createIndex('prc_auth_item_name_idx', '{{%auth_item}}', 'name', true);
        $this->createIndex('prc_auth_item_type_idx', '{{%auth_item}}', 'type');
        $this->createIndex('prc_auth_item_rule_name_idx', '{{%auth_item}}', 'rule_name');

    }

    public function down()
    {
        $this->dropTable('{{%auth_item}}');
    }
}
