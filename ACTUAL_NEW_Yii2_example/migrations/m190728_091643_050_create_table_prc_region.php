<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_050_create_table_prc_region extends Migration
{
    public function up()
    {
        $this->createTable('{{%region}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'parent_id' => $this->integer(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'is_ours' => $this->boolean()->defaultValue(false),
        ], null);
    }

    public function down()
    {
        $this->dropTable('{{%region}}');
    }
}
