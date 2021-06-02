<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_025_create_table_prc_brand extends Migration
{
    public function up()
    {
        $this->createTable('{{%brand}}', [
            'id' => $this->uuid()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
             'index' => $this->serial(),
        ], null);

        $this->addPk('{{%brand}}', ['id']);
        $this->db->createCommand("CREATE INDEX ci_prc_brand_status_id ON {{%brand}} (status_id) WHERE status_id = 0;")->execute();
    }

    public function down()
    {
        $this->dropTable('{{%brand}}');
    }
}
