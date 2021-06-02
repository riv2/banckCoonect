<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_071_create_table_prc_project_item extends Migration
{
    public function up()
    {
        $this->createTable('{{%project_item}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'item_id' => $this->uuid()->notNull(),
            'project_id' => $this->uuid()->notNull(),
            'select_price_logic_id' => $this->integer()->notNull()->defaultValue('101'),
            'min_margin' => $this->double(),
            'rrp_regulations' => $this->boolean()->notNull()->defaultValue(false),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'name' => $this->string(512),
        ], null);
        $this->addPk('{{%project_item}}', ['project_id','id']);
        $this->db->createCommand("CREATE INDEX ci_prc_project_item_1 ON prc_project_item  USING btree (project_id) INCLUDE (item_id, rrp_regulations);")->execute();
        $this->db->createCommand("CREATE UNIQUE INDEX ci_prc_project_item_2 ON prc_project_item  USING btree (project_id, item_id) INCLUDE (rrp_regulations);")->execute();
    }

    public function down()
    {
        $this->dropTable('{{%project_item}}');
    }
}
