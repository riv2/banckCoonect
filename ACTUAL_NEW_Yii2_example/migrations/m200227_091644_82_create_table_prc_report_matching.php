<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200227_091644_82_create_table_prc_report_matching extends Migration
{
    public function up()
    {
        $this->createTable('{{%report_matching}}', [
            'id' => $this->uuidpk()->notNull(),

            'created_at' => $this->timestamp()->notNull(),
            'parsed_from' => $this->timestamp(),
            'parsed_to' => $this->timestamp(),

            'parsing_project_id'        => $this->uuid(),
            'parsing_id'                => $this->uuid(),
            'parsing_project_name'      => $this->string(),
            'parsing_name'              => $this->string(),
            'competitor_id'             => $this->uuid(),
            'competitor_name'           => $this->string(),

            'parsed_total'              => $this->bigInteger()->notNull()->defaultValue(0),
            'parsed_in_stock'           => $this->bigInteger()->notNull()->defaultValue(0),
            'filtered_out_stock'        => $this->bigInteger()->notNull()->defaultValue(0),
            'filtered_by_api'           => $this->bigInteger()->notNull()->defaultValue(0),
            'filtered_existing'         => $this->bigInteger()->notNull()->defaultValue(0),
            'filtered_total'            => $this->bigInteger()->notNull()->defaultValue(0),
            'matched_auto'              => $this->bigInteger()->notNull()->defaultValue(0),
            'to_manual_matching'        => $this->bigInteger()->notNull()->defaultValue(0),

        ], null);

        $this->addPk('{{%report_matching}}', ['parsing_project_id', 'id']);
        $this->addIndex('{{%report_matching}}', ['parsing_id']);
        $this->addIndex('{{%report_matching}}', ['parsing_project_id']);

        $this->insert('{{%entity}}', [
            'id' => 82,
            'name' => 'Отчет по сопоставлению',
            'alias' => 'ReportMatching',
            'class_name' => 'app\models\pool\ReportMatching',
            'action' => 'report-matching',
            'entity_type' => 'pool',
            'parent_id' => null,
            'is_logging' => false,
            'is_enabled' => true
        ]);

        $this->insert('{{%task_type}}', [
            'id' => 16,
            'name' => 'Отчет по сопоставлению',
            'icon' => 'fa fa-refresh',
        ]);
    }

    public function down()
    {
        $this->delete('{{%entity}}', [
            'id' => 82,
        ]);
        $this->delete('{{%task_type}}', [
            'id' => 16,
        ]);
        $this->dropTable('{{%report_matching}}');
    }
}
