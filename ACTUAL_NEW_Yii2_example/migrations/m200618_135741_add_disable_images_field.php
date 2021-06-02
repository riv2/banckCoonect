<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200618_135741_add_disable_images_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%parsing_project}}', 'disable_images', $this->boolean()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%parsing_project}}', 'disable_images');
    }
}
