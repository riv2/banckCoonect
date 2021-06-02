<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_077_create_table_prc_price_calculated extends Migration
{
    public function up()
    {
        $this->createTable('{{%price_calculated}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'price' => $this->double()->notNull(),
            'item_id' => $this->uuid()->notNull(),
            'project_id' => $this->uuid()->notNull(),
            'project_execution_id' => $this->uuid()->notNull()->defaultValue('aaa00004-36e5-4b35-bd74-cb971a8d9335'),
             'index' => $this->serial(),
        ], null);
        $this->addPk('{{%price_calculated}}', ['project_execution_id', 'id']);
        $this->db->createCommand("CREATE INDEX ci_prc_price_calculated_created_at ON prc_price_calculated (created_at DESC);")->execute();
        $this->db->createCommand("CREATE UNIQUE INDEX ci_prc_price_calculated_project_execution_id_item_id ON prc_price_calculated (project_execution_id,item_id) INCLUDE (price);")->execute();
        $this->createIndex('ix_prc_price_calculated_project_execution_id', '{{%price_calculated}}', 'project_execution_id');
    }

    public function down()
    {
        $this->dropTable('{{%price_calculated}}');
    }
}
