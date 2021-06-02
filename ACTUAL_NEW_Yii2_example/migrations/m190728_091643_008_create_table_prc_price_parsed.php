<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_008_create_table_prc_price_parsed extends Migration
{
    public function up()
    {
        $this->createTable('{{%price_parsed}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'price' => $this->string(),
            'extracted_at' => $this->timestamp()->notNull(),
            'source_id' => $this->integer()->notNull(),
            'region_id' => $this->integer(),
            'item_id' => $this->uuid(),
            'competitor_id' => $this->uuid(),
            'competitor_shop_name' => $this->string(),
            'competitor_shop_index' => $this->string(),
            'competitor_shop_domain' => $this->string(),
            'competitor_item_name' => $this->string(1024),
            'competitor_item_sku' => $this->string(),
            'url' => $this->string(1024),
            'out_of_stock' => $this->boolean()->notNull()->defaultValue(false),
            'stock_reverse' => $this->boolean()->notNull()->defaultValue(false),
            'delivery' => $this->string(),
            'parsing_project_id' => $this->uuid()->notNull()->defaultValue('aaa00001-36e5-4b35-bd74-cb971a8d9335'),
            'parsing_id' => $this->uuid()->notNull()->defaultValue('aaa00002-36e5-4b35-bd74-cb971a8d9335'),
            'price_parsed_status_id' => $this->integer()->defaultValue('0'),
            'error_message' => $this->text(),
            'screenshot' => $this->string(512),
             'index' => $this->serial(),
            'robot_id' => $this->string(),
            'competitor_item_count' => $this->integer(),
            'regions' => $this->json(),
            'competitor_item_url' => $this->text(),
            'original_url' => $this->text(),
            'delivery_days' => $this->integer(),
            'competitor_item_rubric1' => $this->string(),
            'competitor_item_rubric2' => $this->string(),
            'competitor_item_seller'    => $this->string(64)->notNull()->defaultValue('default'),
            'competitor_item_brand' => $this->string(),
            'thread' => $this->smallInteger()->defaultValue('0'),
            'brand_id' => $this->uuid(),
        ], null);

        $this->db->createCommand("CREATE INDEX ci_prc_price_parsed_extracted_at ON {{%price_parsed}} (extracted_at ASC);")->execute();
        $this->db->createCommand("CREATE INDEX ci_prc_price_parsed_price_parsed_status_id ON {{%price_parsed}} (price_parsed_status_id);")->execute();
        $this->db->createCommand("CREATE INDEX ci_prc_price_parsed_price_parsed_1 ON {{%price_parsed}} (parsing_project_id, parsing_id);")->execute();
        $this->db->createCommand("CREATE INDEX ci_prc_price_parsed_price_parsed_stock ON {{%price_parsed}} (out_of_stock);")->execute();
        $this->db->createCommand("CREATE INDEX ci_prc_price_parsed_price_parsed_thread ON {{%price_parsed}} (thread);")->execute();
        $this->db->createCommand("create index ci_prc_price_parsed_refine_index on prc_price_parsed (thread asc, extracted_at desc) WHERE price_parsed_status_id = 0;")->execute();


        $this->addPk('{{%price_parsed}}', ['parsing_project_id','id']);
    }

    public function down()
    {
        $this->dropTable('{{%price_parsed}}');
    }
}
