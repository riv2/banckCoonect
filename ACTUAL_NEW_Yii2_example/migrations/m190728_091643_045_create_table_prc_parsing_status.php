<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_045_create_table_prc_parsing_status extends Migration
{
    public function up()
    {
        $this->createTable('{{%parsing_status}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
        ], null);
    }

    public function down()
    {
        $this->dropTable('{{%parsing_status}}');
    }
}
