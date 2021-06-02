<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_057_create_table_prc_competitor_item extends Migration
{
    public function up()
    {
        $this->createTable('{{%competitor_item}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string(1024)->notNull(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'source_id' => $this->integer()->notNull(),
            'item_id' => $this->uuid()->notNull(),
            'competitor_id' => $this->uuid()->notNull(),
            'sku' => $this->string(),
            'url' => $this->text(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
             'index' => $this->serial(),
            'competitor_item_name' => $this->string(1024),
            'competitor_item_seller'    => $this->string(64)->notNull()->defaultValue('default'),
            'price' => $this->double(),
            'price_updated_at' => $this->timestamp(),
            'error_last_date' => $this->timestamp(),
            'errors_count' => $this->integer()->notNull()->defaultValue('0'),
        ], null);
        //$this->addPk('{{%competitor_item}}', ['id']);
        $this->addPk('{{%competitor_item}}', ['competitor_id', 'id']);

        $this->db->createCommand("CREATE INDEX ci_prc_competitor_item_url  ON prc_competitor_item USING gin (url gin_trgm_ops);")->execute();
        $this->createIndex('ix_prc_competitor_item_source_id_competitor_id_name', '{{%competitor_item}}', ['competitor_id', 'name', 'source_id']);
        $this->createIndex('ix_prc_competitor_item_source_id_competitor_id_sku', '{{%competitor_item}}', ['competitor_id', 'sku', 'source_id' ]);
        $this->createIndex('ix_prc_competitor_item_competitor_id', '{{%competitor_item}}', ['competitor_id']);
        $this->createIndex('ix_prc_competitor_item_item_id', '{{%competitor_item}}', ['item_id']);

        $this->createIndex('ix_prc_competitor_competitor_id_item_id_competitor_item_seller', '{{%competitor_item}}', ['competitor_id', 'item_id', 'competitor_item_seller' ], true);
    }

    public function down()
    {
        $this->dropTable('{{%competitor_item}}');
    }
}
