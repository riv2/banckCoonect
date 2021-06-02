<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_031_create_table_prc_competitor extends Migration
{
    public function up()
    {
        $this->createTable('{{%competitor}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
             'index' => $this->serial(),
        ], null);

        $this->addPk('{{%competitor}}', ['id']);
        $this->db->createCommand("CREATE INDEX ci_prc_competitor_name ON {{%competitor}} USING gin (name gin_trgm_ops);")->execute();
    }

    public function down()
    {
        $this->dropTable('{{%competitor}}');
    }
}
