<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200415_064045_add_tor_enabled_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{prc_parsing_project}}', 'tor_enabled', $this->boolean()->notNull()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->dropColumn('{{prc_parsing_project}}', 'tor_enabled');
    }
}
