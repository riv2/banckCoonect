<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_074_create_table_prc_project_source extends Migration
{
    public function up()
    {
        $this->createTable('{{%project_source}}', [
            'id' => $this->uuidpk()->notNull(),
            'project_id' => $this->uuid()->notNull(),
            'source_id' => $this->integer()->notNull(),
        ], null);

        $this->addPk('{{%project_source}}', ['id']);
        //$this->addPk('{{%project_source}}', ['project_id', 'id']);
        $this->createIndex('ux_prc_project_source_project_id_item_id', '{{%project_source}}', ['project_id','source_id'], true);
        $this->createIndex('ix_prc_project_source_project_id', '{{%project_source}}', 'project_id');
    }

    public function down()
    {
        $this->dropTable('{{%project_source}}');
    }
}
