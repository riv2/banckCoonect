<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m191226_091644_065_create_table_prc_report_kpi extends Migration
{
    public function up()
    {
        $this->createTable('{{%report_kpi}}', [
            'id' => $this->uuidpk()->notNull(),

            'created_at' => $this->timestamp()->notNull(),
            'from_date'  => $this->timestamp()->notNull(),
            'to_date'    => $this->timestamp()->notNull(),

            'project_id'                => $this->uuid(),
            'project_name'              => $this->string(),
            'competitor_id'             => $this->uuid(),
            'competitor_name'           => $this->string(),
            'project_execution_id'      => $this->uuid(),

            'total_competitor_sku'      => $this->bigInteger()->notNull()->defaultValue(0),
            'total_parsed'              => $this->bigInteger()->notNull()->defaultValue(0),
            'in_stock'                  => $this->bigInteger()->notNull()->defaultValue(0),
            'out_stock'                 => $this->bigInteger()->notNull()->defaultValue(0),
            'unparsed'                  => $this->bigInteger()->notNull()->defaultValue(0),
            'in_calculation'            => $this->bigInteger()->notNull()->defaultValue(0),
            'avg_price_life'            => $this->time(),
            'percent_missed'            => $this->float()->notNull()->defaultValue(0),
            'regions'                   => $this->json(),

        ], null);

        $this->addPk('{{%report_kpi}}', ['competitor_id', 'id']);
        $this->addIndex('{{%report_kpi}}', ['project_id']);
        $this->addIndex('{{%report_kpi}}', ['project_execution_id']);

        $this->insert('{{%entity}}', [
            'id' => 69,
            'name' => 'Отчет KPI',
            'alias' => 'ReportKpi',
            'class_name' => 'app\models\pool\ReportKpi',
            'action' => 'report-kpi',
            'entity_type' => 'pool',
            'parent_id' => null,
            'is_logging' => false,
            'is_enabled' => true
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%report_kpi}}');
    }
}
