<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_019_create_table_vw_prc_log_calc extends Migration
{
    public function up()
    {
                $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%vw_prc_log_calc}}', [
            'id' => $this->char(),
            'created_at' => $this->timestamp(),
            'source_id' => $this->integer(),
            'region_id' => $this->integer(),
            'item_id' => $this->char(),
            'item_name' => $this->string(),
            'item_brand_name' => $this->string(),
            'item_ym_index' => $this->string(),
            'item_ym_url' => $this->string(),
            'competitor_id' => $this->char(),
            'competitor_shop_name' => $this->string(),
            'project_id' => $this->char(),
            'project_item_id' => $this->char(),
            'project_execution_id' => $this->char(),
            'price_calculated_id' => $this->char(),
            'out_of_stock' => $this->integer(),
            'price_refined' => $this->double(),
            'price_calculated' => $this->double(),
            'price_supply' => $this->double(),
            'price_recommended_retail' => $this->double(),
            'price_default' => $this->double(),
            'extracted_at' => $this->timestamp(),
            'price_refined_id' => $this->char(),
            'is_key_competitor' => $this->integer(),
            'rrp_regulations' => $this->integer(),
            'margin' => $this->double(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%vw_prc_log_calc}}');
    }
}
