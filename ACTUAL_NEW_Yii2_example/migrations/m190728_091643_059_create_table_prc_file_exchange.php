<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_059_create_table_prc_file_exchange extends Migration
{
    public function up()
    {
        $this->createTable('{{%file_exchange}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'name' => $this->string(),
            'entity_id' => $this->integer()->notNull(),
            'file_path' => $this->string()->notNull(),
            'original_file_name' => $this->string(),
            'file_format_id' => $this->integer()->notNull(),
            'settings' => $this->text()->notNull(),
            'rows_imported' => $this->integer()->notNull()->defaultValue('0'),
            'rows_failed' => $this->integer()->notNull()->defaultValue('0'),
            'had_errors' => $this->boolean()->notNull()->defaultValue(false),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'is_export' => $this->boolean()->notNull()->defaultValue(false),
            'encoding' => $this->string(),
            'task_status_id' => $this->integer()->notNull()->defaultValue('1'),
            'file_size' => $this->bigInteger(),
             'index' => $this->serial(),
        ], null);
        $this->addPk('{{%file_exchange}}', ['id']);
        $this->db->createCommand("CREATE INDEX ci_prc_file_exchange_status_id ON {{%file_exchange}} (status_id) WHERE status_id = 0;")->execute();
    }

    public function down()
    {
        $this->dropTable('{{%file_exchange}}');
    }
}
