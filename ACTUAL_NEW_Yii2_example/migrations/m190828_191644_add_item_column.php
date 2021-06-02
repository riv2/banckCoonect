<?php

use app\components\Migration;

class m190828_191644_add_item_column extends Migration
{
    public function up()
    {
        $this->addColumn('{{%item}}','sales_rank', $this->tinyInteger()->defaultValue(1000)->notNull());
        $this->addIndex('{{%item}}','sales_rank');
    }

    public function down()
    {
        $this->delIndex('{{%item}}','sales_rank');
        $this->dropColumn('{{%item}}','sales_rank');
    }
}
