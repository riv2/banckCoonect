<?php

use app\components\Migration;
use app\components\Schema;

/**
 * @noinspection PhpUnused
 */
class m210315_085600_alter_index_types extends Migration
{
    private $_excludedSequences = [
        'prc_log_price_calculation_index_seq'
    ];

    private function _changeTypeForSequences($newType)
    {
        $allSequences = $this->db->createCommand("SELECT relname FROM pg_class WHERE relkind = 'S'")
            ->queryColumn();
        $sequences = array_diff($allSequences, $this->_excludedSequences);
        foreach ($sequences as $sequence) {
            $this->db->createCommand("ALTER SEQUENCE " . $sequence . " AS " . $newType)->execute();
        }
    }

    public function safeUp()
    {
        $this->_changeTypeForSequences(Schema::TYPE_BIGINT);
    }

    public function safeDown()
    {
        $this->_changeTypeForSequences(Schema::TYPE_INTEGER);
    }
}
