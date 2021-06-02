<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m201214_100059_project_chart extends Migration
{
    const ENTITY_ID = 94;

    public function safeUp(): void
    {
        $this->createTable('{{%project_chart}}', [
            'id' => $this->uuidpk(),
            'created_at' => $this->timestamp(),
            'date' => $this->timestamp(),
            'project_id' => $this->uuid(),
            'project_execution_id' => $this->uuid(),
            'type' => $this->integer(1),
            'data' => $this->text(),
        ]);
        $this->insert('{{%entity}}', [
            'id' => self::ENTITY_ID,
            'name' => 'График проекта',
            'alias' => 'ProjectChart',
            'class_name' => 'app\models\pool\ProjectChart',
            'action' => 'project-chart',
            'entity_type' => 'pool',
            'parent_id' => null,
            'is_logging' => false,
            'is_enabled' => true
        ]);
        $this->db->createCommand()->resetSequence('{{%entity}}')->execute();
        Yii::$app->cache->delete('#prc_entity#');
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%project_chart}}');
        $this->delete('{{%entity}}', ['id' => self::ENTITY_ID]);
        Yii::$app->cache->delete('#prc_entity#');
    }
}
