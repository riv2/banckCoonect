<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200328_092921_add_is404 extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%parsing}}', 'regions', $this->text());
        $this->dropColumn('{{%parsing}}', 'proxies');
        $this->addColumn('{{%price_parsed}}', 'http404', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('{{%price_refined}}', 'http404', $this->boolean()->notNull()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->addColumn('{{%parsing}}', 'proxies', $this->json()->defaultValue(new \yii\db\Expression("'[]'::jsonb")));
        $this->dropColumn('{{%price_parsed}}', 'http404');
        $this->dropColumn('{{%price_refined}}', 'http404');
    }
}
