<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_005_create_table_prc_log_project_execution extends Migration
{
    public function up()
    {
        $this->createTable('{{%log_project_execution}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'item_id' => $this->uuid()->notNull(),
            'item_name' => $this->string(1024),
            'item_brand_name' => $this->string(),
            'region_id' => $this->integer(),
            'project_id' => $this->uuid()->notNull(),
            'project_item_id' => $this->uuid()->notNull(),
            'project_execution_id' => $this->uuid()->notNull(),
            'price_calculated_id' => $this->uuid()->notNull(),
            'price_calculated' => $this->double(),
            'price_supply' => $this->double(),
            'price_recommended_retail' => $this->double(),
            'price_default' => $this->double(),
            'is_export' => $this->boolean()->notNull()->defaultValue(false),
            'rrp_regulations' => $this->boolean(),
            'margin' => $this->double(),
            'item_ym_url' => $this->string(1024),
            'item_ym_index' => $this->string(),
             'index' => $this->serial(),
            'brand_id' => $this->uuid(),
            'price_weighted' => $this->double(),
        ], null);

        $this->addPk('{{%log_project_execution}}', ['project_execution_id','id']);
        $this->db->createCommand("CREATE INDEX ci_prc_log_project_execution_created_at ON {{%log_project_execution}} (project_execution_id, created_at);")
            ->execute();
    }

    public function down()
    {
        $this->dropTable('{{%log_project_execution}}');
    }
}
