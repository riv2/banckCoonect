<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_009_create_table_prc_price_parsed_status extends Migration
{
    public function up()
    {
        $this->createTable('{{%price_parsed_status}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ], null);
    }

    public function down()
    {
        $this->dropTable('{{%price_parsed_status}}');
    }
}
