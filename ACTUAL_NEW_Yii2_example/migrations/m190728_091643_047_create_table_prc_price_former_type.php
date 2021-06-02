<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_047_create_table_prc_price_former_type extends Migration
{
    public function up()
    {
        $this->createTable('{{%price_former_type}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
             'index' => $this->serial(),
        ], null);
        $this->addPk('{{%price_former_type}}', ['id']);
    }

    public function down()
    {
        $this->dropTable('{{%price_former_type}}');
    }
}
