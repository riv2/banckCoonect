<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_029_create_table_prc_category_item extends Migration
{
    public function up()
    {
        $this->createTable('{{%category_item}}', [
            'id' => $this->uuid(),
            'category_id' => $this->uuid()->notNull(),
            'item_id' => $this->uuid()->notNull(),
            'is_top' => $this->boolean(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
             'index' => $this->serial(),
        ], null);

        $this->addPk('{{%category_item}}', ['category_id', 'item_id']);
        $this->createIndex('ix_prc_category_item_category_id', '{{%category_item}}', 'category_id');
        $this->createIndex('ix_prc_category_item_item_id', '{{%category_item}}', 'item_id');
        $this->db->createCommand("CREATE INDEX ci_prc_category_item_is_top ON prc_category_item (is_top ASC) WHERE status_id = 0;")->execute();

    }

    public function down()
    {
        $this->dropTable('{{%category_item}}');
    }
}
