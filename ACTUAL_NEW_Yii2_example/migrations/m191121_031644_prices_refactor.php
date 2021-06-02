<?php

use app\components\Migration;

class m191121_031644_prices_refactor extends Migration
{
    public function up()
    {

        $this->dropColumn('{{%price_refined}}','group_hash');
        $this->dropColumn('{{%price_refined}}','index');
        $this->dropColumn('{{%price_refined}}','screenshot');
        $this->dropColumn('{{%price_refined}}','robot_id');
        $this->dropColumn('{{%price_refined}}','competitor_shop_index');
        $this->dropColumn('{{%price_refined}}','region_id');
        $this->db
            ->createCommand("drop sequence if exists prc_price_refinedq_index_seq;")
            ->execute();
        $this->db
            ->createCommand("drop sequence if exists prc_price_refined_index_seq;")
            ->execute();


        $this->dropColumn('{{%price_parsed}}','region_id');
        $this->dropColumn('{{%price_parsed}}','stock_reverse');
        $this->dropColumn('{{%price_parsed}}','screenshot');
        $this->dropColumn('{{%price_parsed}}','robot_id');
        $this->dropColumn('{{%price_parsed}}','competitor_shop_index');


        $this->dropColumn('{{%log_price_calculation}}','competitor_shop_index');
        $this->dropColumn('{{%log_price_calculation}}','screenshot');
        $this->dropColumn('{{%log_price_calculation}}','region_id');

    }

    public function down()
    {
        $this->addColumn('{{%price_refined}}','competitor_shop_index', $this->string());
        $this->addColumn('{{%price_refined}}','group_hash', $this->string());
        $this->addColumn('{{%price_refined}}','index', $this->tinyInteger());
        $this->addColumn('{{%price_refined}}','screenshot', $this->string());
        $this->addColumn('{{%price_refined}}','robot_id', $this->string());
        $this->addColumn('{{%price_refined}}','region_id', $this->integer());


        $this->addColumn('{{%price_parsed}}','region_id', $this->integer());
        $this->addColumn('{{%price_parsed}}','stock_reverse', $this->boolean());
        $this->addColumn('{{%price_parsed}}','screenshot', $this->string());
        $this->addColumn('{{%price_parsed}}','robot_id', $this->string());
        $this->addColumn('{{%price_parsed}}','competitor_shop_index', $this->string());


        $this->addColumn('{{%log_price_calculation}}','competitor_shop_index', $this->string());
        $this->addColumn('{{%log_price_calculation}}','screenshot', $this->string());
        $this->addColumn('{{%log_price_calculation}}','region_id', $this->string());

    }
}
