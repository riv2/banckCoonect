<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m210203_113209_add_status_price_parsed extends Migration
{
    public $_statusesToAdd = [
        12 => 'Сбор номенклатуры: отправлен на API',
    ];

    public function safeUp()
    {
        foreach ($this->_statusesToAdd as $id => $name) {
            $this->insert('{{%price_parsed_status}}', [
                'id' => $id,
                'name' => $name,
            ]);
        }
    }

    public function safeDown()
    {
        foreach ($this->_statusesToAdd as $id => $name) {
            $this->delete('{{%price_parsed_status}}', ['id' => $id]);
        }
    }
}
