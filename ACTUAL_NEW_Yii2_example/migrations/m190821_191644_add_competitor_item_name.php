<?php

use app\components\Migration;

class m190821_191644_add_competitor_item_name extends Migration
{
    public function up()
    {
        $this->addColumn('{{%price_refined}}', 'competitor_item_name', $this->string(1024));
    }

    public function down()
    {
        $this->dropColumn('{{%price_refined}}', 'competitor_item_name');
    }
}
