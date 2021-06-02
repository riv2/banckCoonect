<?php

use app\components\Migration;
use app\components\Schema;

/**
 * @noinspection PhpUnused
 */
class m210315_102600_alter_index_types extends Migration
{
    private function _changeTypeForSequences($newType)
    {
        $allColumns = $this->db->createCommand("
            SELECT
                table_name,
                column_name
            FROM information_schema.columns
            WHERE
                table_schema = 'public' AND
                table_name LIKE 'prc_%' AND
                column_default LIKE 'nextval%'
            ORDER BY table_name
        ")->queryAll();
        foreach ($allColumns as $columnData) {
            $this->alterColumn($columnData['table_name'], $columnData['column_name'], $newType);
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
