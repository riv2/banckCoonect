<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_011_create_table_prc_status extends Migration
{
    public function up()
    {
        $this->createTable('{{%status}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ], null);
    }

    public function down()
    {
        $this->dropTable('{{%status}}');
    }
}
