<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_055_create_table_prc_task extends Migration
{
    public function up()
    {
        $this->createTable('{{%task}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'name' => $this->string(),
            'task_type_id' => $this->integer()->notNull()->defaultValue('1'),
            'task_status_id' => $this->integer()->notNull()->defaultValue('1'),
            'requester_entity_id' => $this->integer(),
            'requester_id' => $this->uuid(),
            'priority' => $this->integer()->notNull()->defaultValue('0'),
            'task_function' => $this->string(),
            'task_params' => $this->text(),
            'total' => $this->bigInteger()->notNull()->defaultValue('0'),
            'progress' => $this->bigInteger()->notNull()->defaultValue('0'),
            'errors' => $this->bigInteger()->notNull()->defaultValue('0'),
            'had_errors' => $this->boolean()->notNull()->defaultValue(false),
            'started_at' => $this->timestamp(),
            'finished_at' => $this->timestamp(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'is_external' => $this->boolean()->defaultValue(false),
             'index' => $this->serial(),
        ], null);
        $this->addPk('{{%task}}', ['id']);
    }

    public function down()
    {
        $this->dropTable('{{%task}}');
    }
}
