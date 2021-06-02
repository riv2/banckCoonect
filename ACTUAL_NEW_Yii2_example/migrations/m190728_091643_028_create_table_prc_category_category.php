<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_028_create_table_prc_category_category extends Migration
{
    public function up()
    {
        $this->createTable('{{%category_category}}', [
            'id' => $this->uuid(),
            'parent_id' => $this->uuid()->notNull(),
            'child_id' => $this->uuid()->notNull(),
             'index' => $this->serial(),
        ], null);

        $this->addPk('{{%category_category}}', ['parent_id', 'child_id']);
        $this->createIndex('ix_prc_category_category_parent_id', '{{%category_category}}', 'parent_id');
        $this->createIndex('ix_prc_category_category_child_id', '{{%category_category}}', 'child_id');
    }

    public function down()
    {
        $this->dropTable('{{%category_category}}');
    }
}
