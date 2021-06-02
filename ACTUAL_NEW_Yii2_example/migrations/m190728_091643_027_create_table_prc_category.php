<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_027_create_table_prc_category extends Migration
{
    public function up()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->uuid()->notNull(),
            'name' => $this->string()->notNull(1024),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'is_top' => $this->boolean()->notNull()->defaultValue(false),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
             'index' => $this->serial(),
        ], null);

        $this->addPk('{{%category}}', ['id']);
        $this->db->createCommand("CREATE INDEX ci_prc_category_is_top ON prc_category (is_top ASC) WHERE status_id = 0;")->execute();
     }

    public function down()
    {
        $this->dropTable('{{%category}}');
    }
}
