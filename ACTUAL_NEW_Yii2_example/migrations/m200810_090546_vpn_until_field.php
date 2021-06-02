<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200810_090546_vpn_until_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%vpn}}', 'until', $this->timestamp());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%vpn}}', 'until');
    }
}
