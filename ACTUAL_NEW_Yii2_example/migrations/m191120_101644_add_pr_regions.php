<?php

use app\components\Migration;

class m191120_101644_add_pr_regions extends Migration
{
    public function up()
    {
        $this->addColumn('{{%price_refined}}','regions', $this->json()->defaultValue(new \yii\db\Expression("'[]'::jsonb")));
    }

    public function down()
    {
        $this->dropColumn('{{%price_refined}}','regions');
    }
}
