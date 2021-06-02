<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_061_create_table_prc_horadric_cube extends Migration
{
    public function up()
    {
        $this->createTable('{{%horadric_cube}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'item_id' => $this->uuid(),
            'competitor_id' => $this->uuid(),
            'parsing_id' => $this->uuid(),
            'parsing_project_id' => $this->uuid()->notNull(),
            'horadric_cube_status_id' => $this->integer()->notNull()->defaultValue('1'),
            'competitor_item_id' => $this->uuid(),
            'competitor_shop_name' => $this->string(),
            'competitor_item_name' => $this->string(1024),
            'competitor_item_price' => $this->double(),
            'competitor_item_url' => $this->string(1024),
            'competitor_item_sku' => $this->string(),
            'competitor_item_seller'    => $this->string(64)->notNull()->defaultValue('default'),
            'vi_item_name' => $this->string(1024),
            'vi_item_price' => $this->double(),
            'vi_item_url' => $this->string(1024),
            'vi_item_sku' => $this->string(),
            'vi_item_id' => $this->string(),
            'vi_item_brand_name' => $this->string(),
            'percent' => $this->double(),
            'filter_reason' => $this->text(),
            'brand_id' => $this->uuid(),
            'vi_item_in_msk' => $this->boolean()->notNull()->defaultValue('1'),
            'vi_item_matrix' => $this->text(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
        ], null);
        //$this->addPk('{{%horadric_cube}}', ['id']);
        $this->addPk('{{%horadric_cube}}', ['competitor_id', 'id']);
        $this->db->createCommand("CREATE INDEX ci_prc_horadric_cube_1  ON prc_horadric_cube USING gin (vi_item_id, status_id, competitor_item_url  gin_trgm_ops) WHERE status_id = 0;")->execute();
        $this->db->createCommand("CREATE INDEX ci_prc_horadric_cube_2  ON prc_horadric_cube  (horadric_cube_status_id, updated_at ASC) INCLUDE (competitor_id, item_id);")->execute();
        $this->db->createCommand("CREATE INDEX ci_prc_horadric_cube_3  ON prc_horadric_cube  (horadric_cube_status_id, competitor_id, item_id, percent ASC) WHERE horadric_cube_status_id = 1;")->execute();
        $this->createIndex('ix_prc_horadric_cube_vi_item_sku', '{{%horadric_cube}}', 'vi_item_sku');
        $this->createIndex('ix_prc_horadric_cube_horadric_cube_status_id', '{{%horadric_cube}}', 'horadric_cube_status_id');
        $this->db->createCommand("create unique index ci_prc_horadric_cube_url_uindex on prc_horadric_cube (competitor_id, competitor_item_url, vi_item_id, competitor_item_seller);")->execute();
    }

    public function down()
    {
        $this->dropTable('{{%horadric_cube}}');
    }
}
