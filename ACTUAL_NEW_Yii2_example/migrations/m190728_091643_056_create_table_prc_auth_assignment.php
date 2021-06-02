<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_056_create_table_prc_auth_assignment extends Migration
{
    public function up()
    {
        $this->createTable('{{%auth_assignment}}', [
            'id' => $this->primaryKey(),
            'item_name' => $this->string()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
        ], null);

        $this->createIndex('prc_auth_assignment_parent_child', '{{%auth_assignment}}', ['item_name', 'user_id'], true);
    }

    public function down()
    {
        $this->dropTable('{{%auth_assignment}}');
    }
}
