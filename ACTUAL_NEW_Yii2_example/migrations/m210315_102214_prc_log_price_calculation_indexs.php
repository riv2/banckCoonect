<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m210315_102214_prc_log_price_calculation_indexs extends Migration
{
    public function safeUp()
    {
		$this->db->createCommand("CREATE INDEX ci_prc_log_price_calculation_item_id ON {{%log_price_calculation}} (item_id);")
            ->execute();
    }

    public function safeDown()
    {
		$this->db->createCommand("DROP INDEX ci_prc_log_price_calculation_item_id;")
            ->execute();
    }
}
