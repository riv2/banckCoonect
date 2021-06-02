<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_004_create_table_prc_log_price_calculation extends Migration
{
    public function up()
    {
        $this->createTable('{{%log_price_calculation}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'source_id' => $this->integer()->notNull(),
            'region_id' => $this->integer(),
            'item_id' => $this->uuid(),
            'item_name' => $this->string(1024),
            'item_brand_name' => $this->string(),
            'item_ym_index' => $this->string(),
            'item_ym_url' => $this->string(1024),
            'competitor_id' => $this->uuid(),
            'competitor_shop_name' => $this->string(),
            'competitor_shop_index' => $this->string(),
            'competitor_shop_domain' => $this->string(),
            'competitor_item_name' => $this->string(1024),
            'competitor_item_sku' => $this->string(),
            'url' => $this->string(1024),
            'project_id' => $this->uuid()->notNull(),
            'project_item_id' => $this->uuid()->notNull(),
            'project_execution_id' => $this->uuid()->notNull(),
            'price_calculated_id' => $this->uuid(),
            'price_refined' => $this->double(),
            'price_calculated' => $this->double(),
            'price_supply' => $this->double(),
            'price_recommended_retail' => $this->double(),
            'price_default' => $this->double(),
            'extracted_at' => $this->timestamp()->notNull(),
            'price_refined_id' => $this->uuid()->notNull(),
            'is_key_competitor' => $this->boolean(),
            'rrp_regulations' => $this->boolean(),
            'margin' => $this->double(),
            'out_of_stock' => $this->boolean()->defaultValue(false),
             'index' => $this->serial(),
            'screenshot' => $this->string(),
            'brand_id' => $this->uuid(),
            'price_weighted' => $this->double(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'delivery_days' => $this->integer(),
        ], null);

        $this->addPk('{{%log_price_calculation}}', ['project_execution_id','id']);


        $this->db->createCommand("CREATE INDEX ci_prc_log_price_calculation_created_at ON {{%log_price_calculation}} (project_execution_id, created_at);")
            ->execute();

        $this->db->createCommand("CREATE INDEX ci_rrp_violations1  ON prc_log_price_calculation (price_refined)");
        $this->db->createCommand("CREATE INDEX ci_rrp_violations2  ON prc_log_price_calculation (project_execution_id);");
        $this->db->createCommand("CREATE INDEX ci_rrp_violations3  ON prc_log_price_calculation (item_id);");
        $this->db->createCommand("CREATE INDEX ci_rrp_violations4  ON prc_log_price_calculation (item_brand_name ASC);");
        $this->db->createCommand("CREATE INDEX ci_rrp_violations5  ON prc_log_price_calculation (price_recommended_retail);");
        $this->db->createCommand("CREATE INDEX ci_rrp_violations6  ON prc_log_price_calculation (competitor_id);");
    }

    public function down()
    {
        $this->dropTable('{{%log_price_calculation}}');
    }
}
