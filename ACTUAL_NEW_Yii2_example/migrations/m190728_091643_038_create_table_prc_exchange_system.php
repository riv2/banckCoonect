<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_038_create_table_prc_exchange_system extends Migration
{
    public function up()
    {
        $this->createTable('{{%exchange_system}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'class_name' => $this->string()->notNull(),
            'data' => $this->text(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
             'index' => $this->serial(),
        ], null);

        $this->addPk('{{%exchange_system}}', ['id']);
    }

    public function down()
    {
        $this->dropTable('{{%exchange_system}}');
    }
}
