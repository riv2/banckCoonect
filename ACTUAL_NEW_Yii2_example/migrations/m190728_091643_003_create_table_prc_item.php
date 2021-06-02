<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_003_create_table_prc_item extends Migration
{
    public function up()
    {
        $this->createTable('{{%item}}', [
            'id' => $this->uuid()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'wtis_index' => $this->bigInteger(),
            'site_index' => $this->bigInteger(),
            'sku' => $this->bigInteger(),
            'is_expendable' => $this->boolean()->notNull()->defaultValue(false),
            'is_liquid' => $this->boolean()->notNull()->defaultValue(false),
            'brand_id' => $this->uuid(),
            'price_supply' => $this->double(),
            'price_recommended_retail' => $this->double(),
            'price_default' => $this->double(),
            'vendor_type_text' => $this->string(),
            'pricing_keyword' => $this->string(1024),
            'pricing_must_be' => $this->string(1024),
            'pricing_dont_be' => $this->string(1024),
            'ym_index' => $this->bigInteger(),
            'ym_url' => $this->string(1024),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
             'index' => $this->serial(),
            'price_weighted' => $this->double(),
            'is_duplicate' => $this->boolean()->notNull()->defaultValue(false),
            'main_id' => $this->uuid(),
        ], null);
        $this->addPk('{{%item}}', ['id']);

        $this->db->createCommand("CREATE INDEX ci_prc_item_name ON {{%item}} USING gin (name gin_trgm_ops) WHERE status_id = 0;")->execute();
        $this->db->createCommand("CREATE INDEX ci_prc_item_brand ON {{%item}} (brand_id) WHERE status_id = 0;")->execute();

    }

    public function down()
    {
        $this->dropTable('{{%item}}');
    }
}
