<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_018_create_table_vw_prc_item extends Migration
{
    public function up()
    {
                $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%vw_prc_item}}', [
            'pkid' => $this->char(),
            'pricing_must_be' => $this->string(),
            'pricing_dont_be' => $this->string(),
            'pricing_keyword' => $this->string(),
            'name' => $this->string(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'wtis_index' => $this->bigInteger(),
            'site_index' => $this->bigInteger(),
            'sku' => $this->bigInteger(),
            'is_expendable' => $this->integer(),
            'is_liquid' => $this->integer(),
            'brand_id' => $this->char(),
            'price_supply' => $this->double(),
            'price_recommended_retail' => $this->double(),
            'price_default' => $this->double(),
            'vendor_type_text' => $this->string(),
            'ym_index' => $this->bigInteger(),
            'ym_url' => $this->string(),
            'status_id' => $this->integer(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%vw_prc_item}}');
    }
}
