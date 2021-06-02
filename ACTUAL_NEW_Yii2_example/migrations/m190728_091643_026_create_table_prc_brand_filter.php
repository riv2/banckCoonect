<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_026_create_table_prc_brand_filter extends Migration
{
    public function up()
    {
        $this->createTable('{{%brand_filter}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
        ], null);

        $this->addPk('{{%brand_filter}}', ['id']);
        $this->db->createCommand("CREATE INDEX prc_brand_filter_status_id ON {{%brand_filter}} (status_id) WHERE status_id = 0;")->execute();
    }

    public function down()
    {
        $this->dropTable('{{%brand_filter}}');
    }
}
