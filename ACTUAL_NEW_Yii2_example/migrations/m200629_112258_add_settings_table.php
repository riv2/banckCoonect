<?php

use app\components\Migration;
use app\models\enum\Status;

class m200629_112258_add_settings_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%setting}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string(1024)->notNull(),
            'data' => $this->text(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'status_id' => $this->integer(1)->defaultValue(Status::STATUS_ACTIVE),
        ]);
        $this->addFK('{{%setting}}', 'status_id', '{{%status}}', 'id');
        $this->addFK('{{%setting}}', 'created_user_id', '{{%user}}', 'id');
        $this->addFK('{{%setting}}', 'updated_user_id', '{{%user}}', 'id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%setting}}');
    }
}
