<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_073_create_table_prc_project_region extends Migration
{
    public function up()
    {
        $this->createTable('{{%project_region}}', [
            'id' => $this->uuidpk()->notNull(),
            'project_id' => $this->uuid()->notNull(),
            'region_id' => $this->integer()->notNull(),
        ], null);
        $this->addPk('{{%project_region}}', ['id']);
        //$this->addPk('{{%project_region}}', ['project_id', 'id']);
        $this->createIndex('ux_prc_project_region_project_id_item_id', '{{%project_region}}', ['project_id','region_id'], true);
        $this->createIndex('ix_prc_project_region_project_id', '{{%project_region}}', 'project_id');
    }

    public function down()
    {
        $this->dropTable('{{%project_region}}');
    }
}
