<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200328_094643_000_create_table_prc_log_kpi extends Migration
{
    public function up()
    {
        $this->createTable('{{%log_kpi}}', [
            'id'                => $this->uuidpk()->notNull(),
            'project_execution_id' => $this->uuid(),

            'item_id'           => $this->uuid(),
            'competitor_id'     => $this->uuid(),
            'project_id'        => $this->uuid(),

            'parsing_id'        => $this->uuid(),
            'parsing_project_id' => $this->uuid(),
            'price_refined_id'   => $this->uuid(),

            'created_at'        => $this->timestamp()->notNull(),
            'extracted_at'      => $this->dateTime(),
            'calculated_at'     => $this->dateTime(),

            'is_parsed'         => $this->boolean()->defaultValue(false),
            'http404'           => $this->boolean()->defaultValue(false),
            'out_of_stock'      => $this->boolean()->defaultValue(false),
            'is_used_in_calc'   => $this->boolean()->defaultValue(false),

            'price'             => $this->float(),
            'url'               => $this->text(),
            'status_id'         => $this->integer()->notNull()->defaultValue('0'),
        ], null);

        $this->addPk('{{%log_kpi}}', ['project_execution_id','id']);
        $this->addIndex('{{%log_kpi}}', ['project_execution_id', 'competitor_id']);
        $this->addIndex('{{%log_kpi}}', ['project_execution_id', 'competitor_id', 'item_id'], true);

        $this->insert('{{%entity}}', [
            'id' => 84,
            'name' => 'Расчет проекта',
            'alias' => 'LogKpi',
            'class_name' => 'app\models\pool\LogKpi',
            'action' => 'calculation',
            'entity_type' => 'pool',
            'parent_id' => null,
            'is_logging' => false,
            'is_enabled' => true
        ]);

    }

    public function down()
    {
        $this->delete('{{%entity}}', [
            'id' => 84,
        ]);

        $this->dropTable('{{%log_kpi}}');
    }
}
