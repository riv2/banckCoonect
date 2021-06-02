<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_066_create_table_prc_project extends Migration
{
    public function up()
    {
        $this->createTable('{{%project}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'region_id' => $this->integer(),
            'min_margin' => $this->double()->notNull()->defaultValue('3'),
            'competition_mode_id' => $this->integer()->notNull(),
            'price_former_type_id' => $this->uuid(),
            'price_export_mode_id' => $this->integer()->notNull(),
            'is_auto_export' => $this->boolean()->notNull()->defaultValue(false),
            'price_relevance_time_span' => $this->bigInteger()->notNull()->defaultValue('86400'),
            'price_range_k1' => $this->double()->notNull()->defaultValue('0.7'),
            'price_range_k2' => $this->double()->notNull()->defaultValue('1.5'),
            'price_range_threshold' => $this->double()->notNull()->defaultValue('800'),
            'price_range_k3' => $this->double()->notNull()->defaultValue('0.9'),
            'price_range_k4' => $this->double()->notNull()->defaultValue('1.3'),
            'data_life_time_span' => $this->bigInteger()->notNull()->defaultValue('2592000'),
            'is_logging' => $this->boolean()->notNull()->defaultValue(false),
            'scheduled_daily_time' => $this->time(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'project_execution_status_id' => $this->integer()->notNull()->defaultValue('1'),
            'scheduled_weekdays' => $this->string(),
            'supply_price_threshold' => $this->double()->defaultValue('300'),
            'project_theme_id' => $this->uuid(),
             'index' => $this->serial(),
            'use_vi' => $this->boolean()->notNull()->defaultValue(false),
            'last_export_at'=> $this->timestamp(),
            'last_export_count'=> $this->bigInteger()->defaultValue(0),
        ], null);

        $this->addPk('{{%project}}', ['id']);
        $this->db->createCommand("CREATE INDEX ci_prc_project_name ON prc_project (name ASC);")->execute();
    }

    public function down()
    {
        $this->dropTable('{{%project}}');
    }
}
