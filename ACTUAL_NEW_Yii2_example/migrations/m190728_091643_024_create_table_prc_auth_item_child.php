<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_024_create_table_prc_auth_item_child extends Migration
{
    public function up()
    {
        $this->createTable('{{%auth_item_child}}', [
            'id' => $this->primaryKey(),
            'parent' => $this->string()->notNull(),
            'child' => $this->string()->notNull(),
        ], null);

        $this->createIndex('prc_auth_item_child_parent_child', '{{%auth_item_child}}', ['parent', 'child'], true);
    }

    public function down()
    {
        $this->dropTable('{{%auth_item_child}}');
    }
}
