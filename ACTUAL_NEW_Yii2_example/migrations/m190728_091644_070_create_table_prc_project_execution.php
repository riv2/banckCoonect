<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_070_create_table_prc_project_execution extends Migration
{
    public function up()
    {
        $this->createTable('{{%project_execution}}', [
            'id' => $this->uuidpk()->notNull(),
            'number' => $this->serial(),
            'name' => $this->string(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'project_id' => $this->uuid()->notNull(),
            'project_execution_status_id' => $this->integer()->notNull()->defaultValue('1'),
            'started_at' => $this->timestamp(),
            'calculated_at' => $this->timestamp(),
            'exported_at' => $this->timestamp(),
            'project_snapshot' => $this->text(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'processed_count' => $this->bigInteger(),
            'exported_count' => $this->bigInteger(),
            'calculated_count' => $this->bigInteger(),
            'prepared_count' => $this->bigInteger(),
            'prepared_at' => $this->timestamp(),
        ], null);
        //$this->addPk('{{%project_execution}}', ['id']);
        $this->addPk('{{%project_execution}}', ['project_id', 'id']);
        $this->db->createCommand("CREATE INDEX ci_prc_project_execution_project_id ON prc_project_execution (project_id, number DESC);")->execute();
        $this->createIndex('ix_prc_project_execution_project_id', '{{%project_execution}}', 'project_id');
    }

    public function down()
    {
        $this->dropTable('{{%project_execution}}');
    }
}
