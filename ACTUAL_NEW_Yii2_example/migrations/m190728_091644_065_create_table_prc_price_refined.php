<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_065_create_table_prc_price_refined extends Migration
{
    public function up()
    {
        $this->createTable('{{%price_refined}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'price' => $this->double(),
            'extracted_at' => $this->timestamp()->notNull(),
            'source_id' => $this->integer()->notNull(),
            'region_id' => $this->integer(),
            'item_id' => $this->uuid()->notNull(),
            'parsing_id' => $this->uuid()->notNull()->defaultValue('aaa00002-36e5-4b35-bd74-cb971a8d9335'),
            'price_parsed_id' => $this->uuid(),
            'parsing_project_id' => $this->uuid()->notNull()->defaultValue('aaa00001-36e5-4b35-bd74-cb971a8d9335'),
            'competitor_id' => $this->uuid()->notNull()->defaultValue('aaa00003-36e5-4b35-bd74-cb971a8d9335'),
            'competitor_shop_name' => $this->string(),
            'competitor_shop_index' => $this->string(),
            'competitor_item_seller'    => $this->string(64)->notNull()->defaultValue('default'),
            'competitor_item_sku'    => $this->string(64),
            'out_of_stock' => $this->boolean()->notNull()->defaultValue(false),
            'group_hash' => $this->string(),
            'index' => $this->serial(),
            'screenshot' => $this->string(),
            'url' => $this->string(1024),
            'robot_id' => $this->string(),
            'delivery_days' => $this->integer(),
        ], null);

       //$this->addPk('{{%price_refined}}', ['competitor_id', 'id']);
        $this->addPk('{{%price_refined}}', ['id']);
        $this->db->createCommand("create index ci_price_refined1 on prc_price_refined (source_id asc, item_id asc, region_id asc, competitor_id asc, extracted_at desc);")->execute();
        $this->db->createCommand("CREATE INDEX ci_price_refined2  ON prc_price_refined (id, out_of_stock asc, source_id asc, item_id ASC, region_id, extracted_at asc) include (price) WHERE out_of_stock = false;")->execute();
        $this->db->createCommand("create index ci_prc_price_refined_extracted_at on prc_price_refined (extracted_at DESC);")->execute();
    }

    public function down()
    {
        $this->dropTable('{{%price_refined}}');
    }
}
