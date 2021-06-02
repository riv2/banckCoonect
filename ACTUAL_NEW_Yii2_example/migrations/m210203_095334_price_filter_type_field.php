<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m210203_095334_price_filter_type_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%project}}', 'price_filter_type', $this->integer()->defaultValue(1));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%project}}', 'price_filter_type');
    }
}
