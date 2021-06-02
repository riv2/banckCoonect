<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m201228_075519_competitor_price_lifetime extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%competitor}}', 'price_lifetime', $this->bigInteger()->notNull()->defaultValue('86400'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%competitor}}', 'price_lifetime');
    }
}
