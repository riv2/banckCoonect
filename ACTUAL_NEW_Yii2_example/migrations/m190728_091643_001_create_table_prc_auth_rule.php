<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_001_create_table_prc_auth_rule extends Migration
{
    public function up()
    {
        $this->createTable('{{%auth_rule}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'data' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], null);

        $this->createIndex('prc_auth_rule_name_idx', '{{%auth_rule}}', 'name', true);
    }

    public function down()
    {
        $this->dropTable('{{%auth_rule}}');
    }
}
