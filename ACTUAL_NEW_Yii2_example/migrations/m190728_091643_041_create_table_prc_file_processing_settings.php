<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_041_create_table_prc_file_processing_settings extends Migration
{
    public function up()
    {
        $this->createTable('{{%file_processing_settings}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'settings_json' => $this->text(),
            'class' => $this->string(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
        ], null);
        $this->addPk('{{%file_processing_settings}}', ['id']);
    }

    public function down()
    {
        $this->dropTable('{{%file_processing_settings}}');
    }
}
