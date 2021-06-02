<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m210126_121440_log_price_calculation_seller_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn(
            '{{%log_price_calculation}}',
            'competitor_item_seller',
            $this->string(64)->notNull()->defaultValue('default')
        );
    }

    public function safeDown()
    {
        $this->dropColumn(
            '{{%log_price_calculation}}',
            'competitor_item_seller'
        );
    }
}
