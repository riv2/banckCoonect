<?php

use app\components\Migration;
use app\models\reference\Item;
use app\models\register\HoradricCube;

class m190828_191644_add_sales_rank_column extends Migration
{
    public function up()
    {
        $this->addColumn('{{%horadric_cube}}','sales_rank', $this->tinyInteger()->defaultValue(1000)->notNull());

        foreach (HoradricCube::find()->each(1000) as $hc) {
            /** @var HoradricCube $hc */
            $rank =  Item::find()->andWhere(['id' => $hc->item_id])->select('sales_rank')->scalar();
            if ($rank) {
                $hc->sales_rank = $rank;
                $hc->save();
            }
        }
        $this->addIndex('{{%horadric_cube}}','sales_rank');
    }

    public function down()
    {
        $this->delIndex('{{%horadric_cube}}','sales_rank');
        $this->dropColumn('{{%horadric_cube}}','sales_rank');
    }
}
