<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200923_084124_proxy_public extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%proxy}}', 'is_public', $this->boolean()->defaultValue(true));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%proxy}}', 'is_public');
    }
}
