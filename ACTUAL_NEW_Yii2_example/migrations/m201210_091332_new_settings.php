<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m201210_091332_new_settings extends Migration
{
    private $_settingsToUpdate = [
        'vpn_error_limit' => ['Максимальное число ошибок для смены VPN', 5],
    ];
    private $_settingsToInsert = [
        'price_variance_up' => ['Максимальное отклонение цены вверх (в процентах)', 200],
        'price_variance_down' => ['Максимальное отклонение цены вниз (в процентах)', 50],
    ];
    const SETTING_ENTITY_ID = 93;

    public function safeUp()
    {
        $this->addPk('{{%setting}}', ['id']);
        $this->addColumn('{{%setting}}', 'full_name', $this->string(256));
        $this->insert('{{%entity}}', [
            'id' => self::SETTING_ENTITY_ID,
            'name' => 'Настройка',
            'alias' => 'Setting',
            'class_name' => 'app\models\reference\Setting',
            'action' => 'setting',
            'entity_type' => 'reference',
            'parent_id' => null,
            'is_logging' => false,
            'is_enabled' => true
        ]);
        $this->db->createCommand()->resetSequence('{{%entity}}')->execute();
        Yii::$app->cache->delete('#prc_entity#');

        foreach ($this->_settingsToUpdate as $name => $data) {
            $this->update('{{%setting}}', [
                'full_name' => $data[0],
                'data' => $data[1],
            ], [
                'name' => $name,
            ]);
        }
        foreach ($this->_settingsToInsert as $name => $data) {
            $this->insert('{{%setting}}', [
                'name' => $name,
                'full_name' => $data[0],
                'data' => $data[1],
                'created_at' => new \yii\db\Expression('NOW()'),
                'updated_at' => new \yii\db\Expression('NOW()'),
            ]);
        }
    }

    public function safeDown()
    {
        foreach ($this->_settingsToInsert as $name => $data) {
            $this->delete('{{%setting}}', [
                'name' => $name,
            ]);
        }
        $this->delete('{{%entity}}', ['id' => self::SETTING_ENTITY_ID]);
        Yii::$app->cache->delete('#prc_entity#');
        $this->dropColumn('{{%setting}}', 'full_name');
    }
}
