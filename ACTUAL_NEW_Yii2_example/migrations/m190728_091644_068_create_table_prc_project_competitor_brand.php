<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_068_create_table_prc_project_competitor_brand extends Migration
{
    public function up()
    {
        $this->createTable('{{%project_competitor_brand}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'project_id' => $this->uuid()->notNull(),
            'competitor_id' => $this->uuid()->notNull(),
            'brand_id' => $this->uuid()->notNull(),
            'project_competitor_id' => $this->uuid()->notNull(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'name' => $this->string(),
             'index' => $this->serial(),
        ], null);
        $this->addPk('{{%project_competitor_brand}}', ['id']);
        //$this->addPk('{{%project_competitor_brand}}', ['project_id', 'id']);
        $this->createIndex('ix_prc_project_competitor_brand_project_id', '{{%project_competitor_brand}}', 'project_id');
    }

    public function down()
    {
        $this->dropTable('{{%project_competitor_brand}}');
    }
}
