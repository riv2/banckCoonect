<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_044_create_table_prc_masks extends Migration
{
    public function up()
    {
        $this->createTable('{{%masks}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'domain' => $this->string()->notNull(),
            'test_urls' => $this->text(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'masks' => 'json',
        ], null);
        $this->addPk('{{%masks}}', ['id']);
    }

    public function down()
    {
        $this->dropTable('{{%masks}}');
    }
}
