<?php

use app\components\Migration;

class m200720_155300_alter_sequence extends Migration
{
    private function _changeColumnsType($type)
    {
        $this->db->createCommand('ALTER TABLE prc_log_price_calculation ALTER COLUMN index TYPE ' . $type)->execute();
        $this->db->createCommand('ALTER SEQUENCE prc_log_price_calculation_index_seq AS ' . $type)->execute();
    }

    public function safeUp()
    {
        $this->_changeColumnsType('BIGINT');
    }

    public function safeDown()
    {
        $this->_changeColumnsType('INTEGER');
    }
}
