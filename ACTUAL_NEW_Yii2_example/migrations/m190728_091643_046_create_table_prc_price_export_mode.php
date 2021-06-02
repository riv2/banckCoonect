<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_046_create_table_prc_price_export_mode extends Migration
{
    public function up()
    {
        $this->createTable('{{%price_export_mode}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
        ], null);
    }

    public function down()
    {
        $this->dropTable('{{%price_export_mode}}');
    }
}
