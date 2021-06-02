<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_035_create_table_prc_error_type extends Migration
{
    public function up()
    {
        $this->createTable('{{%error_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
        ], null);
    }

    public function down()
    {
        $this->dropTable('{{%error_type}}');
    }
}
