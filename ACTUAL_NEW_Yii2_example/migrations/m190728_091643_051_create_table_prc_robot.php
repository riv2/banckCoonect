<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_051_create_table_prc_robot extends Migration
{
    public function up()
    {
        $this->createTable('{{%robot}}', [
            'id' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'proxies' => $this->text(),
            'user_agents' => $this->text(),
            'max_projects' => $this->integer()->notNull()->defaultValue('5'),
            'max_connections' => $this->integer()->notNull()->defaultValue('5'),
            'rate_limit' => $this->integer()->notNull()->defaultValue('2000'),
            'retry_timeout' => $this->integer()->notNull()->defaultValue('2000'),
            'timeout' => $this->integer()->notNull()->defaultValue('5000'),
            'retries' => $this->integer()->notNull()->defaultValue('1'),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'anticaptcha_key' => $this->string(),
            'color' => $this->string(),
        ], null);
        $this->addPk('{{%robot}}', ['id']);
    }

    public function down()
    {
        $this->dropTable('{{%robot}}');
    }
}
