<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_040_create_table_prc_file_processing extends Migration
{
    public function up()
    {
        $this->createTable('{{%file_processing}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'file_processing_settings_id' => $this->uuid(),
            'file_path' => $this->string(),
            'original_file_name' => $this->string(),
            'encoding' => $this->string(),
            'file_size' => $this->bigInteger()->notNull()->defaultValue('0'),
            'total' => $this->bigInteger()->notNull()->defaultValue('0'),
            'progress' => $this->bigInteger()->notNull()->defaultValue('0'),
            'errors' => $this->bigInteger()->notNull()->defaultValue('0'),
            'settings_json' => $this->text(),
            'error_message' => $this->text(),
            'task_id' => $this->uuid(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
        ], null);

        $this->addPk('{{%file_processing}}', ['id']);

    }

    public function down()
    {
        $this->dropTable('{{%file_processing}}');
    }
}
