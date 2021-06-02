<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200817_100844_project_competitor_items_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%project_competitor_item}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'project_id' => $this->uuid()->notNull(),
            'competitor_id' => $this->uuid()->notNull(),
            'item_id' => $this->uuid()->notNull(),
            'project_competitor_id' => $this->uuid()->notNull(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'name' => $this->string(),
            'index' => $this->serial(),
        ], null);
        $this->createIndex('ix_prc_project_competitor_item_project_id', '{{%project_competitor_item}}', 'project_id');
        $this->addFK('{{%project_competitor_item}}', 'created_user_id', '{{%user}}', 'id');
        $this->addFK('{{%project_competitor_item}}', 'updated_user_id', '{{%user}}', 'id');
        $this->addFK('{{%project_competitor_item}}', 'project_id', '{{%project}}', 'id');
        $this->addFK('{{%project_competitor_item}}', 'competitor_id', '{{%competitor}}', 'id');
        $this->addFK('{{%project_competitor_item}}', 'item_id', '{{%item}}', 'id');
        $this->addFK('{{%project_competitor_item}}', 'project_competitor_id', '{{%project_competitor}}', 'id');
        $this->addFK('{{%project_competitor_item}}', 'status_id', '{{%status}}', 'id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%project_competitor_item}}');
    }
}
