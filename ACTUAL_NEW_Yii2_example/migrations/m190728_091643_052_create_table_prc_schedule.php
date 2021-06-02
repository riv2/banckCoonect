<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_052_create_table_prc_schedule extends Migration
{
    public function up()
    {
        $this->createTable('{{%schedule}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'requester_entity_id' => $this->integer()->notNull(),
            'requester_id' => $this->uuid()->notNull(),
            'function' => $this->string(),
            'args' => $this->text(),
            'time' => $this->time()->notNull(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'day' => $this->integer()->notNull(),
            'description' => $this->text(),
            'duration' => $this->time(),
             'index' => $this->serial(),
        ], null);
        $this->addPk('{{%schedule}}', ['id']);
        $this->db->createCommand("CREATE INDEX ci_prc_schedule_day_time ON {{%schedule}} (day, time, status_id) WHERE status_id = 0;")->execute();
        $this->db->createCommand("CREATE INDEX ci_prc_schedule_requester_entity_id ON {{%schedule}} (requester_entity_id);")->execute();
    }

    public function down()
    {
        $this->dropTable('{{%schedule}}');
    }
}
