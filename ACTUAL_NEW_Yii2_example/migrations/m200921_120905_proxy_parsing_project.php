<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200921_120905_proxy_parsing_project extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%proxy_parsing_project}}', [
            'id' => $this->uuidpk(),
            'proxy_id' => $this->string()->notNull(),
            'parsing_project_id' => $this->uuid()->notNull(),
            'parsing_id' => $this->uuid()->notNull(),
            'created_at' => $this->timestamp(),
        ]);
        $this->addFK('{{%proxy_parsing_project}}', 'proxy_id', '{{%proxy}}', 'id');
        $this->addFK('{{%proxy_parsing_project}}', 'parsing_project_id',
            '{{%parsing_project}}', 'id');
        $this->addFK('{{%proxy_parsing_project}}', 'parsing_id',
            '{{%parsing}}', 'id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%proxy_parsing_project}}');
    }
}
