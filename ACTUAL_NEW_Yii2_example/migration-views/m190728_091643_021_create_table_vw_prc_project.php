<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_021_create_table_vw_prc_project extends Migration
{
    public function up()
    {
                $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%vw_prc_project}}', [
            'id' => $this->char(),
            'name' => $this->string(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'region_id' => $this->integer(),
            'min_margin' => $this->double(),
            'competition_mode_id' => $this->integer(),
            'price_former_type_id' => $this->char(),
            'price_export_mode_id' => $this->integer(),
            'is_auto_export' => $this->integer(),
            'price_relevance_time_span' => $this->bigInteger(),
            'price_range_k1' => $this->double(),
            'price_range_k2' => $this->double(),
            'price_range_threshold' => $this->double(),
            'price_range_k3' => $this->double(),
            'price_range_k4' => $this->double(),
            'data_life_time_span' => $this->bigInteger(),
            'is_logging' => $this->integer(),
            'scheduled_daily_time' => $this->time(),
            'status_id' => $this->integer(),
            'project_execution_status_id' => $this->integer(),
            'scheduled_weekdays' => $this->string(),
            'supply_price_threshold' => $this->double(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%vw_prc_project}}');
    }
}
