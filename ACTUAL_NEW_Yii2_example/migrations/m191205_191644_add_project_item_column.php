<?php

use app\components\Migration;

class m191205_191644_add_project_item_column extends Migration
{
    public function up()
    {
        $this->addColumn('{{%project_item}}','price_variation_modifier', $this->float());
    }

    public function down()
    {
        $this->dropColumn('{{%project_item}}','price_variation_modifier');
    }
}
