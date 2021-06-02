<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_013_create_table_prc_task_type extends Migration
{
    public function up()
    {
        $this->createTable('{{%task_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'icon' => $this->string(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
        ], null);
    }

    public function down()
    {
        $this->dropTable('{{%task_type}}');
    }
}
