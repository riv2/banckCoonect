<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200311_092921_add_parsing_project_fields extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%parsing_project}}', 'url_replace_from', $this->string());
        $this->addColumn('{{%parsing_project}}', 'url_replace_to', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%parsing_project}}', 'url_replace_from');
        $this->dropColumn('{{%parsing_project}}', 'url_replace_to');
    }
}
