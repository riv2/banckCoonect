<?php

use app\components\Migration;

class m200422_143503_add_roles extends Migration
{
    const ROLE_ENTITY_ID = 85;

    public function safeUp()
    {
        $this->insert('{{%entity}}', [
            'id' => self::ROLE_ENTITY_ID,
            'name' => 'Роль пользователя',
            'alias' => 'Role',
            'class_name' => 'app\models\reference\Role',
            'action' => 'role',
            'entity_type' => 'reference',
            'parent_id' => null,
            'is_logging' => false,
            'is_enabled' => true
        ]);
        $this->db->createCommand()->resetSequence('{{%entity}}')->execute();

        Yii::$app->cache->delete('#prc_entity#');
    }

    public function safeDown()
    {
        $this->delete('{{%entity}}', [
            'id' => self::ROLE_ENTITY_ID,
        ]);
    }
}

