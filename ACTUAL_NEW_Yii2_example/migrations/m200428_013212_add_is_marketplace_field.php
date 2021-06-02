<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200428_013212_add_is_marketplace_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%competitor}}', 'is_marketplace', $this->boolean()->notNull()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%competitor}}', 'is_marketplace');
    }
}
